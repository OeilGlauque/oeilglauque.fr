<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Game;
use App\Entity\GameSlot;
use App\Entity\Edition;
use App\Form\GameType;
use App\Form\GameEditType;
use App\Service\GlauqueMarkdownParser;

class GameController extends FOGController {
    
    /**
     * @Route("/nouvellePartie", name="nouvellePartie")
     */
    public function newGame(Request $request, \Swift_Mailer $mailer) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $game = new Game();
        $form = $this->createForm(GameType::class, $game, array('slots' => $this->getCurrentEdition()->getGameSlots()));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $game->setAuthor($user);

            // Check if the slot belongs to the current edition
            if($game->getGameSlot()->getEdition() != $this->getCurrentEdition()) {
                $this->addFlash('danger', "Vous ne pouvez proposer une partie que pour l'édition actuelle !");
                return $this->redirectToRoute('nouvellePartie');
            }

            // Check if the user has no other game on the same slot
            $otherGames = $user->getPartiesJouees();
            foreach ($otherGames as $g) {
                if($g->getGameSlot() == $game->getGameSlot()) {
                    $this->addFlash('danger', "Vous avez déjà la partie ".$g->getTitle()." prévue sur cet horaire !");
                    return $this->redirectToRoute('nouvellePartie');
                }
            }
            // Check if the user has not proposed an other game on this slot
            $proposedGames = $user->getPartiesOrganisees();
            foreach ($proposedGames as $g) {
                if($g->getGameSlot() == $game->getGameSlot()) {
                    $this->addFlash('danger', "Vous êtes déjà Maître du Jeu de la partie ".$g->getTitle()." sur cet horaire !");
                    return $this->redirectToRoute('nouvellePartie');
                }
            }

            //Check if the slot is not full
            $slot = $game->getGameSlot();
            if(count($slot->getGames()) >= $slot->getMaxGames()) {
                $this->addFlash('danger', "Malheureusement, il n'y a plus de place disponible sur le créneau ".$slot->getText()."... Essayez un autre créneau.");
                return $this->redirectToRoute('nouvellePartie');
            }

            //Remove any undesired html tags
            $game->setDescription(strip_tags($game->getDescription()));

            // Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();

            $message = (new \Swift_Message('Demande de validation de partie'))
                ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
                ->setTo([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
                ->setBody(
                    $this->renderView(
                        'oeilglauque/emails/game/gameValidationRequest.html.twig',
                        ['user' => $user,
                        'game' => $game]
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $this->addFlash('info', "Votre partie a bien été enregistrée ! Elle va être validée par notre équipe avant d'être mise en ligne. Merci pour votre investissement auprès du Festival !");

            return $this->redirectToRoute('listeParties');
        }

        return $this->renderPage('oeilglauque/newGame.html.twig', array(
            'form' => $form->createView(), 
            'edit' => false
        ));
    }

    /**
     * @Route("/parties/slots")
     */
    public function listGameSlots() {
        $res = array();
        $slots = $this->getCurrentEdition()->getGameSlots();
        foreach ($slots as $s) {
            array_push($res, array('id' => $s->getId(), 'text' => $s->getText()));
        }
        return $this->json($res);
    }

    /**
     * @Route("/parties", name="listeParties")
     */
    public function listGames() {
        if ($this->getCurrentEdition()->getId() != null) {
            $games = $this->getDoctrine()->getRepository(Game::class)->getOrderedGameList($this->getCurrentEdition(), true);
            $gameSlots = $this->getDoctrine()->getRepository(GameSlot::class)->findBy(["edition" => $this->getCurrentEdition()]);
            
            $user = $this->getUser();
            $userGames = ($user != null) ? $this->getUser()->getPartiesJouees()->toArray() : array();
            $userGames = array_filter($userGames, function($element) {
                return $element->getGameSlot()->getEdition() == $this->getCurrentEdition();
            });
            $userProposedGames = ($user != null) ? $this->getUser()->getPartiesOrganisees()->toArray() : array();
            $userProposedGames = array_filter($userProposedGames, function($element) {
                return $element->getGameSlot()->getEdition() == $this->getCurrentEdition();
            });
    
            foreach ($games as $g) {
                $g->setDescription(
                    GlauqueMarkdownParser::safeTruncateHtml(
                        GlauqueMarkdownParser::parse($g->getDescription()), 500
                    )
                );
            }
    
            return $this->renderPage('oeilglauque/gamesList.html.twig', array(
                'games' => $games,
                'gameSlots' => $gameSlots,
                'userGames' => $userGames, 
                'hasRegistered' => count($userGames) > 0, 
                'userProposedGames' => $userProposedGames, 
                'isMJ' => count($userProposedGames) > 0, 
            ));
        }
        $this->addFlash('danger', "Il n'y a pas d'édition du FOG prévu pour le moment.");
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/partie/edit/{id}", name="editGame")
     */
    public function editGame(Request $request, $id) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $game = $this->getDoctrine()->getRepository(Game::class)->find($id);

        // Check we are editing our own game, or we are an admin
        if($game->getAuthor() != $user && ! $user->hasRole('ROLE_ADMIN')) {
            $this->addFlash('danger', "Vous ne pouvez pas éditer la partie d'un autre utilisateur...");
            return $this->redirectToRoute('showGame', ["id" => $id]);
        }

        $form = $this->createForm(GameEditType::class, $game, array('slots' => $this->getCurrentEdition()->getGameSlots(), 'seats' => $game->getBookedSeats()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($user == $game->getAuthor()) {   // Let admins edit games no matter what

                // Check if the slot belongs to the current edition
                if($game->getGameSlot()->getEdition() != $this->getCurrentEdition()) {
                    $this->addFlash('danger', "Vous ne pouvez proposer une partie que pour l'édition actuelle !");
                    return $this->redirectToRoute('editGame', ["id" => $id]);
                }

                // Check if the user has no other game on the same slot
                $otherGames = $user->getPartiesJouees();
                foreach ($otherGames as $g) {
                    if($g->getGameSlot() == $game->getGameSlot()) {
                        $this->addFlash('danger', "Vous avez déjà la partie ".$g->getTitle()." prévue sur cet horaire !");
                        return $this->redirectToRoute('editGame', ["id" => $id]);
                    }
                }
                // Check if the user has not proposed an other game on this slot
                $proposedGames = $user->getPartiesOrganisees();
                foreach ($proposedGames as $g) {
                    if($g->getGameSlot() == $game->getGameSlot() && $g->getId() != $id) {
                        $this->addFlash('danger', "Vous êtes déjà Maître du Jeu de la partie ".$g->getTitle()." sur cet horaire !");
                        return $this->redirectToRoute('editGame', ["id" => $id]);
                    }
                }
            }

            // Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();
            $this->addFlash('success', "Votre partie a bien été mise à jour !");

            return $this->redirectToRoute('showGame', ["id" => $id]);
        }

        return $this->renderPage('oeilglauque/newGame.html.twig', array(
            'form' => $form->createView(), 
            'edit' => true
        ));
    }

    /**
     * @Route("/partie/{id}", name="showGame")
     */
    public function showGame($id) {
        $game = $this->getDoctrine()->getRepository(Game::class)->find($id);
        if($game) {
            $user = $this->getUser();
            // Only the author can review a game that has not yet been validated
            if(!$game->getValidated() && ($user == null || ($user != null && $game->getAuthor() != $user))) {
                $this->addFlash('danger', "Cette partie n'a pas encore été validée par notre équipe. ");
                return $this->redirectToRoute('listeParties');
            }

            $game->setDescription(GlauqueMarkdownParser::parse($game->getDescription()));

            return $this->renderPage('oeilglauque/showGame.html.twig', array(
                'game' => $game, 
                'registered' => $game->getPlayers()->contains($this->getUser()), 
            ));
        }else{
            throw $this->createNotFoundException('Impossible de trouver la partie demandée. ');
        }
    }

    /**
     * @Route("/partie/register/{id}", name="registerGame")
     */
    public function registerGame($id) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $game = $this->getDoctrine()->getRepository(Game::class)->find($id);
        if($game) {
            if(!$game->getLocked()) { // Check if game is locked
                // Check if the game is validated
                if(!$game->getValidated()) {
                    $this->addFlash('danger', "Vous ne pouvez pas vous inscrire à une partie non validée !");
                    return $this->redirectToRoute('showGame', ["id" => $id]);
                }
                // Check if the game belongs to the current edition
                if($game->getGameSlot()->getEdition() != $this->getCurrentEdition()) {
                    $this->addFlash('danger', "Vous ne pouvez vous inscrire qu'aux parties de l'édition actuelle !");
                    return $this->redirectToRoute('showGame', ["id" => $id]);
                }
                $user = $this->getUser();
                // Check if the user has no other game on the same slot
                $otherGames = $user->getPartiesJouees();
                foreach ($otherGames as $g) {
                    if($g->getGameSlot() == $game->getGameSlot()) {
                        $this->addFlash('danger', "Vous avez déjà la partie ".$g->getTitle()." prévue sur cet horaire !");
                        return $this->redirectToRoute('showGame', ["id" => $id]);
                    }
                }
                // Check if the user has not proposed an other game on this spot
                $proposedGames = $user->getPartiesOrganisees();
                foreach ($proposedGames as $g) {
                    if($g->getGameSlot() == $game->getGameSlot()) {
                        $this->addFlash('danger', "Vous êtes déjà Maître du Jeu de la partie ".$g->getTitle()." sur cet horaire !");
                        return $this->redirectToRoute('showGame', ["id" => $id]);
                    }
                }
                if($game->getFreeSeats() > 0) {
                    $game->addPlayer($user); // Handles 'contains' verification
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($game);
                    $entityManager->flush();
                    $this->addFlash('success', "Vous avez bien été inscrit à la partie ".$game->getTitle());
                }else {
                    $this->addFlash('danger', "Malheureusement il n'y a plus de place disponible en ligne pour la partie ".$game->getTitle()."... ");
                }
            }
            return $this->redirectToRoute('showGame', ["id" => $id]);
        }else{
            throw $this->createNotFoundException('Impossible de trouver la partie demandée. ');
        }
    }


    /**
     * @Route("/partie/unregister/{id}", name="unregisterGame")
     */
    public function unregisterGame($id) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $game = $this->getDoctrine()->getRepository(Game::class)->find($id);
        if($game) {
            $user = $this->getUser();
            $game->removePlayer($user); // Handles 'contains' verification
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();
            $this->addFlash('info', "Vous avez bien été désinscrit de la partie ".$game->getTitle());
            return $this->redirectToRoute('showGame', ["id" => $id]);
        }else{
            throw $this->createNotFoundException('Impossible de trouver la partie demandée. ');
        }
    }
}

?>
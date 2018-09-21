<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Game;
use App\Entity\Edition;
use App\Form\GameType;
use App\Form\GameEditType;
use App\Service\GlauqueMarkdownParser;

class GameController extends CustomController {
    
    /**
     * @Route("/nouvellePartie", name="nouvellePartie")
     */
    public function newGame(Request $request) {
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
                $this->addFlash('danger', "Malheureusement, il n'y a plus de place disponnible sur le créneau ".$slot->getText()."... ");
                return $this->redirectToRoute('nouvellePartie');
            }

            // Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();
            $this->addFlash('info', "Votre partie a bien été enregistrée ! Elle va être validée par notre équipe avant d'être mise en ligne. Merci pour votre investissement auprès du Festival !");

            return $this->redirectToRoute('listeParties');
        }

        return $this->render('oeilglauque/newGame.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
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
        $games = $this->getDoctrine()->getRepository(Game::class)->getOrderedGameList($this->getCurrentEdition(), true);
        
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
            $g->setDescription(GlauqueMarkdownParser::parse($g->getDescription()));
        }

        return $this->render('oeilglauque/gamesList.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
            'games' => $games, 
            'userGames' => $userGames, 
            'hasRegistered' => count($userGames) > 0, 
            'userProposedGames' => $userProposedGames, 
            'isMJ' => count($userProposedGames) > 0, 
        ));
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

        $form = $this->createForm(GameEditType::class, $game, array('slots' => $this->getCurrentEdition()->getGameSlots()));
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

        return $this->render('oeilglauque/newGame.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
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

            return $this->render('oeilglauque/showGame.html.twig', array(
                'dates' => $this->getCurrentEdition()->getDates(), 
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
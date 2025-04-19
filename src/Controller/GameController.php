<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game;
use App\Entity\GameSlot;
use App\Form\GameType;
use App\Form\GameEditType;
use App\Repository\GameRepository;
use App\Service\FileUploader;
use App\Service\FOGDiscordWebhookService;
use App\Service\FOGGmail;
use App\Service\GlauqueMarkdownParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class GameController extends FOGController {
    
    #[Route("/nouvellePartie", name: "nouvellePartie")]
    public function newGame(Request $request, FOGGmail $mailer, EntityManagerInterface $entityManager, FileUploader $uploader, FOGDiscordWebhookService $discord): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $game = new Game();
        $form = $this->createForm(GameType::class, $game, ['slots' => $this->FogParams->getCurrentEdition()->getGameSlots()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\User */
            $user = $this->getUser();
            $game->setAuthor($user);

            // Check if there is a slot
            if(! $game->getGameSlot()) {
                $this->addFlash('danger', "Vous devez sélectionner un horaire pour votre partie !");
                return $this->redirectToRoute('nouvellePartie');
            }

            // Check if the slot belongs to the current edition
            if($game->getGameSlot()->getEdition() != $this->FogParams->getCurrentEdition()) {
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

            $file = $form->get('img')->getData();
            if ($file) {
                $filename = $uploader->upload($file, "games");
                if ($filename != "") {
                    $game->setImage($filename);
                } else {
                    $this->addFlash('danger', "Erreur lors de l'upload de l'image.");
                    return $this->redirectToRoute('nouvellePartie');
                }
            }

            // Sauvegarde en base
            $entityManager->persist($game);
            $entityManager->flush();

            $mailer->sendTemplatedEmail(
                $mailer->getMailFOG(),
                "Demande de validation de partie",
                "oeilglauque/emails/game/gameValidationRequest.html.twig",
                ['user' => $user, 'game' => $game]
            );

            $discord->send("Nouvelle partie disponible pour validation (https://oeilglauque.fr/admin/games/validate)");

            $this->addFlash('info', "Votre partie a bien été enregistrée ! Elle va être validée par notre équipe avant d'être mise en ligne. Merci pour votre investissement auprès du Festival !");

            return $this->redirectToRoute('listeParties');
        }

        return $this->render('oeilglauque/newGame.html.twig', [
            'form' => $form, 
            'edit' => false,
            'newHeader' => true
        ]);
    }

    #[Route("/parties/slots")]
    public function listGameSlots() {
        $res = [];
        $slots = $this->FogParams->getCurrentEdition()->getGameSlots();
        foreach ($slots as $s) {
            array_push($res, ['id' => $s->getId(), 'text' => $s->getText()]);
        }
        return $this->json($res);
    }

    #[Route("/parties", name: "listeParties")]
    public function listGames(EntityManagerInterface $manager): Response
    {
        if ($this->FogParams->getCurrentEdition()->getId() != null) {
            $games = $manager->getRepository(Game::class)->getOrderedGameList($this->FogParams->getCurrentEdition(), true);
            $gameSlots = $manager->getRepository(GameSlot::class)->findBy(["edition" => $this->FogParams->getCurrentEdition()]);
            
            /** @var \App\Entity\User */
            $user = $this->getUser();
            $userGames = ($user != null) ? $user->getPartiesJouees()->toArray() : [];
            $userGames = array_filter($userGames, function($element) {
                return $element->getGameSlot()->getEdition() == $this->FogParams->getCurrentEdition();
            });

            $userProposedGames = ($user != null) ? $user->getPartiesOrganisees()->toArray() : [];
            $userProposedGames = array_filter($userProposedGames, function($element) {
                return $element->getGameSlot()->getEdition() == $this->FogParams->getCurrentEdition();
            });
    
            foreach ($games as $g) {
                $g->setDescription(
                    GlauqueMarkdownParser::safeTruncateHtml(
                        GlauqueMarkdownParser::parse($g->getDescription()), 500
                    )
                );
            }
    
            return $this->render('oeilglauque/gamesList.html.twig', [
                'gameSlots' => $gameSlots,
                'userGames' => $userGames, 
                'hasRegistered' => count($userGames) > 0, 
                'userProposedGames' => $userProposedGames, 
                'isMJ' => count($userProposedGames) > 0,
                'edition' => $this->FogParams->getCurrentEdition(),
                'hasGames' => count($games) > 0,
                'newHeader' => true
            ]);
        }
        $this->addFlash('danger', "Il n'y a pas d'édition du FOG prévu pour le moment.");
        return $this->redirectToRoute('index');
    }

    #[Route("/partie/edit/{id}", name: "editGame")]
    public function editGame(Request $request, Game $game, EntityManagerInterface $manager, FileUploader $uploader): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User */
        $user = $this->getUser();
        $id = $game->getId();

        // Check we are editing our own game, or we are an admin
        if($game->getAuthor() != $user && ! $user->hasRole('ROLE_ADMIN')) {
            $this->addFlash('danger', "Vous ne pouvez pas éditer la partie d'un autre utilisateur...");
            return $this->redirectToRoute('showGame', ["id" => $id]);
        }

        $form = $this->createForm(GameEditType::class, $game, ['slots' => $this->FogParams->getCurrentEdition()->getGameSlots(), 'seats' => $game->getBookedSeats()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($user == $game->getAuthor()) {   // Let admins edit games no matter what

                // Check if the slot belongs to the current edition
                if($game->getGameSlot()->getEdition() != $this->FogParams->getCurrentEdition()) {
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

                $file = $form->get('img')->getData();
                if ($file) {
                    $filename = $uploader->upload($file, "games");
                    if ($filename != "") {
                        $filesystem = new Filesystem();

                        // remove previous image if exist
                        if ($game->getImage() != null) {
                            $filesystem->remove($game->getImage());
                        }
                        
                        $game->setImage($filename);
                    } else {
                        $this->addFlash('danger', "Erreur lors de l'upload de l'image.");
                        return $this->redirectToRoute('nouvellePartie');
                    }
                }
            }

            // Sauvegarde en base
            $manager->flush();
            $this->addFlash('success', "Votre partie a bien été mise à jour !");

            return $this->redirectToRoute('showGame', ["id" => $id]);
        }

        return $this->renderForm('oeilglauque/newGame.html.twig', [
            'form' => $form, 
            'edit' => true
        ]);
    }

    #[Route("/partie/{id}", name: "showGame")]
    public function showGame(int $id, GameRepository $gameRepository): Response
    {
        $game = $gameRepository->find($id);
        if($game) {
            /** @var \App\Entity\User */
            $user = $this->getUser();
            // Only the author can review a game that has not yet been validated
            if(!$game->getValidated() && ($user == null || ($user != null && $game->getAuthor() != $user))) {
                $this->addFlash('danger', "Cette partie n'a pas encore été validée par notre équipe.");
                return $this->redirectToRoute('listeParties');
            }

            $game->setDescription(GlauqueMarkdownParser::parse($game->getDescription()));

            return $this->render('oeilglauque/showGame.html.twig', [
                'game' => $game, 
                'registered' => $game->getPlayers()->contains($this->getUser()),
                'newHeader' => true
            ]);
        }else{
            throw $this->createNotFoundException('Impossible de trouver la partie demandée. ');
        }
    }

    #[Route("/partie/register/{id}", name: "registerGame")]
    public function registerGame(Game $game, EntityManagerInterface $manager) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($game) {
            $id = $game->getId();

            if(!$game->getLocked()) { // Check if game is locked
                // Check if the game is validated
                if(!$game->getValidated()) {
                    $this->addFlash('danger', "Vous ne pouvez pas vous inscrire à une partie non validée !");
                    return $this->redirectToRoute('showGame', ["id" => $id]);
                }
                // Check if the game belongs to the current edition
                if($game->getGameSlot()->getEdition() != $this->FogParams->getCurrentEdition()) {
                    $this->addFlash('danger', "Vous ne pouvez vous inscrire qu'aux parties de l'édition actuelle !");
                    return $this->redirectToRoute('showGame', ["id" => $id]);
                }

                /** @var \App\Entity\User */
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
                    $manager->flush();
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


    #[Route("/partie/unregister/{id}", name: "unregisterGame")]
    public function unregisterGame(Game $game, EntityManagerInterface $manager) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($game) {
            $user = $this->getUser();
            $game->removePlayer($user); // Handles 'contains' verification
            $manager->flush();
            $this->addFlash('info', "Vous avez bien été désinscrit de la partie ".$game->getTitle());
            return $this->redirectToRoute('showGame', ["id" => $game->getId()]);
        }else{
            throw $this->createNotFoundException('Impossible de trouver la partie demandée. ');
        }
    }
}
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Game;
use App\Entity\Edition;
use App\Form\GameType;

class GameController extends Controller {
    
    /**
     * @Route("/nouvellePartie", name="nouvellePartie")
     */
    public function newGame(Request $request) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $game = new Game();
        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $game->setAuthor($this->getUser());

            // Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('listeParties');
        }

        return $this->render('oeilglauque/newGame.html.twig', array(
            'dates' => "Du 19 au 21 octobre", 
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/parties", name="listeParties")
     */
    public function listGames() {
        $games = $this->getDoctrine()->getRepository(Game::class)->findBy(["validated" => true]);
        
        return $this->render('oeilglauque/gamesList.html.twig', array(
            'dates' => "Du 19 au 21 octobre", 
            'games' => $games
        ));
    }

    /**
     * @Route("/partie/{id}", name="showGame")
     */
    public function showGame($id) {
        $game = $this->getDoctrine()->getRepository(Game::class)->find($id);
        if($game) {
            return $this->render('oeilglauque/showGame.html.twig', array(
                'dates' => "Du 19 au 21 octobre", 
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
            $user = $this->getUser();
            // Check if the user has no other game on the same slot
            $otherGames = $user->getPartiesJouees();
            foreach ($otherGames as $g) {
                if($g->getGameSlot() == $game->getGameSlot()) {
                    $this->addFlash('error', "Vous avez déjà la partie ".$g->getTitle()." prévue sur cet horaire !");
                    return $this->redirectToRoute('showGame', ["id" => $id]);
                }
            }
            $proposedGames = $user->getPartiesOrganisees();
            foreach ($proposedGames as $g) {
                if($g->getGameSlot() == $game->getGameSlot()) {
                    $this->addFlash('error', "Vous êtes déjà Maître du Jeu de la partie ".$g->getTitle()." sur cet horaire !");
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
                $this->addFlash('error', "Malheureusement il n'y a plus de place disponible en ligne pour la partie ".$game->getTitle()."... ");
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
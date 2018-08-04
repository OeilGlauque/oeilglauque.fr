<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Game;
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
            $game->setAuthor($user = $this->getUser());

            // Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('oeilglauque/newGame.html.twig', array(
            'dates' => "Du 10 au 31 octobre", 
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/parties", name="listeParties")
     */
    public function listGames() {
        $games = $this->getDoctrine()->getRepository(Game::class)->findAll();
        /*$gamesArray = array();

        foreach($games as $n) {
            array_push($gamesArray, $n);
        }*/
        
        return $this->render('oeilglauque/gamesList.html.twig', array(
            'dates' => "Du 10 au 31 octobre", 
            'games' => $games
        ));
    }
}

?>
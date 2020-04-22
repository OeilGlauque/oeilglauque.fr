<?php

namespace App\Controller;

use App\Entity\LocalReservation;
use App\Form\BoardGameReservationType;
use App\Form\LocalReservationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Dotenv\Dotenv;

class BoardGameReservationController extends CustomController
{
    /**
     * @Route("/reservations/boardGame", name="boardGameReservation")
     */
    public function boardGameReservation(Request $request)
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $reservation = new LocalReservation();
        $form = $this->createForm(BoardGameReservationType::class, $reservation, array());
        $repository = $this->getDoctrine()
            ->getRepository('App:BoardGame');
        $boardGames = $repository->findAll();

        /*$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $reservation->setAuthor($user);

            // Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->sendmail($reservation, $this->get('swiftmailer.mailer.default'));

            $this->addFlash('info', "Votre réservation a bien été enregistrée, vous recevrez une confirmation par e-mail dès qu'elle sera acceptée.");

            return $this->redirectToRoute('index');
        }*/

        return $this->render('oeilglauque/boardGameReservation.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(),
            'form' => $form->createView(),
            'boardGames' => $boardGames
        ));
    }
}
?>
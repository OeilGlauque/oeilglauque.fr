<?php

namespace App\Controller;

use App\Entity\LocalReservation;
use App\Form\LocalReservationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends CustomController
{
    /**
     * @Route("/reservations", name="reservations")
     */
    public function reservations(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $reservation = new LocalReservation();
        $form = $this->createForm(LocalReservationType::class, $reservation, array());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //TODO: check up if everything is OK

            $user = $this->getUser();
            $reservation->setAuthor($user);

            // Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();
            $this->addFlash('info', "Votre réservation a bien été enregistrée, vous recevrez une confirmation par e-mail dès qu'elle sera acceptée.");

            return $this->redirectToRoute('index');
        }


        return $this->render('oeilglauque/reservations.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(),
            'form' => $form->createView()
        ));
    }
}
?>
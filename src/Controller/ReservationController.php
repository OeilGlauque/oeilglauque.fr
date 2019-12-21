<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\LocalReservation;
use App\Form\ReservationType;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends CustomController
{
    /**
     * @Route("/reservations", name="reservations")
     */
    public function reservations()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $reservation = new LocalReservation();
        $form = $this->createForm(ReservationType::class, $reservation, array());

        return $this->render('oeilglauque/reservations.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(),
            'form' => $form->createView()
        ));
    }
}
?>
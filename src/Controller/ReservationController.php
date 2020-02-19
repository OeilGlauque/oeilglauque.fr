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
            $user = $this->getUser();
            $reservation->setAuthor($user);

            // Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->sendmail($reservation, $this->get('swiftmailer.mailer.default'));

            $this->addFlash('info', "Votre réservation a bien été enregistrée, vous recevrez une confirmation par e-mail dès qu'elle sera acceptée.");

            return $this->redirectToRoute('index');
        }

        return $this->render('oeilglauque/reservations.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(),
            'form' => $form->createView()
        ));
    }

    private function sendmail(LocalReservation $reservation, \Swift_Mailer $mailer) {
        $message = (new \Swift_Message('Nouvelle demande de réservation du local FOG'))
            ->setFrom(['oeilglauque@gmail.com' => 'L\'équipe du FOG'])
            // ->setBcc('oeilglauque@gmail.com')
            ->setTo($reservation->getAuthor()->getEmail())
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/nouvelleReservation.html.twig',
                    ['reservation' => $reservation]
                ),
                'text/html'
            );
        $mailer->send($message);
    }
}
?>
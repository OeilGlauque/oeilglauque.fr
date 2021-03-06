<?php

namespace App\Controller;

use App\Entity\LocalReservation;
use App\Entity\Feature;
use App\Form\LocalReservationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Dotenv\Dotenv;

class LocalReservationController extends FOGController
{
    /**
     * @Route("/reservations/local", name="localReservation")
     */
    public function localReservation(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->getDoctrine()->getRepository(Feature::class)->find(2)->getState()) {
            return $this->render('oeilglauque/localReservation.html.twig', array(
                'state' => false
            ));
        }

        $reservation = new LocalReservation();
        $form = $this->createForm(LocalReservationType::class, $reservation, array());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $overlap = $this->getDoctrine()
                ->getRepository(LocalReservation::class)
                ->findLocalReservationOverlap($reservation);

            if ($overlap == 0) {

                $user = $this->getUser();
            $reservation->setAuthor($user);

            // Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->sendmail($reservation, $this->get('swiftmailer.mailer.default'));

            $this->addFlash('info', "Votre réservation a bien été enregistrée, vous recevrez une confirmation par e-mail dès qu'elle sera acceptée.");

            return $this->redirectToRoute('index');
            } else {
                $this->addFlash('warning',
                    "Votre réservation entre en conflit avec " . $overlap . " réservation(s) déjà effectuée(s) :(");
            }
        }

        return $this->render('oeilglauque/localReservation.html.twig', array(
            'form' => $form->createView(),
            'state' => true
        ));
    }

    private function sendmail(LocalReservation $reservation, \Swift_Mailer $mailer) {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env');


        $message = (new \Swift_Message('Nouvelle demande de réservation du local FOG'))
            ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setTo([$reservation->getAuthor()->getEmail() => $reservation->getAuthor()->getPseudo()])
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/localReservation/nouvelleReservation.html.twig',
                    ['reservation' => $reservation]
                ),
                'text/html'
            );
        $mailer->send($message);


        $message = (new \Swift_Message('Nouvelle demande de réservation du local FOG'))
            ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setTo([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/localReservation/admin/nouvelleReservation.html.twig',
                    ['reservation' => $reservation]
                ),
                'text/html'
            );
        $mailer->send($message);

    }
}
?>
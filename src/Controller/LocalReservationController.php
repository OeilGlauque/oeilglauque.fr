<?php

namespace App\Controller;

use App\Entity\LocalReservation;
use App\Entity\Feature;
use App\Form\LocalReservationType;
use App\Service\FOGMailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;

class LocalReservationController extends FOGController
{
    #[Route("/reservations/local", name: "localReservation")]
    public function localReservation(Request $request, EntityManagerInterface $manager, FOGMailerService $mailer)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$manager->getRepository(Feature::class)->find(2)->getState()) {
            return $this->render('oeilglauque/localReservation.html.twig', array(
                'state' => false
            ));
        }

        $reservation = new LocalReservation();
        $form = $this->createForm(LocalReservationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setDate($form->get('date')->getData());
            $reservation->setEndDate($form->get('duration')->getData());
            $reservation->setMotif($form->get('motif')->getData());

            $overlap = $manager->getRepository(LocalReservation::class)->findLocalReservationOverlap($reservation);

            if ($overlap == 0) {

                /** @var \App\Entity\User */
                $user = $this->getUser();
                $reservation->setAuthor($user);

                // Sauvegarde en base
                $manager->persist($reservation);
                $manager->flush();

                $mailer->sendMail(
                    new Address($reservation->getAuthor()->getEmail(), $reservation->getAuthor()->getPseudo()),
                    'Nouvelle demande de réservation du local FOG',
                    'oeilglauque/emails/localReservation/nouvelleReservation.html.twig',
                    ['reservation' => $reservation],
                    [],
                    [$mailer->getMailFOG()]
                );

                $this->addFlash('info', "Votre réservation a bien été enregistrée, vous recevrez une confirmation par e-mail dès qu'elle sera acceptée.");

                return $this->redirectToRoute('index');
            } else {
                $this->addFlash('warning',
                    "Votre réservation entre en conflit avec " . $overlap . " réservation(s) déjà effectuée(s) :(");
            }
        }

        return $this->renderForm('oeilglauque/localReservation.html.twig', [
            'form' => $form,
            'state' => true
        ]);
    }
}
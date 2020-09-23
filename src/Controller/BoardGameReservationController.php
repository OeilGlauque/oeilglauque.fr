<?php

namespace App\Controller;

use App\Entity\BoardGameReservation;
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $reservation = new BoardGameReservation();
        $form = $this->createForm(BoardGameReservationType::class, $reservation, array());
        $repository = $this->getDoctrine()
            ->getRepository('App:BoardGame');
        $boardGames = $repository->findAll();
        usort($boardGames, function($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $form->handleRequest($request);

        $overlap = $this->getDoctrine()
            ->getRepository(BoardGameReservation::class)
            ->findBoardGameReservationOverlap($reservation);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($reservation->getDateBeg() < $reservation->getDateEnd()) {
                if (count($overlap) == 0) {
    
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
                        "Votre réservation entre en conflit avec une réservation déjà effectuée sur le(s) jeu(x) suivant(s) : "
                        . implode(', ', $overlap));
                }
            } else {
                $this->addFlash('warning', "La date de début doit être antérieur à la date de fin.");
            }
        }

        return $this->render('oeilglauque/boardGameReservation.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(),
            'form' => $form->createView(),
            'boardGames' => $boardGames
        ));
    }

    private function sendmail(BoardGameReservation $reservation, \Swift_Mailer $mailer) {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env');


        $message = (new \Swift_Message('Nouvelle demande de réservation de jeu au FOG'))
            ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setBcc($_ENV['MAILER_ADDRESS'])
            ->setTo([$reservation->getAuthor()->getEmail() => $reservation->getAuthor()->getPseudo()])
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/boardGameReservation/nouvelleReservation.html.twig',
                    ['reservation' => $reservation]
                ),
                'text/html'
            );
        $mailer->send($message);

        $message = (new \Swift_Message('Nouvelle demande de réservation de jeu au FOG'))
            ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setTo([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/boardGameReservation/admin/nouvelleReservation.html.twig',
                    ['reservation' => $reservation]
                ),
                'text/html'
            );
        $mailer->send($message);
    }
}
?>
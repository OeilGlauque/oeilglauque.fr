<?php
namespace App\Controller;

use App\Entity\BoardGameReservation;
use App\Entity\Feature;
use App\Entity\BoardGame;
use App\Form\BoardGameReservationType;
use App\Service\FOGMailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;

class BoardGameReservationController extends FOGController
{
    #[Route("/reservations/boardGame", name: "boardGameReservation")]
    public function boardGameReservation(Request $request, EntityManagerInterface $manager, FOGMailerService $mailer)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$manager->getRepository(Feature::class)->find(3)->getState()) {
            return $this->render('oeilglauque/boardGameReservation.html.twig', [
                'state' => false
            ]);
        }

        $reservation = new BoardGameReservation();
        $form = $this->createForm(BoardGameReservationType::class, $reservation);
        $boardGames = $manager->getRepository(BoardGame::class)->findAll();
        usort($boardGames, function($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($reservation->getDateBeg() < $reservation->getDateEnd()) {
                $overlap = $manager
                    ->getRepository(BoardGameReservation::class)
                    ->findBoardGameReservationOverlap($reservation);
                    
                if (count($overlap) == 0) {
    
                    $user = $this->getUser();
                    $reservation->setAuthor($user);
    
                    // Sauvegarde en base
                    $manager->persist($reservation);
                    $manager->flush();

                    $mailer->sendMail(
                        new Address($reservation->getAuthor()->getEmail(), $reservation->getAuthor()->getPseudo()),
                        'Nouvelle demande de réservation de jeu au FOG',
                        'oeilglauque/emails/boardGameReservation/nouvelleReservation.html.twig',
                        ['reservation' => $reservation],
                        [],
                        [$mailer->getMailFOG()]
                    );
    
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

        return $this->renderForm('oeilglauque/boardGameReservation.html.twig', array(
            'form' => $form,
            'boardGames' => $boardGames,
            'state' => true
        ));
    }
}
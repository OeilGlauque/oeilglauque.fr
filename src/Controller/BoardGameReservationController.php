<?php
namespace App\Controller;

use App\Entity\BoardGameReservation;
use App\Entity\Feature;
use App\Entity\BoardGame;
use App\Form\BoardGameReservationType;
use App\Service\FOGDiscordWebhookService;
use App\Service\FOGGmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;

class BoardGameReservationController extends FOGController
{
    #[Route("/reservations/boardGame", name: "boardGameReservation")]
    public function boardGameReservation(Request $request, EntityManagerInterface $manager, FOGGmail $mailer, FOGDiscordWebhookService $discord)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$manager->getRepository(Feature::class)->find(3)->getState()) {
            return $this->render('oeilglauque/boardGameReservation.html.twig', [
                'state' => false
            ]);
        }

        $reservation = new BoardGameReservation();
        $boardGames = $manager->getRepository(BoardGame::class)->findAllAlphabetical();
        $form = $this->createForm(BoardGameReservationType::class, data: $reservation, options: ['choices' => $boardGames]);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($reservation->getDateBeg() > $reservation->getDateEnd()) {     
                $this->addFlash('warning', "La date de début doit être antérieur à la date de fin.");
            } else {
                if ($reservation->getDateBeg()->diff($reservation->getDateEnd())->days > 30) {
                    $this->addFlash('warning', "La réservation ne peut pas durer plus d'un mois.");
                } else {
                    $overlap = $manager
                    ->getRepository(BoardGameReservation::class)
                    ->findBoardGameReservationOverlap($reservation);
                    
                    if (count($overlap) != 0) {
                        $this->addFlash('warning',
                            "Votre réservation entre en conflit avec une réservation déjà effectuée sur le(s) jeu(x) suivant(s) : "
                            . implode(', ', $overlap));
                    } else {
                        $user = $this->getUser();
                        $reservation->setAuthor($user);
        
                        // Sauvegarde en base
                        $manager->persist($reservation);
                        $manager->flush();

                        // mail pour l'utilisateur
                        $mailer->sendTemplatedEmail(
                            new Address($reservation->getAuthor()->getEmail(), $reservation->getAuthor()->getPseudo()),
                            'Nouvelle demande de réservation de jeu au FOG',
                            'oeilglauque/emails/boardGameReservation/nouvelleReservation.html.twig',
                            ['reservation' => $reservation],
                            [],
                            []
                        );

                        // mail pour le bureau
                        $mailer->sendTemplatedEmail(
                            $mailer->getMailFOG(),
                            'Nouvelle demande de réservation de jeu au FOG',
                            'oeilglauque/emails/boardGameReservation/admin/nouvelleReservation.html.twig',
                            ['reservation' => $reservation],
                            [],
                            []
                        );

                        // ping discord du bureau
                        $discord->send(
                            'Nouvelle demande de réservation de jeux.',
                            [[
                                "title" => "Demande de réservation de jeux par " . $reservation->getAuthor()->getPseudo() . " (" . $reservation->getAuthor()->getEmail() . ") du " . $reservation->getDateBeg()->format('d/m/Y') . " au " . $reservation->getDateEnd()->format("d/m/Y"),
                                "description" => "Liste des jeux :",
                                "fields" => array_merge(
                                    array_map(function ($jeu,$price) {
                                        return ["name" => "- " . $jeu . " (" . $price . " €)", "value" => 
                                        (!is_null($jeu->getMissing()) && $jeu->getMissing() !== "" ? "Manquant : " . $jeu->getMissing() . "\n" : "") .
                                        (!is_null($jeu->getExcess()) && $jeu->getExcess() !== "" ? "En trop : " . $jeu->getExcess() . "\n" : "" ) .
                                        (!is_null($jeu->getNote()) && $jeu->getNote() !== "" ? "Note : " . $jeu->getNote() : "") 
                                    ];
                                    }, 
                                    $reservation->getBoardGames()->getValues(),
                                    array_map(function ($el) { return $el->getPrice();}, $reservation->getBoardGames()->getValues())
                                ),
                                    [
                                        [
                                            "name" => "", "value" => ""
                                        ],
                                        [
                                            "name" => "Note :", "value" => $reservation->getNote()
                                        ],
                                        [
                                            "name" => "Caution totale : " . $reservation->deposit() . " €",
                                            "value" => ""
                                        ],
                                        [
                                            "name" => "Lien pour gérer la réservation :", "value" => "https://oeilglauque.fr/admin/reservations/boardGame"
                                        ]
                                    ]
                                )
                            ]]
                        );
        
                        $this->addFlash('info', "Votre réservation a bien été enregistrée, vous recevrez une confirmation par e-mail dès qu'elle sera acceptée.");
        
                        return $this->redirectToRoute('index');
                    }
                }
            }
        }

        return $this->renderForm('oeilglauque/boardGameReservation.html.twig', [
            'form' => $form,
            'boardGames' => $boardGames,
            'state' => true
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\BoardGameOrder;
use App\Form\BoardGameOrderType;
use App\Entity\ShopBoardGame;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Dotenv\Dotenv;
use Psr\Log\LoggerInterface;

class BoardGameOrderController extends CustomController
{
    /**
     * @Route("/shop/boardGame", name="shopBoardGame")
     */
    public function shopBoardGame(Request $request, LoggerInterface $logger)
    {
        $order = new BoardGameOrder();
        $form = $this->createForm(BoardGameOrderType::class);

        $repository = $this->getDoctrine()
            ->getRepository('App:ShopBoardGame');
        $boardGames = $repository->findAll();
        usort($boardGames, function($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $logger->info(json_encode($data));
            $logger->info(json_encode(
                $data['boardGames']->current()->getName()
            ));
            
            // Sauvegarde en base
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($reservation);
            // $entityManager->flush();

            // $this->sendmail($reservation, $this->get('swiftmailer.mailer.default'));

            $this->addFlash('info', "Votre réservation a bien été enregistrée, vous recevrez une confirmation par e-mail dès qu'elle sera acceptée.");

            // return $this->redirectToRoute('index');
        }

        return $this->render('oeilglauque/boardGameShop.html.twig', array(
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
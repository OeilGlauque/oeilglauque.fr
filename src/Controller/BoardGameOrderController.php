<?php

namespace App\Controller;

use App\Entity\BoardGameOrder;
use App\Form\BoardGameOrderType;
use App\Entity\ShopBoardGame;
use App\Entity\ShopBoardGameQuantity;
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
        $form = $this->createForm(BoardGameOrderType::class);

        $repository = $this->getDoctrine()
            ->getRepository('App:ShopBoardGame');
        $boardGames = $repository->findAll();
        usort($boardGames, function($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //TODO checks
            $data = $request->request->get('board_game_order');
            $entityManager = $this->getDoctrine()->getManager();

            $order = new BoardGameOrder();
            $order->setName($data['name']);
            $order->setSurname($data['surname']);
            $order->setMail($data['mail']);
            $entityManager->persist($order);
            
            foreach($data['boardGamesQuantity'] as $bgq) {
                $gameQuantity = new ShopBoardGameQuantity();
                $gameQuantity->setQuantity((int)$bgq['quantity']);
                $gameQuantity->setBoardGame($boardGames[(int)$bgq['boardGames']]);
                $gameQuantity->setBoardGameOrder($order);
                $entityManager->persist($gameQuantity);
            }
            
            // $logger->info(json_encode($data));
            // $logger->info($data['name']);
            // $logger->info($data['surname']);
            // $logger->info($data['mail']);
            
            // Sauvegarde en base
            $entityManager->flush();

            // $this->sendmail($order, $this->get('swiftmailer.mailer.default'));

            $this->addFlash('info', "Votre commande a bien été enregistrée, une confirmation par e-mail a été envoyée.");

            return $this->redirectToRoute('index');
        }

        return $this->render('oeilglauque/boardGameShop.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(),
            'form' => $form->createView(),
            'boardGames' => $boardGames
        ));
    }

    private function sendmail(BoardGameOrder $order, \Swift_Mailer $mailer) {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env');


        $message = (new \Swift_Message('Confirmation d\'achat de jeu au FOG'))
            ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setBcc($_ENV['MAILER_ADDRESS'])
            ->setTo([$order->getMail() => $order->getSurname() . $order->getName()])
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/boardGameReservation/nouvelleReservation.html.twig',
                    ['order' => $order]
                ),
                'text/html'
            );
        $mailer->send($message);

        $message = (new \Swift_Message('Confirmation d\'achat de jeu au FOG'))
            ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setTo([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/boardGameReservation/admin/nouvelleReservation.html.twig',
                    ['order' => $order]
                ),
                'text/html'
            );
        $mailer->send($message);
    }
}
?>
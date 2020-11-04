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
     * @Route("/shop", name="shopBoardGame")
     */
    public function shopBoardGame(Request $request, LoggerInterface $logger)
    {
        $shopEnabled = $this->getParameter('allow_shop');
        if ($shopEnabled) {
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
                $total = 0.0;
    
                $order = new BoardGameOrder();
                $order->setName($data['name']);
                $order->setSurname($data['surname']);
                $order->setMail($data['mail']);
                
                foreach($data['boardGamesQuantity'] as $bgq) {
                    $gameQuantity = new ShopBoardGameQuantity();
                    $gameQuantity->setQuantity((int)$bgq['quantity']);
    
                    // $logger->info((int)$bgq['boardGames']);
                    // $logger->info($boardGames[(int)$bgq['boardGames']]);
                    // $logger->info($boardGames[(int)$bgq['boardGames']]->getId());
                    // $logger->info(implode(", ", $boardGames));
                    
                    $gameId = (int)$bgq['boardGames'];
                    $currentGame;
                    foreach($boardGames as $game) {
                        if ($game->getId() == $gameId) {
                            $currentGame = $game;
                            break;
                        }
                    }
                    $gameQuantity->setBoardGame($currentGame);
                    $gameQuantity->setBoardGameOrder($order);
                    $entityManager->persist($gameQuantity);
                    $order->addBoardGameQuantity($gameQuantity);
                    $total += $gameQuantity->getQuantity() * $gameQuantity->getBoardGame()->getPrice();
                }
                $entityManager->persist($order);
                
                // $logger->info(json_encode($data));
                // $logger->info($data['name']);
                // $logger->info(implode(" ", $order->getBoardGamesQuantity()->toArray()));
                // $logger->info(count($order->getBoardGamesQuantity()->toArray()));
                
                // Sauvegarde en base
                $entityManager->flush();
    
                $this->sendmail($order, $total, $this->get('swiftmailer.mailer.default'));
    
                $this->addFlash('info', "Votre commande a bien été enregistrée, une confirmation par e-mail a été envoyée.");
    
                return $this->redirectToRoute('index');
            }
    
            return $this->render('oeilglauque/boardGameShop.html.twig', array(
                'dates' => $this->getCurrentEdition()->getDates(),
                'form' => $form->createView(),
                'boardGames' => $boardGames
            ));
        } else {
            return $this->redirectToRoute('index');
        }
    }

    private function sendmail(BoardGameOrder $order, float $total, \Swift_Mailer $mailer) {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env');


        $message = (new \Swift_Message('Confirmation d\'achat de jeu au FOG'))
            ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setBcc($_ENV['MAILER_ADDRESS'])
            ->setTo([$order->getMail() => $order->getSurname() . $order->getName()])
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/shop/confirmationCommande.html.twig',
                    ['order' => $order, 'total' => $total]
                ),
                'text/html'
            );
        $mailer->send($message);

        $message = (new \Swift_Message('Confirmation d\'achat de jeu au FOG'))
            ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setTo([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/shop/admin/confirmationCommande.html.twig',
                    ['order' => $order, 'total' => $total]
                ),
                'text/html'
            );
        $mailer->send($message);
    }
}
?>
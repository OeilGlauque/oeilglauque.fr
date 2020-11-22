<?php

namespace App\Controller;

use App\Entity\BoardGameOrder;
use App\Form\BoardGameOrderType;
use App\Entity\ShopBoardGame;
use App\Entity\ShopBoardGameQuantity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Dotenv\Dotenv;

class BoardGameOrderController extends CustomController
{
    /**
     * @Route("/shop", name="shopBoardGame")
     */
    public function shopBoardGame(Request $request)
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
                if (!array_key_exists('boardGamesQuantity', $data)) {
                    $this->addFlash('warning',
                        "Vous devez ajouter au moins un jeu à votre commande.");
                } else {
                    $entityManager = $this->getDoctrine()->getManager();
                    $total = 0.0;
        
                    $order = new BoardGameOrder();
                    $order->setName($data['name']);
                    $order->setSurname($data['surname']);
                    $order->setMail($data['mail']);
                    $order->setAddress($data['address']);
                    $order->setCity($data['city']);
                    $order->setPostalCode($data['postalCode']);
                    
                    $dedupGame = $this->gameDeduplicate($data['boardGamesQuantity']);

                    foreach($dedupGame as $bgqId=>$bgqQ) {
                        $gameQuantity = new ShopBoardGameQuantity();
                        $gameQuantity->setQuantity($bgqQ);
                        
                        $currentGame;
                        foreach($boardGames as $game) {
                            if ($game->getId() == $bgqId) {
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
                    
                    // Sauvegarde en base
                    $entityManager->flush();
        
                    $this->sendmail($order, $total, $this->get('swiftmailer.mailer.default'));
        
                    $this->addFlash('info', "Votre commande a bien été enregistrée, une confirmation par e-mail a été envoyée.");
        
                    return $this->redirectToRoute('index');
                }
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

    private function gameDeduplicate(array $games) {
        $dedupGame = [];
        foreach($games as $bgq) {
            $id = $bgq['boardGames'];
            $q = (int)$bgq['quantity'];
            if (array_key_exists($id, $dedupGame)) {
                $dedupGame[$id] += $q;
            } else {
                $dedupGame[$id] = $q;
            }
        }
        return $dedupGame;
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
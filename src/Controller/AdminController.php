<?php

namespace App\Controller;

use App\Entity\BoardGameReservation;
use App\Entity\LocalReservation;
use App\Entity\Feature;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Edition;
use App\Entity\GameSlot;
use App\Entity\Game;
use App\Entity\News;
use App\Entity\User;
use App\Form\NewsType;
use App\Repository\EditionRepository;
use App\Repository\FeatureRepository;
use Doctrine\Persistence\ManagerRegistry;
use PharIo\Manifest\Manifest;
use Symfony\Component\Validator\Constraints\Date;

class AdminController extends FOGController {

    /****************************************
     *      Interface d'administration      *
     ****************************************/

    #[Route("/admin", name: "admin")]
    public function admin(FeatureRepository $featureRepository) : Response{
        $newsState = $featureRepository->find(6)->getState();
        return $this->render('oeilglauque/admin.html.twig', array(
            'newsState' => $newsState
        ));
    }


    /**********************************
     *      Gestion des éditions      *
     **********************************/


    #[Route("/admin/editions", name: "admin_editions")]
    public function editionsAdmin(EditionRepository $editionRepository) {
        $editions = array_reverse($editionRepository->findAll());
        return $this->render('oeilglauque/admin/editions.html.twig', array(
            'editions' => $editions
        ));
    }

    #[Route("/admin/editions/updateEdition/{edition}", name: "updateEdition")]
    public function updateEdition(Request $request, $edition, ManagerRegistry $doctrine) {
        $editionval = $doctrine->getRepository(Edition::class)->find($edition);
        if(!$editionval) {
            throw $this->createNotFoundException(
                'Aucune édition n\'a pour id '.$edition
            );
        }
        if($request->query->get('dates') != "") {
            $editionval->setDates($request->query->get('dates'));
            $editionval->setHomeText($request->query->get('homeText'));
            $doctrine->getManager()->flush();
            $this->addFlash('success', "L'édition " . $editionval->getAnnee() . " a bien été mise à jour.");
        }
        return $this->redirectToRoute('admin_editions');
    }

    #[Route("/admin/editions/updateGameSlot/{slot}", name: "updateGameSlot")]
    public function updateGameSlot(Request $request, $slot, ManagerRegistry $doctrine) {
        $slotval = $doctrine->getRepository(GameSlot::class)->find($slot);
        if (!$slotval) {
            throw $this->createNotFoundException(
                'Aucun slot n\'a pour id '.$slot
            );
        }
        if($request->query->get('text') != "") {
            $slotval->setText($request->query->get('text'));
            $slotval->setMaxGames($request->query->get('maxGames'));
            $doctrine->getManager()->flush();
            $this->addFlash('success', "Le slot a bien été mis à jour. ");
        }
        return $this->redirectToRoute('admin_editions');
    }

    #[Route("/admin/editions/addGameSlot/{edition}", name: "addGameSlot")]
    public function addGameSlot(Request $request, $edition, ManagerRegistry $doctrine) {
        if($request->query->get('text') != "") {
            $slot = new GameSlot();
            $slot->setText($request->query->get('text'));
            $slot->setMaxGames($request->query->get('maxGames'));
            $edition = $doctrine->getRepository(Edition::class)->find($edition);
            if (!$edition) {
                throw $this->createNotFoundException(
                    'Aucune édition n\'a pour id '.$edition
                );
            }
            $slot->setEdition($edition);
            $doctrine->getManager()->persist($slot);
            $doctrine->getManager()->flush();
            $this->addFlash('success', "Le slot a bien été ajouté. ");
        }

        return $this->redirectToRoute('admin_editions');
    }

    /******************************
     *      Gestion des news      *
     ******************************/

    #[Route("/admin/news/rediger", name: "writeNews")]
    public function writeNews(Request $request, ManagerRegistry $doctrine) {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news->setAuthor($this->getUser());

            // Sauvegarde en base
            $entityManager = $doctrine->getManager();
            $entityManager->persist($news);
            $entityManager->flush();
            $this->addFlash('success', "La news ".$news->getTitle()." a bien été publiée.");

            return $this->redirectToRoute('newsIndex');
        }

        return $this->render('oeilglauque/admin/writeNews.html.twig', array(
            'form' => $form->createView(), 
            'edit' => false
        ));
    }

    #[Route("/admin/news/edit/{slug}", name: "editNews")]
    public function editNews(Request $request, $slug, ManagerRegistry $doctrine) {
        $news = $doctrine->getRepository(News::class)->findOneBy(['slug' => $slug]);
        if(!$news) {
            throw $this->createNotFoundException(
                'Impossible de trouver la resource demandée'
            );
        }
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news->setAuthor($this->getUser());

            // Sauvegarde en base
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            $this->addFlash('success', "La news a bien été modifiée.");

            return $this->redirectToRoute('newsIndex');
        }

        return $this->render('oeilglauque/admin/writeNews.html.twig', array(
            'form' => $form->createView(), 
            'edit' => true
        ));
    }

    #[Route("/admin/news/delete/{slug}", name: "deleteNews")]
    public function deleteNews(Request $request, $slug, ManagerRegistry $doctrine) {
        $news = $doctrine->getRepository(News::class)->findOneBy(['slug' => $slug]);
        if(!$news) {
            throw $this->createNotFoundException(
                'Impossible de trouver la resource demandée'
            );
        }
        $entityManager = $doctrine->getManager();
        $entityManager->remove($news);
        $entityManager->flush();
        $this->addFlash('success', "La news ".$news->getTitle()." a bien été supprimée.");

        return $this->redirectToRoute('newsIndex');
    }


    /******************************
     *      Nouvelle édition      *
     ******************************/

    #[Route("/admin/editions/nouvelle", name: "newEdition")]
    public function newEdition() {
        return $this->render('oeilglauque/admin/newEdition.html.twig');
    }

    #[Route("/admin/editions/creer", name: "createEdition")]
    public function createEdition(Request $request, ManagerRegistry $doctrine) {
        if($request->query->get('annee') != "" && $request->query->get('dates')) {
            $editionCheck = $doctrine->getRepository(Edition::class)->findOneBy(['annee' => $request->query->get('annee')]);
            if ($editionCheck) {
                $this->addFlash('danger', "L'édition ".$request->query->get('annee')." existe déjà.");
                return $this->redirectToRoute('newEdition');
            }
            $edition = new Edition();
            $edition->setAnnee($request->query->get('annee'));
            $edition->setDates($request->query->get('dates'));
            $edition->setHomeText($request->query->get('homeText'));
            $doctrine->getManager()->persist($edition);
            $doctrine->getManager()->flush();
            $this->addFlash('success', "La nouvelle édition a bien été ajoutée");
        }
        return $this->redirectToRoute('admin');
    }

    /************************************
     *       Gestion des parties        *
     ************************************/

    #[Route("/admin/games/validate", name: "unvalidatedGamesList")]
    public function unvalidatedGamesList(ManagerRegistry $doctrine) {
        $games = $doctrine->getRepository(Game::class)->getOrderedGameList($this->getCurrentEdition(), false);
        return $this->render('oeilglauque/admin/unvalidatedGamesList.html.twig', array(  
            'games' => $games
        ));
    }

    #[Route("/admin/games", name: "adminGamesList")]
    public function adminGamesList(ManagerRegistry $doctrine) {
        $games = $doctrine->getRepository(Game::class)->getOrderedGameList($this->getCurrentEdition(), true);
        return $this->render('oeilglauque/admin/gamesList.html.twig', array(
            'games' => $games, 
        ));
    }

    #[Route("/admin/games/validate/{id}", name: "validateGame")]
    public function validateGame($id, \Swift_Mailer $mailer, ManagerRegistry $doctrine) {
        $game = $doctrine->getRepository(Game::class)->find($id);
        if($game) {
            $game->setValidated(true);
            $doctrine->getManager()->persist($game);
            $doctrine->getManager()->flush();

            $author = $game->getAuthor();
            $message = (new \Swift_Message('Votre partie '.$game->getTitle().' a été validée !'))
            ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setTo([$author->getEmail() => $author->getPseudo()])
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/game/gameValidationNotif.html.twig',
                    ['author' => $author,
                    'game' => $game]
                ),
                'text/html'
            );
        $mailer->send($message);

            $this->addFlash('success', "La partie a bien été validée.");
        }
        return $this->redirectToRoute('unvalidatedGamesList');
    }

    #[Route("/admin/games/deleteGame/{id}", name: "deleteGame")]
    public function deleteGame($id, ManagerRegistry $doctrine) {
        $game = $doctrine->getRepository(Game::class)->find($id);
        if ($game) {
            $doctrine->getManager()->remove($game);
            $doctrine->getManager()->flush();
            $this->addFlash('success', "La partie a bien été supprimée.");
        }
        return $this->redirectToRoute('unvalidatedGamesList');
    }

    #[Route("/admin/games/unregister/{idGame}/{idPlayer}", name: "unregisterGamePlayer")]
    public function unregisterGamePlayer(Request $request, $idGame, $idPlayer, ManagerRegistry $doctrine) {
        $game = $doctrine->getRepository(Game::class)->find($idGame);
        $player = $doctrine->getRepository(User::class)->find($idPlayer);
        if ($game && $player) {
            $game->removePlayer($player); // Handles 'contains' verification
            $entityManager = $doctrine->getManager();
            $entityManager->persist($game);
            $entityManager->flush();
            $this->addFlash('info', "Le joueur ".$player->getPseudo()." a bien été supprimé de la partie ".$game->getTitle());
            return $this->redirectToRoute('adminGamesList');
        } else {
            throw $this->createNotFoundException('Impossible de trouver la partie ou le joueur demandée. ');
        }
    }

    #[Route("/admin/games/lock/{id}/{status}", name: "lockGame")]
    public function lockGame(Request $request, $id, $status, ManagerRegistry $doctrine) {
        $game = $doctrine->getRepository(Game::class)->find($id);
        if ($game) {
            $game->setLocked($status == 1 ? true : false);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($game);
            $entityManager->flush();
            $this->addFlash('info', "La partie ".$game->getTitle()." a bien été ".($status ? 'bloquée' : 'débloquée'));
            return $this->redirectToRoute('adminGamesList');
        } else {
            throw $this->createNotFoundException('Impossible de trouver la partie demandée.');
        }
    }

    /************************************
     *     Gestion des reservations     *
     ************************************/

    /*****************
     *     local     *
     *****************/

    #[Route("/admin/reservations/local", name: "localReservationList")]
    public function localReservationList(ManagerRegistry $doctrine) {
        $reservations =$doctrine->getRepository(LocalReservation::class)->getLocalReservationList();
        return $this->render('oeilglauque/admin/localReservationList.html.twig', array(
            'reservations' => $reservations,
            'archive' => false
        ));
    }
    #[Route("/admin/reservations/local/validate/{id}", name: "validateLocalReservation")]
    public function validateLocalReservation($id, ManagerRegistry $doctrine) {
        $reservation = $doctrine->getRepository(LocalReservation::class)->find($id);
        if($reservation) {
            $reservation->setValidated(true);
            $doctrine->getManager()->persist($reservation);
            $doctrine->getManager()->flush();

            $this->sendmail('Demande de réservation du local FOG Acceptée !',
                [$reservation->getAuthor()->getEmail() => $reservation->getAuthor()->getPseudo()],
                'localReservation/confirmationReservation',
                ['reservation' => $reservation],
                $this->get('swiftmailer.mailer.default'));

            $this->sendmail('Demande de réservation du local FOG Acceptée',
                [$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'],
                'localReservation/admin/confirmationReservation',
                ['reservation' => $reservation],
                $this->get('swiftmailer.mailer.default'));

            $this->addFlash('success', "La demande a bien été acceptée.");
        }
        return $this->redirectToRoute('localReservationList');
    }

    #[Route("/admin/reservations/local/delete/{id}", name: "deleteLocalReservation")]
    public function deleteLocalReservation($id, ManagerRegistry $doctrine) {
        $archive = false;

        $reservation = $doctrine->getRepository(LocalReservation::class)->find($id);
        if ($reservation) {
            $doctrine->getManager()->remove($reservation);
            $doctrine->getManager()->flush();

            if($reservation->getDate() > new \DateTime()) {
                $this->sendmail('Demande de réservation du local FOG refusée',
                [$reservation->getAuthor()->getEmail() => $reservation->getAuthor()->getPseudo()],
                    'localReservation/suppressionReservation',
                    ['reservation' => $reservation],
                    $this->get('swiftmailer.mailer.default'));

                $this->sendmail('Demande de réservation du local FOG refusée',
                    [$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'],
                    'localReservation/admin/suppressionReservation',
                    ['reservation' => $reservation],
                    $this->get('swiftmailer.mailer.default'));
            }

            $this->addFlash('success', "La demande a bien été supprimée.");
        }
        return $this->redirectToRoute('localReservationList');
    }
    #[Route("/admin/reservations/local/archive", name: "localReservationArchive")]
    public function localReservationArchive(ManagerRegistry $doctrine) {
        $reservations = $doctrine->getRepository(LocalReservation::class)->getLocalReservationArchive();
        return $this->render('oeilglauque/admin/localReservationList.html.twig', array(
            'reservations' => $reservations,
            'archive' => true
        ));
    }

    private function sendmail(string $obj, array $to, string $templateName, array $data, \Swift_Mailer $mailer) {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env');

        $message = (new \Swift_Message($obj))
            ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
            ->setBcc($_ENV['MAILER_ADDRESS'])
            ->setTo($to)
            ->setBody(
                $this->renderView(
                    'oeilglauque/emails/' . $templateName . '.html.twig',
                    $data
                ),
                'text/html'
            );

        $mailer->send($message);
    }

    /*****************
     *      jeux     *
     *****************/
    #[Route("/admin/reservations/boardGame", name: "boardGameReservationList")]
    public function boardGameReservationList(ManagerRegistry $doctrine) {
        $reservations =$doctrine->getRepository(BoardGameReservation::class)->getBoardGameReservationList();
        return $this->render('oeilglauque/admin/boardGameReservationList.html.twig', array(
            'reservations' => $reservations,
            'archive' => false
        ));
    }
    #[Route("/admin/reservations/boardGame/validate/{id}", name: "validateBoardGameReservation")]
    public function validateBoardGameReservation($id, ManagerRegistry $doctrine) {
        $reservation = $doctrine->getRepository(BoardGameReservation::class)->find($id);
        if($reservation) {
            $reservation->setValidated(true);
            $doctrine->getManager()->persist($reservation);
            $doctrine->getManager()->flush();

            $this->sendmail('Demande de réservation de jeu au FOG Acceptée !',
                [$reservation->getAuthor()->getEmail() => $reservation->getAuthor()->getPseudo()],
                'boardGameReservation/confirmationReservation',
                ['reservation' => $reservation],
                $this->get('swiftmailer.mailer.default'));

            $this->sendmail('Demande de réservation de jeu au FOG Acceptée',
                [$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'],
                'boardGameReservation/admin/confirmationReservation',
                ['reservation' => $reservation],
                $this->get('swiftmailer.mailer.default'));

            $this->addFlash('success', "La demande a bien été acceptée.");
        }
        return $this->redirectToRoute('boardGameReservationList');
    }

    #[Route("/admin/reservations/boardGame/delete/{id}", name: "deleteBoardGameReservation")]
    public function deleteBoardGameReservation($id, ManagerRegistry $doctrine) {
        $archive = false;

        $reservation = $doctrine->getRepository(BoardGameReservation::class)->find($id);
        if ($reservation) {
            if($reservation->getDateBeg() > new \DateTime()) {
                $this->sendmail('Demande de réservation de jeu au FOG refusée',
                    [$reservation->getAuthor()->getEmail() => $reservation->getAuthor()->getPseudo()],
                    'boardGameReservation/suppressionReservation',
                    ['reservation' => $reservation],
                    $this->get('swiftmailer.mailer.default'));
            }

            if($reservation->getDateBeg() > new \DateTime()) {
                $this->sendmail('Demande de réservation de jeu au FOG refusée',
                    [$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'],
                    'boardGameReservation/admin/suppressionReservation',
                    ['reservation' => $reservation],
                    $this->get('swiftmailer.mailer.default'));
            }

            $doctrine->getManager()->remove($reservation);
            $doctrine->getManager()->flush();

            $this->addFlash('success', "La demande a bien été supprimée.");
        }
        return $this->redirectToRoute('boardGameReservationList');
    }
    #[Route("/admin/reservations/boardGame/archive", name: "boardGameReservationArchive")]
    public function boardGameReservationArchive(ManagerRegistry $doctrine)
    {
        $reservations = $doctrine->getRepository(BoardGameReservation::class)->getBoardGameReservationArchive();
        return $this->render('oeilglauque/admin/boardGameReservationList.html.twig', array(
            'reservations' => $reservations,
            'archive' => true
        ));
    }

    /*****************
     *    feature    *
     *****************/
    #[Route("/admin/feature", name: "adminFeature")]
    public function adminFeature(ManagerRegistry $doctrine) {
        $features =$doctrine->getRepository(Feature::class)->findAll();
        return $this->render('oeilglauque/admin/feature.html.twig', array(
            'features' => $features
        ));
    }

    #[Route("/admin/feature/update/{id}/{state}", name: "updateFeatureState")]
    public function updateFeatureState($id, $state, ManagerRegistry $doctrine) {
        $feature = $doctrine->getRepository(Feature::class)->find($id);
        if($feature) {
            $feature->setState($state != 0);
            $doctrine->getManager()->persist($feature);
            $doctrine->getManager()->flush();

            $this->addFlash('success', $feature->getName() . " est désormais " . ($feature->getState() ? "activée" : "désactivée"));
        }
        return $this->redirectToRoute('adminFeature');
    }
}

?>

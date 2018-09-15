<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Edition;
use App\Entity\GameSlot;
use App\Entity\Game;
use App\Entity\News;
use App\Form\NewsType;

class AdminController extends Controller {

    /****************************************
     *      Interface d'administration      *
     ****************************************/

    /**
     * @Route("/admin", name="admin")
     */
    public function admin() {
        return $this->render('oeilglauque/admin.html.twig', array(
            'dates' => "Du 19 au 21 octobre", 
        ));
    }


    /**********************************
     *      Gestion des éditions      *
     **********************************/


    /**
     * @Route("/admin/editions", name="admin_editions")
     */
    public function editionsAdmin() {
        $editions = $this->getDoctrine()->getRepository(Edition::class)->findAll();
        return $this->render('oeilglauque/admin/editions.html.twig', array(
            'dates' => "Du 19 au 21 octobre", 
            'editions' => $editions
        ));
    }

    /**
     * @Route("/admin/editions/updateDates/{edition}", name="updateDates")
     */
    public function updateDates(Request $request, $edition) {
        $editionval = $this->getDoctrine()->getRepository(Edition::class)->find($edition);
        if(!$editionval) {
            throw $this->createNotFoundException(
                'Aucune édition n\'a pour id '.$edition
            );
        }
        if($request->query->get('dates') != "") {
            $editionval->setDates($request->query->get('dates'));
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('admin_editions');
    }

    /**
     * @Route("/admin/editions/updateSlot/{slot}", name="updateSlot")
     */
    public function updateSlot(Request $request, $slot) {
        $slotval = $this->getDoctrine()->getRepository(GameSlot::class)->find($slot);
        if (!$slotval) {
            throw $this->createNotFoundException(
                'Aucun slot n\'a pour id '.$slot
            );
        }
        if($request->query->get('text') != "") {
            $slotval->setText($request->query->get('text'));
            $slotval->setMaxGames($request->query->get('maxGames'));
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('admin_editions');
    }

    /**
     * @Route("/admin/editions/deleteSlot/{slot}", name="deleteSlot")
     */
    public function deleteSlot($slot) {
        $slotval = $this->getDoctrine()->getRepository(GameSlot::class)->find($slot);
        if ($slotval) {
            $this->getDoctrine()->getManager()->remove($slotval);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('admin_editions');
    }

    /**
     * @Route("/admin/editions/addSlot/{edition}", name="addSlot")
     */
    public function addSlot(Request $request, $edition) {
        if($request->query->get('text') != "") {
            $slot = new GameSlot();
            $slot->setText($request->query->get('text'));
            $slot->setMaxGames($request->query->get('maxGames'));
            $editionval = $this->getDoctrine()->getRepository(Edition::class)->find($edition);
            if (!$editionval) {
                throw $this->createNotFoundException(
                    'Aucune édition n\'a pour id '.$edition
                );
            }
            $slot->setEdition($editionval);
            $this->getDoctrine()->getManager()->persist($slot);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('admin_editions');
    }

    /******************************
     *      Gestion des news      *
     ******************************/

    /**
     * @Route("/admin/news/rediger", name="writeNews")
     */
    public function writeNews(Request $request) {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news->setAuthor($this->getUser());

            // Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('newsIndex');
        }

        return $this->render('oeilglauque/admin/writeNews.html.twig', array(
            'dates' => "Du 19 au 21 octobre", 
            'form' => $form->createView()
        ));
    }


    /******************************
     *      Nouvelle édition      *
     ******************************/

    /**
    * @Route("/admin/editions/nouvelle", name="newEdition")
    */
    public function newEdition() {
        return $this->render('oeilglauque/admin/newEdition.html.twig', array(
            'dates' => "Du 19 au 21 octobre", 
        ));
    }

    /**
    * @Route("/admin/editions/creer", name="createEdition")
    */
    public function createEdition(Request $request) {
        if($request->query->get('annee') != "" && $request->query->get('dates')) {
            $edition = new Edition();
            $edition->setAnnee($request->query->get('annee'));
            $edition->setDates($request->query->get('dates'));
            $this->getDoctrine()->getManager()->persist($edition);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('admin');
    }

    /************************************
     *      Validation des parties      *
     ************************************/

    /**
     * @Route("/admin/games/validate", name="unvalidatedGamesList")
     */
    public function unvalidatedGamesList() {
        $games = $this->getDoctrine()->getRepository(Game::class)->findBy(["validated" => false]);
        return $this->render('oeilglauque/admin/unvalidatedGamesList.html.twig', array(
            'dates' => "Du 19 au 21 octobre", 
            'games' => $games
        ));
    }

    /**
     * @Route("/admin/games/validate/{id}", name="validateGame")
     */
    public function validateGame($id) {
        $game = $this->getDoctrine()->getRepository(Game::class)->find($id);
        if($game) {
            $game->setValidated(true);
            $this->getDoctrine()->getManager()->persist($game);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('unvalidatedGamesList');
    }

    /**
     * @Route("/admin/games/deleteGame/{id}", name="deleteGame")
     */
    public function deleteGame($id) {
        $game = $this->getDoctrine()->getRepository(Game::class)->find($id);
        if ($game) {
            $this->getDoctrine()->getManager()->remove($game);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('unvalidatedGamesList');
    }
}

?>
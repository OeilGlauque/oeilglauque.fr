<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Game;
use App\Form\GameType;

class StaticPagesController extends CustomController {
    
    /**
     * @Route("/informationsFestival", name="infosFest")
     */
    public function infosFest(Request $request) {
        return $this->render('oeilglauque/infosFest.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
        ));
    }

    /**
     * @Route("/informationsClub", name="infosClub")
     */
    public function infosClub() {
        return $this->render('oeilglauque/infosClub.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
        ));
    }

    /**
     * @Route("/planning", name="planning")
     */
    public function planning() {
        return $this->render('oeilglauque/planning.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
        ));
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact() {
        return $this->render('oeilglauque/contact.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
        ));
    }

    /**
     * @Route("/partenaires", name="partenaires")
     */
    public function partenaires() {
        return $this->render('oeilglauque/partenaires.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
        ));
    }

    /**
     * @Route("/reservations", name="reservations")
     */
    public function reservations() {
        return $this->render('oeilglauque/reservations.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
        ));
    }

    /**
     * @Route("/photos", name="photos")
     */
    public function photos() {
        return $this->render('oeilglauque/photos.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
        ));
    }
}

?>

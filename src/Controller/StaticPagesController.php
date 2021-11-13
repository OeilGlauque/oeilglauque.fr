<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Game;
use App\Form\GameType;

class StaticPagesController extends FOGController {
    
    /**
     * @Route("/informationsFestival", name="infosFest")
     */
    public function infosFest(Request $request) {
        return $this->renderPage('oeilglauque/infosFest.html.twig');
    }

    /**
     * @Route("/informationsClub", name="infosClub")
     */
    public function infosClub() {
        return $this->renderPage('oeilglauque/infosClub.html.twig');
    }

    /**
     * @Route("/planning", name="planning")
     */
    public function planning() {
        return $this->renderPage('oeilglauque/planning.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact() {
        return $this->renderPage('oeilglauque/contact.html.twig');
    }

    /*
     * ("/partenaires", name="partenaires")
    public function partenaires() {
        return $this->renderPage('oeilglauque/partenaires.html.twig');
    }
    */

    /**
     * @Route("/photos", name="photos")
     */
    public function photos() {
        return $this->renderPage('oeilglauque/photos.html.twig');
    }
}

?>

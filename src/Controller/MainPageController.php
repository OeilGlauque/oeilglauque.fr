<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Feature;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainPageController extends CustomController {
    
    /**
     * @Route("/", name="index")
     */
    public function index() {
        if ($this->getDoctrine()->getRepository(Feature::class)->find(4)->getState()) {
            return $this->render('oeilglauque/index.html.twig', array(
                'dates' => $this->getCurrentEdition()->getDates(),
                'state' => true
            ));
        }

        return $this->render('oeilglauque/index.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
            'state' => false
        ));
    }
}

?>
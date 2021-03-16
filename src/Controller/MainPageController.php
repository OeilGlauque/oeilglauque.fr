<?php

namespace App\Controller;

use App\Entity\Edition;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainPageController extends FOGController {
    
    /**
     * @Route("/", name="index")
     */
    public function index() {
        $edition = $this->getDoctrine()->getRepository(Edition::class)->findOneBy(['annee' => $this->getParameter('current_edition')]);
        $homeText = '';
        if ($edition != null) {
            $homeText = $edition->getHomeText();
        }
        return $this->render('oeilglauque/index.html.twig', array(
            'homeText' => $homeText
        ));
    }
}

?>
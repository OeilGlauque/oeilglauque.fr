<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainPageController extends FOGController {
    
    /**
     * @Route("/", name="index")
     */
    public function index() {
        return $this->render('oeilglauque/index.html.twig');
    }
}

?>
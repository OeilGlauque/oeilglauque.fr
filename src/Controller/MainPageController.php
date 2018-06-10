<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainPageController extends Controller {
    
    /**
     * @Route("/")
     */
    public function index() {
        //return new Response("Hello world !");
        return $this->render('oeilglauque/index.html.twig', array(
            'var' => 0, 
        ));
    }
}

?>
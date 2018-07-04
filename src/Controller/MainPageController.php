<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainPageController extends Controller {
    
    /**
     * @Route("/", name="index")
     */
    public function index() {
        //return new Response("Hello world !");
        return $this->render('oeilglauque/index.html.twig', array(
            'dates' => "Du 10 au 31 octobre", 
        ));
    }

    /**
     * @Route("/infos")
     */
    public function infos() {
        return $this->render('oeilglauque/infos.html.twig', array(
            'dates' => "Du 10 au 31 octobre", 
        ));
    }

    /**
     * @Route("/admin")
     */
    public function admin() {
        return new Response("admin page !");
    }
}

?>
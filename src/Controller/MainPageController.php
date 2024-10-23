<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends FOGController {
    
    #[Route("/", name: "index", methods: ['GET'])]
    public function index() : Response{
        $edition = $this->FogParams->getCurrentEdition();
        $homeText = '';
        if ($edition != null) {
            $homeText = $edition->getHomeText();
        }
        return $this->render('oeilglauque/index.html.twig', [
            'homeText' => $homeText
        ]);
    }
}
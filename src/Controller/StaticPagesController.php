<?php

namespace App\Controller;

use App\Repository\PosterRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticPagesController extends FOGController {
    
    #[Route("/informationsFestival", name: "infosFest", methods: ['GET'])]
    public function infosFest() : Response
    {
        return $this->render('oeilglauque/infosFest.html.twig', [
            'newHeader' => true
        ]);
    }

    #[Route("/informationsClub", name: "infosClub", methods: ['GET'])]
    public function infosClub() : Response
    {
        return $this->render('oeilglauque/infosClub.html.twig', [
            'newHeader' => true
        ]);
    }

    #[Route("/contact", name: "contact", methods: ['GET'])]
    public function contact() : Response
    {
        return $this->render('oeilglauque/contact.html.twig', [
            'newHeader' => true
        ]);
    }

    /*
     * ("/partenaires", name="partenaires")
    public function partenaires() {
        return $this->render('oeilglauque/partenaires.html.twig');
    }
    */

    #[Route("/photos", name: "photos", methods: ['GET'])]
    public function photos() : Response
    {
        return $this->render('oeilglauque/photos.html.twig');
    }

    #[Route("/menu", name: "menu", methods:['GET'])]
    public function menu() : Response
    {
        return $this->render('oeilglauque/menu.html.twig', [
            'newHeader' => true
        ]);
    }

    #[Route("/affiches", name: "affiches", methods: ['GET'])]
    public function affiches(PosterRepository $posterRepository) : Response{
        return $this->render('oeilglauque/affiches.html.twig', [
            'posters' => $posterRepository->findAll(),
            'newHeader' => true
        ]);
    }
}
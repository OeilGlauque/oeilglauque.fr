<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends FOGController {
    
    #[Route("/", name: "index", methods: ['GET'])]
    public function index() : Response{
        $edition = $this->FogParams->getCurrentEdition();//$editionRepository->findOneBy(['annee' => $this->getParameter('current_edition')]);
        $homeText = '';
        if ($edition != null) {
            $homeText = $edition->getHomeText();
        }
        return $this->render('oeilglauque/index.html.twig', [
            'homeText' => $homeText
        ]);
    }

    #[Route("/test", name: "test")]
    public function test(): Response {
        /*
        $webhook = "";

        $data = array_map(function ($el) { return "Jeux" . $el;}, range(1,3));

        $json_data = json_encode([
            "content" => "<@&1072599909288652800> Test webhook !",
            "username" => "Test",
            "embeds" => [
                [
                    "title" => "Demande de réservation de jeux",
                    "description" => "Liste des jeux :",
                    "fields" => [
                        [
                            "name" => "Test 1", "value" => ""
                        ],
                        [
                            "name" => "Test2", "value" => ""
                        ]
                    ]//array_map(function ($r) {return ["name" => $r, "value" => ""];}, $data)
                ]
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init($webhook);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);

        curl_close($ch);
        */

        return $this->redirectToRoute('index');
    }
}
<?php

namespace App\Controller;

use App\Entity\Edition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends FOGController
{
    #[Route("/planning", name: "planning", methods: ['GET'])]
    public function planning() : Response
    {
        $edition = $this->FogParams->getCurrentEdition();
        if ($edition->getId() != null) {
            return $this->render('oeilglauque/planning.html.twig', [
                'events' => $edition->getEvents(),
                'edition' => $this->FogParams->getCurrentEdition()
            ]);
        }

        $this->addFlash('danger', "Il n'y a pas d'édition du FOG prévu pour le moment.");
        return $this->redirectToRoute('index');
    }
}
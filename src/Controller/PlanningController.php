<?php

namespace App\Controller;

use App\Entity\Edition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends FOGController
{
    #[Route("/planning", name: "planning", methods: ['GET'])]
    public function planning(EntityManagerInterface $doctrine) : Response
    {
        $editionId = $this->FogParams->getCurrentEdition()->getId();
        if ($editionId != null) {
            return $this->render('oeilglauque/planning.html.twig', [
                'events' => $doctrine->getRepository(Edition::class)->find($editionId)->getEvents(),
                'edition' => $this->FogParams->getCurrentEdition()
            ]);
        }

        $this->addFlash('danger', "Il n'y a pas d'édition du FOG prévu pour le moment.");
        return $this->redirectToRoute('index');
    }
}
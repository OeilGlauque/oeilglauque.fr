<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends FOGController
{
    #[Route("/planning", name: "planning", methods: ['GET'])]
    public function planning(EventRepository $eventRepository) : Response
    {
        if ($this->FogParams->getCurrentEdition()->getId() != null) {
            return $this->render('oeilglauque/planning.html.twig', [
                'events' => $eventRepository->findByEdition($this->FogParams->getCurrentEdition()->getId()),
                'edition' => $this->FogParams->getCurrentEdition()
            ]);
        }

        $this->addFlash('danger', "Il n'y a pas d'édition du FOG prévu pour le moment.");
        return $this->redirectToRoute('index');
    }
}
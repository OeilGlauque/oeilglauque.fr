<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Edition;

class CustomController extends AbstractController {
    
    
    public function getCurrentEdition(): ?Edition {
        $edition = $this->getDoctrine()->getRepository(Edition::class)->findOneBy(['annee' => $this->getParameter('current_edition')]);
        return $edition ? $edition : new Edition();
    }
}

?>
<?php

namespace App\Controller;

use App\Entity\Poster;
use App\Form\PosterType;
use App\Repository\EditionRepository;
use App\Repository\PosterRepository;
use App\Service\FileUploader;
use App\Service\FOGParametersService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/poster')]
class PosterController extends FOGController
{

    public function __construct(FOGParametersService $FogParams)
    {
        parent::__construct($FogParams);
    }

    #[Route(name: 'app_poster_index', methods: ['GET'])]
    public function index(PosterRepository $posterRepository): Response
    {
        return $this->render('oeilglauque/poster/index.html.twig', [
            'posters' => $posterRepository->findAll(),
            'newHeader' => true
        ]);
    }

    #[Route('/new', name: 'app_poster_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FileUploader $uploader,EditionRepository $editionRepository, EntityManagerInterface $entityManager): Response
    {
        $poster = new Poster();
        $form = $this->createForm(PosterType::class, $poster, [
            'editions' => $editionRepository->findAll(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $selectedEdition = $poster->getEdition();
            if ($selectedEdition && $entityManager->getRepository(Poster::class)->findOneBy(['edition' => $selectedEdition])) {
                $this->addFlash('danger', "Cette édition est déjà utilisée par un autre poster.");
                return $this->redirectToRoute('app_poster_new');
            }

            /* Image FOG */
            $file = $form->get('fogImg')->getData();
            if ($file) {
                $filename = $uploader->upload($file, "posters", $poster->getTitle());
                if ($filename != "") {
                    $poster->setFogImg($filename);
                } else {
                    $this->addFlash('danger', "Erreur lors de l'upload de l'image Fog.");
                    return $this->redirectToRoute('app_poster_new');
                }
            }

            /* Image Concert */
            $file = $form->get('concertImg')->getData();
            if ($file) {
                $filename = $uploader->upload($file, "posters", "concert-{$poster->getTitle()}");
                if ($filename != "") {
                    $poster->setConcertImg($filename);
                } else {
                    $this->addFlash('danger', "Erreur lors de l'upload de l'image du concert.");
                    return $this->redirectToRoute('app_poster_new');
                }
            }

            $entityManager->persist($poster);
            $entityManager->flush();

            return $this->redirectToRoute('app_poster_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('oeilglauque/poster/new.html.twig', [
            'poster' => $poster,
            'form' => $form,
            'newHeader' => true
        ]);
    }

    #[Route('/{id}/edit', name: 'app_poster_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,EditionRepository $editionRepository, Poster $poster, FileUploader $uploader, EntityManagerInterface $entityManager): Response
    {
        $oldFogImg = $poster->getFogImg();
        $oldConcertImg = $poster->getConcertImg();

        $form = $this->createForm(PosterType::class, $poster, [
            'new' => false,
            'editions' => $editionRepository->findAll(),
            'edition' => $poster->getEdition(),
        ]);

        $form->setData($request);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $selectedEdition = $poster->getEdition();

            if ($selectedEdition && $entityManager->getRepository(Poster::class)->findOneBy(['edition' => $selectedEdition, 'id' => '!= ' . $poster->getId()])) {
                $this->addFlash('danger', "Cette édition est déjà utilisée par un autre poster.");
                return $this->redirectToRoute('app_poster_edit', ['id' => $poster->getId()]);
            }

            $fogImg = $form->get('fogImg')->getData();
            $concertImg = $form->get('concertImg')->getData();

            if ($fogImg != null){
                $uploader->remove($oldFogImg);

                $filename = $uploader->upload($fogImg, "posters", $poster->getTitle());
                if($filename != ""){
                    $poster->setFogImg($filename);
                }else{
                    $this->addFlash('danger', "Erreur lors de l'upload de l'image du FOG.");
                    return $this->redirectToRoute('app_poster_edit', ['id' => $poster->getId()]);
                }
                $entityManager->flush();
            }

            if ($concertImg != null){
                $uploader->remove($oldConcertImg);

                $filename = $uploader->upload($concertImg, "posters", "concert-{$poster->getTitle()}");
                if($filename != ""){
                    $poster->setConcertImg($filename);
                }else{
                    $this->addFlash('danger', "Erreur lors de l'upload de l'image du concert.");
                    return $this->redirectToRoute('app_poster_edit', ['id' => $poster->getId()]);
                }
                $entityManager->flush();
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_poster_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('oeilglauque/poster/edit.html.twig', [
            'poster' => $poster,
            'form' => $form,
            'newHeader' => true
        ]);
    }

    #[Route('/{id}', name: 'app_poster_delete', methods: ['POST'])]
    public function delete(Request $request,FileUploader $uploader, Poster $poster, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$poster->getId(), $request->getPayload()->getString('_token'))) {
            $fogImgFilename = $poster->getFogImg();
            $concertImgFilename = $poster->getConcertImg();

            if ($fogImgFilename != null) {
                $uploader->remove($fogImgFilename);
            }

            if ($concertImgFilename != null) {
                $uploader->remove($concertImgFilename);
            }

            $entityManager->remove($poster);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_poster_index', [], Response::HTTP_SEE_OTHER);
    }
}

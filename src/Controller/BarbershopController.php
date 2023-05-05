<?php

namespace App\Controller;

use App\Entity\Barbershop;
use App\Form\BarbershopType;
use App\Entity\BarbershopPics;
use App\Service\PictureService;
use App\HttpClient\NominatimHttpClient;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BarbershopController extends AbstractController
{
    #[Route('/barbershop', name: 'app_barbershop')]
    public function index(): Response
    {
        return $this->render('barbershop/index.html.twig', [
            'controller_name' => 'BarbershopController',
        ]);
    }

    #[Route('/barbershop/add', name: 'add_barbershop')]
    public function add(ManagerRegistry $doctrine, Barbershop $barbershop = null, Request $request, PictureService $pictureService, NominatimHttpClient $nominatim) : Response
    {
        $form = $this->createForm(BarbershopType::class, $barbershop);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $barbershop = $form->getData();

            // RECUPERER LA LONGITUDE ET LA LATITUDE

            // On récupere les données du formulaire
            // Urlencode pour que l'adresse soit formatée pour être valide dans une URL (car elle vas passer dans l'API)
            $adresse = urlencode($form->get('adresse')->getData());
            $ville = urlencode($form->get('ville')->getData());
            $cp = $form->get('cp')->getData();

            // On passe les données a nominatim
            $coordinates = $nominatim->getCoordinates($adresse,$ville,$cp);

            // 
            dd($coordinates);

            // RECUPERER LES IMAGES
            $image = $form->get('images')->getData();

            // On définit le dossier de destination
            $folder = 'barbershopPics';
            // On appelle le service d'ajout 
            $fichier = $pictureService->add($image, $folder, 300, 300);

            $img = new BarbershopPics();
            $img->setNom($fichier);
            $barbershop->addBarbershopPic($img);

            // ON ENVOIE LES DONNEES DANS LA BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($barbershop);
            $entityManager->flush();

            return $this->redirectToRout('app_barbershop');
        }

        return $this->render('barbershop/add.html.twig', [
            'formAddBarbershop' => $form->createView()
        ]);
    }

    #[Route('/barbershop/{id}', name: 'show_barbershop')]
    public function show(Barbershop $barbershop) : Response
    {
        return $this->render('barbershop/show.html.twig', []);
    }
}



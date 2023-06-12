<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RendezVousController extends AbstractController
{
    #[Route('/rendezvous', name: 'app_rendezvous')]
    public function index(): Response
    {
        return $this->render('rendez_vous/index.html.twig', [
            'controller_name' => 'RendezVousController',
        ]);
    }

    #[Route('/rendezvous/add', name: 'add_rendezvous')]
    #[Route('/rendezvous/{id}/edit', name: 'edit_rendezvous')]
    public function add(ManagerRegistry $doctrine, RendezVous $rendezvous = null, Request $request) : Response
    {   
        if(!$rendezvous){
            $rendezvous = new RendezVous();
        }

        $form = $this->createForm(RendezVousType::class, $rendezvous);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $rendezvous = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($rendezvous);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvous');
        }

        return $this->render('rendezvous/add.html.twig', [
            'formAddRendezVous' => $form->createView(),
        ]);
    }
}

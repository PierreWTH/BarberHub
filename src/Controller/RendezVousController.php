<?php

namespace App\Controller;

use App\Entity\Barbershop;
use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Entity\BarberPrestation;
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

    #[Route('/barbershop/{barbershop}/prestation/{barberprestation}/rendezvous/add', name: 'add_rendezvous')]
    #[Route('/barbershop/{barbershop}/rendezvous/{id}/edit', name: 'edit_rendezvous')]
    public function add(ManagerRegistry $doctrine, Barbershop $barbershop, BarberPrestation $barberPrestation, RendezVous $rendezvous = null, Request $request) : Response
    {   
        if(!$rendezvous){
            $rendezvous = new RendezVous();
        }

        $form = $this->createForm(RendezVousType::class, $rendezvous, [
        ]);
        $form->handleRequest($request);

        $personnel = $barbershop->getPersonnels();

        if($form->isSubmitted() && $form->isValid())
        {   
            
            $rendezvous = $form->getData();

            // Ajout de la prestation
            $rendezvous->addBarberPrestation($barberPrestation);
            // Ajout du barbershop
            $rendezvous->setBarbershop($barbershop);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($rendezvous);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvous');
        }

        return $this->render('rendezvous/add.html.twig', [
            'formAddRendezVous' => $form->createView(),
            'barbershop' => $barbershop,
            'prestation' => $barberPrestation,
            'personnels' => $personnel
        ]);
    }
}

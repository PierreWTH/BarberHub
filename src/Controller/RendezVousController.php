<?php

namespace App\Controller;

use DateInterval;
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

    #[Route('/barbershop/{barberPrestation}/rendezvous/add', name: 'add_rendezvous')]
    #[Route('/barbershop/rendezvous/{id}/edit', name: 'edit_rendezvous')]
    public function add(ManagerRegistry $doctrine, BarberPrestation $barberPrestation, RendezVous $rendezvous = null, Request $request) : Response
    {   
        if(!$rendezvous){
            $rendezvous = new RendezVous();
        }

        $barbershop = $barberPrestation->getBarbershop();
        $personnel = $barbershop->getPersonnels();
        $barbershopId = $barbershop->getId();
        $horaires = $barbershop->getHoraires();
        
        // Afficher les créneaux horaires
        $plagesHoraires = [];
        $heureDebut = 8; // A remplacer par le vrai horaire
        $heureFin = 18; // A remplacer par le vrai horaire

        $heure = $heureDebut;
        $minute = 0;

        while ($heure <= $heureFin) {
            $heureDebutFormat = sprintf('%02d:%02d', $heure, $minute);
            $heureFinFormat = sprintf('%02d:%02d', $heure, $minute + 30);
            $plage = $heureDebutFormat . ' - ' . $heureFinFormat;
            $plagesHoraires[$plage] = $plage;

            // Ajouter 1 à l'heure suivante
            $heure = ($minute === 30) ? $heure + 1 : $heure;
            $minute = ($minute === 60) ? 0 : 30;
        }

        var_dump($plagesHoraires);

        // Création du form avec envoi de $barbershopId au form builder
        $form = $this->createForm(RendezVousType::class, $rendezvous, ['barbershopId' => $barbershopId, 'plageHoraires' => $plagesHoraires
        ]);
        $form->handleRequest($request);

       
        

        if($form->isSubmitted() && $form->isValid())
        {   
            
            $rendezvous = $form->getData();

            // Ajout de la prestation
            $rendezvous->addBarberPrestation($barberPrestation);
            
            // Ajout du User
            $user = $this->getUser();
            $rendezvous->setUser($user);

            // Début de la prestation

            // Fin de la prestation

            $entityManager = $doctrine->getManager();
            $entityManager->persist($rendezvous);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvous');
        }

        return $this->render('rendezvous/add.html.twig', [
            'formAddRendezVous' => $form->createView(),
            'barbershop' => $barbershop,
            'prestation' => $barberPrestation,
            'horaires' => $horaires
        ]);
    }
}

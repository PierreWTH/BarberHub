<?php

namespace App\Controller;

use DateInterval;
use DateTimeImmutable;
use App\Entity\Barbershop;
use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Entity\BarberPrestation;
use App\Repository\RendezVousRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    #[IsGranted('ROLE_USER')]
    public function add(ManagerRegistry $doctrine, BarberPrestation $barberPrestation, RendezVous $rendezvous = null, Request $request, RendezVousRepository $rvr) : Response
    {   
        if(!$rendezvous){
            $rendezvous = new RendezVous();
        }

        $barbershop = $barberPrestation->getBarbershop();
        $personnel = $barbershop->getPersonnels();
        $barbershopId = $barbershop->getId();
        $horaires = $barbershop->getHoraires();

        // Création du form avec envoi de $barbershopId au form builder
        $form = $this->createForm(RendezVousType::class, $rendezvous, ['barbershopId' => $barbershopId]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   
            $rendezvous = $form->getData();

            // Récupération de début
            $debut = $form->get('debut')->getData();
            var_dump($debut);

            date_default_timezone_set('Europe/Paris');

            // Conversion en datetim pour début
            $heureDebut = $dateTime = new DateTimeImmutable($debut);

            // Récupération de personnel ID et heure début en string pour requete checkIfRdvExist
            $personnelId = $form->get('personnel')->getData()->getId();
            $stringDebut = $heureDebut->format('Y-m-d H:i:s');
            dd($stringDebut);
            
            // On verifie si le RDV existe
            $alreadyExist = $rvr->checkIfRdvExist($stringDebut, $personnelId);

            // Message d'erreur si le RDV existe déja 
            if($alreadyExist){
                echo 'Ce créneaux a déja été reservé';
                die();
            }
    
            // Rajout de 30min pour heure de fin
            $heureFin = $heureDebut->modify('+30 minutes');

            // Set heure de fin et heure de début
            $rendezvous->setDebut($heureDebut);
            $rendezvous->setFin($heureFin);

            // Ajout de la prestation
            $rendezvous->addBarberPrestation($barberPrestation);
            
            // Ajout du User
            $user = $this->getUser();
            $rendezvous->setUser($user);

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

<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/monespace', name: 'app_myspace')]
    public function index(UserRepository $ur): Response
    {
        // Récuperer les likes de l'utilisateur

        $user = $this->getUser();
        $userId = $this->getUser()->getId();
        $likes = $ur->getLikedBarbershops($userId);

        $upcomingRdvs = $ur->getUpcomingRendezVous($user);
        $pastRdvs = $ur->getPastRendezVous($user);

        return $this->render('user/myspace.html.twig', [
            'likes' => $likes,
            'upcomingRdvs' => $upcomingRdvs,
            'pastRdvs' => $pastRdvs
        ]);
        
    }

    #[Route('/monespace/rdv', name: 'app_myrdv')]
    public function displayRendezVous(UserRepository $ur): Response
    {
        $user = $this->getUser();

        $events = $user->getPersonnel()->getRendezVouses();
        

        $rdvs = [];        
        // Boucle sur chaque rdv
        foreach($events as $event){
             $prestations = $event->getBarberPrestation();

            // Boucle sur chaque collection de prestation
            foreach($prestations as $prestation){
            $rdvs[] = [
                'id'=> $event->getId(),
                'start' => $event->getDebut()->format('Y-m-d H:i:s'),
                'end' => $event->getFin()->format('Y-m-d H:i:s'),
                'title' => $event->getUser()->getPseudo() . " - " .$prestation->getPrestation()->getNom(),

            ];
            }
        }

        $data = json_encode($rdvs);

        return $this->render('user/rdv.html.twig', compact('data'));        
    }


}

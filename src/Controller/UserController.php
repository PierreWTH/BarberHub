<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\PersonnelRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER")'))]
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

    #[Route('/manage', name: 'manage_barbershop')]
    public function indexBarber(UserRepository $ur): Response
    {   

        $user = $this->getUser();
        $barbershop = $user->getPersonnel()->getBarbershop();
        $avis = $barbershop->getAvis();
        $personnels = $barbershop->getPersonnels();
        $personnel = $user->getPersonnel();
        $rdvs = $personnel->getRendezVouses();



        return $this->render('user/manageBarbershop.html.twig', [
            "personnels" => $personnels,
            "rdvs" => $rdvs,
            "avis" => $avis,
            "barbershop" => $barbershop
            

        ]);
        
    }


    #[Route('/monespace/rdv', name: 'app_myrdv')]
    public function displayRendezVous(UserRepository $ur, PersonnelRepository $pr, Request $request): Response
    {
        $user = $this->getUser();
        $personnel = $user->getPersonnel();
        // affichage des rendez vous a venir par défaut
        
        $events = $pr->getUpcomingRendezVous($personnel);
       
        $rdvs = [];        
        // Boucle sur chaque rdv
        foreach($events as $event){
             $prestations = $event->getBarberPrestation();

            // Boucle sur chaque collection de prestation
            foreach($prestations as $prestation){
                // tableau avec toutes les infos
            $rdvs[] = [
                'id'=> $event->getId(),
                'start' => $event->getDebut()->format('Y-m-d H:i:s'),
                'end' => $event->getFin()->format('Y-m-d H:i:s'),
                'title' => $event->getUser()->getPseudo() . " - " .$prestation->getPrestation()->getNom(),

            ];
            }
        }
        // On le met en JSON et on l'envoie a la vue
        $data = json_encode($rdvs);

        return $this->render('user/rdv.html.twig', compact('data'));        

    }
    #[Route('/monespace/getrdv', name: 'app_getmyrdv', methods: "POST")]
    public function getRendezVous(UserRepository $ur, PersonnelRepository $pr, Request $request): Response
    {
        $user = $this->getUser();
        $personnel = $user->getPersonnel();
        // affichage des rendez vous a venir par défaut
        $display = $request->getContent(); 
        dump($display);

        if ($display === 'upcoming') {
            $events = $pr->getUpcomingRendezVous($personnel);
        } else {
            $events = $user->getPersonnel()->getRendezVouses();
        }
    
        $rdvs = [];        
        // Boucle sur chaque rdv
        foreach($events as $event){
             $prestations = $event->getBarberPrestation();

            // Boucle sur chaque collection de prestation
            foreach($prestations as $prestation){
                // tableau avec toutes les infos
            $rdvs[] = [
                'id'=> $event->getId(),
                'start' => $event->getDebut()->format('Y-m-d H:i:s'),
                'end' => $event->getFin()->format('Y-m-d H:i:s'),
                'title' => $event->getUser()->getPseudo() . " - " .$prestation->getPrestation()->getNom(),

            ];
            }
        }
        return $this->json($rdvs);
    }

    #[Route('monespace/delete', name: 'delete_account')]
    public function delete(ManagerRegistry $doctrine, Request $request, Security $security): Response
    {   

        $user = $this->getUser();

        if ($user){
            $entityManager = $doctrine->getManager();

            $allAvis = $user->getAvis();

            foreach($allAvis as $avis){
                $avis->setUser(null);
            }

            if($security->isGranted('ROLE_ADMIN')){
                $articles = $user->getArticles();

                foreach($articles as $article){
                    $article->setUser(null);
                }
            }

            $entityManager->remove($user);
            $entityManager->flush();

            
            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);
            
            notyf()
            ->position('x', 'right')
            ->position('y', 'bottom')
            ->addError('Votre compte à bien été supprimé.');
        }
        
        return $this->redirectToRoute('app_home');
    }


}

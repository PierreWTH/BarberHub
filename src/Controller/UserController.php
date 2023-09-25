<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Barbershop;
use App\Form\UpdateUserType;
use App\Form\AddEmployeeType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Repository\PersonnelRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER")'))]
class UserController extends AbstractController
{
    #[Route('/monespace', name: 'app_myspace')]
    public function index(UserRepository $ur): Response
    {
        // Si l'utilisateur n'est pas connecté on redirige vers login 
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
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
    #[isGranted('ROLE_BARBER')]
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

    #[Route('/{slug}/employes', name: 'manage_employees')]
    #[isGranted('ROLE_BARBER')]
    public function manageEmployees(Barbershop $barbershop, UserRepository $userRepository, Request $request, MailerInterface $mailer): Response
    {   
        $form = $this->createForm(AddEmployeeType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $email = $form->get('email')->getData();

            $newEmployee = $userRepository->findOneBy(['email' => $email]);
            
            $newEmployeeId = $newEmployee->getId();

            $tokenLength = 32; // La longueur du token que vous souhaitez générer
            $token = bin2hex(random_bytes($tokenLength));

            $newEmployee->setPersonnelToken($token);

            // dd($token);

            $email = (new Email())
            ->from('admin@barberhub.com')
            ->to($email)
            ->subject($user->getPseudo().' vous invite à rejoindre son salon.')

            ->html("<a href='/addEmployee/confirmation/?token=$token&user=$newEmployeeId'> J'accepte </a>");

            $mailer->send($email);

            notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addSuccess('Demande envoyée.');
    
            return $this->redirectToRoute('manage_employees',['slug' => $barbershop->getSlug()]);
        }

        $personnels = $barbershop->getPersonnels();

        return $this->render('user/manageEmployees.html.twig', [
            'personnel' => $personnels,
            'formAddEmployee' => $form->createView(),

        ]);
        
    }

    #[Route('/{slug}/confirmEmployee', name: 'confirm_employees')]
    public function confirmEmployee(Barbershop $barbershop, Request $request, UserRepository $userRepository): Response
    {
        $token = $_GET['token'];
        $userId = $_GET['user'];

        dd($token, $userId);

        $newEmployee = $userRepository->findOneBy(['id' => $userId]);

        if($newEmployee->getToken() === $token){


        }
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
    #[isGranted('ROLE_BARBER')]
    public function getRendezVous(UserRepository $ur, PersonnelRepository $pr, Request $request): Response
    {
        $user = $this->getUser();
        $personnel = $user->getPersonnel();
        // affichage des rendez vous a venir par défaut
        $display = $request->getContent(); 

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

    #[Route('monespace/profil', name: 'manage_account')]
    public function updateUser(ManagerRegistry $doctrine, Request $request, Security $security, UserPasswordHasherInterface $passwordHasher): Response
    {   

        $user = $this->getUser();

        $form = $this->createForm(UpdateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($data);

            // On récupere les input password
            $old_pwd = $form->get('old_password')->getData();
            $new_pwd = $form->get('new_password')->getData();
            $new_pwd_confirm = $form->get('new_password_confirm')->getData();

            // Si ils ne sont pas null
            if($old_pwd !== null && $new_pwd !== null && $new_pwd_confirm !== null){

                if($new_pwd !== $new_pwd_confirm){
                    notyf()
                    ->position('x', 'right')
                    ->position('y', 'bottom')
                    ->addError('Confirmation de mot de passe incorrecte.');
                    return $this->redirectToRoute("manage_account");
                }
                
                // On vérifier que l'ancien mot de passe correspond bien 
                $checkPass = $passwordHasher->isPasswordValid($user, $old_pwd);
                
                // Si c'est bon, on hash le nouveau password et on le met en BDD
                if($checkPass === true) {
                        $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $new_pwd_confirm );
                    
                    $user->setPassword($hashedPassword);
                }
                // Sinon, notif comme quoi les MDP ne matchent pas
                else{
                    notyf()
                    ->position('x', 'right')
                    ->position('y', 'bottom')
                    ->addError('L\'ancien mot de passe ne corresponds pas.');
                    return $this->redirectToRoute("manage_account");
                }
            }
            // Si tous les champs ne sont pas remplis
            if($old_pwd !== null || $new_pwd !== null || $new_pwd_confirm || null){
                notyf()
                    ->position('x', 'right')
                    ->position('y', 'bottom')
                    ->addError('Merci de remplir tous les champs.');
                    return $this->redirectToRoute("manage_account");
            }

            $entityManager->flush();

            notyf()
            ->position('x', 'right')
            ->position('y', 'bottom')
            ->addSuccess('Informations mises à jour.');

            return $this->redirectToRoute('manage_account');
        }

        return $this->render('user/updateProfil.html.twig', [
            "user" => $user,
            'formUpdateUser' => $form->createView(),
        ]);
    }

}

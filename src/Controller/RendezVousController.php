<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use DateTimeZone;
use DateTimeImmutable;
use IntlDateFormatter;
use App\Entity\Personnel;
use App\Entity\Barbershop;
use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Entity\BarberPrestation;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Repository\PersonnelRepository;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER") or is_granted("ROLE_BARBER")'))]
class RendezVousController extends AbstractController
{
    // Ajouter un rendez vous
    #[Route('/barbershop/{slug}/rendezvous/{barberPrestation}/add', name: 'add_rendezvous')]
    #[Route('/barbershop/rendezvous/{id}/edit', name: 'edit_rendezvous')]
    public function addRdv(ManagerRegistry $doctrine, BarberPrestation $barberPrestation, RendezVous $rendezvous = null, Request $request, RendezVousRepository $rvr, MailerInterface $mailer, Barbershop $barbershop) : Response
    {   
        setlocale(LC_TIME, 'fr_FR');

        // On verifie si la prestation choisie est bien proposée par le barbier
        if(!in_array($barberPrestation, $barbershop->getBarberPrestations()->toArray())){

            return $this->redirectToRoute('app_home');
        }

        if(!$rendezvous){
            $rendezvous = new RendezVous();
        }

        $barbershop = $barberPrestation->getBarbershop();
        $personnels = $barbershop->getPersonnels();
        $barbershopId = $barbershop->getId();
        $horaires = $barbershop->getHoraires();

        // Création du form avec envoi de $barbershopId au form builder

        
        $form = $this->createForm(RendezVousType::class, $rendezvous, ['barbershopId' => $barbershopId]);

        if($this->getUser()->getTelephone()){
            $form->remove('nom');
            $form->remove('prenom');
            $form->remove('telephone');
        }
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   
            $rendezvous = $form->getData();

            // Récupération de début
            $debut = $form->get('debut')->getData();

            
            if(!$debut){
                notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addSuccess('Heure de début invalide.');
                return $this->redirectToRoute('add_rendezvous',['id' => $barbershopId, 'barberPrestation' => $barberPrestation->getId()]);
            }
            // Conversion en datetime pour début
            $heureDebut = new DateTimeImmutable($debut);

            // Récupération de personnel ID et heure début en string pour requete checkIfRdvExist
            $personnel = $form->get('personnel')->getData();

            if(!in_array($personnel, $personnels->toArray())){

                return $this->redirectToRoute('app_home');
            }

            $personnelId = $form->get('personnel')->getData()->getId();
            $personnelName = $form->get('personnel')->getData()->getUser()->getPseudo();
            $stringDebut = $heureDebut->format('Y-m-d H:i:s');

            // VERIF SI LE RDV EST PRIS APRES L'HEURE ET LE JOUR D'AUJOURD'HUI
            $today = new DateTime();

            if($heureDebut->getTimestamp() <= $today->getTimestamp()){
                notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addError('Le rendez vous ne peux pas être prix avant le jour ou l\'heure actuelle.');
    
                return $this->redirectToRoute('add_rendezvous',['id' => $barbershopId, 'barberPrestation' => $barberPrestation->getId()]);
            }

            // VERIF SI RDV PRIS UN JOUR DE FERMETURE

            // Récupération du jour en français
            setlocale(LC_TIME, 'fr_FR.UTF-8');
            $jour = strtolower(strftime('%A', $heureDebut->getTimestamp()));
            $horairesArray = json_decode($horaires, true);

            
            if($horairesArray[$jour]['ouverture'] == 'ferme' || $horairesArray[$jour]['fermeture'] == 'ferme' ){
                notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addError('Le barbershop est fermé ce jour la.');
                return $this->redirectToRoute('add_rendezvous',['id' => $barbershopId, 'barberPrestation' => $barberPrestation->getId()]);
            }

            // VERIF SI LE RDV EST BIEN PRIS PENDANT LES HEURES D'OUVERTURE
            $ouvertureBS = new DateTime($horairesArray[$jour]['ouverture']);
            $fermetureBS = new DateTime($horairesArray[$jour]['fermeture']);

            if($heureDebut->format('H:i:s')  < $ouvertureBS->format('H:i:s')  || $heureDebut->format('H:i:s')  > $fermetureBS->format('H:i:s') )
            {
                notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addError('Vous ne pouvez pas prendre rendez vous en dehors des heures d\'ouverture.');
                return $this->redirectToRoute('add_rendezvous',['id' => $barbershopId, 'barberPrestation' => $barberPrestation->getId()]);
            }
            
            // VERIF SI LE RDV EXISTE DEJA 
            $alreadyExist = $rvr->checkIfRdvExist($stringDebut, $personnelId);

            // Message d'erreur si le RDV existe déja 
            if($alreadyExist){
                notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addError('Ce créneaux est indisponible.');
                return $this->redirectToRoute('add_rendezvous',['id' => $barbershopId, 'barberPrestation' => $barberPrestation->getId()]);
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

            if(!$user->getNom() && !$user->getPrenom() && !$user->getTelephone()){

                $nom = $form->get('nom')->getData();
                $prenom = $form->get('prenom')->getData();
                $telephone = $form->get('telephone')->getData();

                $user->setPrenom($prenom);
                $user->setNom($nom);
                $user->setTelephone($telephone);

            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($rendezvous);
            
            $entityManager->flush();

            // Envoie du mail de confirmation 
        
            $email = (new Email())
            ->from('admin@barberhub.com')
            ->to($user->getEmail())
            ->subject('Votre rendez vous chez '. $barbershop->getNom().'.')

            ->html( '<p>Cher '.$user->getPseudo().',</p> <p>Nous sommes ravis de vous confirmer votre rendez-vous chez '.$barbershop->getNom().'</p> 
            <p> Voici les détails de votre rendez-vous : </p>
            
            <p> Date : '.$heureDebut->format('d/m/Y').'</p>
            <p> Créneau : de '.$heureDebut->format('H:i').'h à '.$heureFin->format('H:i').'h</p>
            <p> Prestation : '.$barberPrestation->getPrestation()->getNom().'.</p> 
            <p> Barbier : '.$personnelName.'
            
            <p> Nous vous remercions de votre confiance et nous sommes impatients de vous accueillir, </p>
            <p> Cordialement, </p>
            <p> L\'équipe '. $barbershop->getNom());


            $mailer->send($email);

            // Jeton de session pour indiquer que le user vient de prendre un RDV ( pour page confirm ) 
            $_SESSION['justBookedRdv'] = true;

            notyf()
            ->position('x', 'right')
            ->position('y', 'bottom')
            ->addSuccess('Rendez-Vous ajouté.');

            return $this->redirectToRoute('app_rendezvous_confirm');
        }        

        return $this->render('rendezvous/add.html.twig', [
            'formAddRendezVous' => $form->createView(),
            'barbershop' => $barbershop,
            'barberPrestation' => $barberPrestation,
            'horaires' => $horaires,
        ]);
    }

    // Générer les créneaux en fonction du barbier en AJAX
    #[Route('/getCreneauxbyPersonnel/{personnelId}', name: 'app_getCreneaux', methods:'post')]
    public function getCreneauxByPersonnel(EntityManagerInterface $entityManager, Request $request): Response
    {   
        setlocale(LC_TIME, 'fr_FR.UTF-8');

        // On récupère les données envoyée par la requete sous forme d'array
        $data = json_decode($request->getContent(), true);
        // Dans le tableau data on récupère personnel ID
        $personnelId = $data['personnel_id'];

        // On récupère l'employé qui corresponds au personnel ID récupéré
        $repository = $entityManager->getRepository(Personnel::class);
        $personnel = $repository->find($personnelId);

        // On récupère le barbier et ses horaires
        $barbershop = $personnel->getBarbershop();
        $horaires = json_decode($barbershop->getHoraires(), true);

        // On récupère les rendez vous déja résérvés de l'employé
        $rdvsPersonnel = $personnel->getRendezvouses();
        
        // On stocke tous les rdv déja pris dans un tableau 
        $unavailableRdvs = [];
        foreach($rdvsPersonnel as $rdvPersonnel){
            $unavailableRdvs[] = $rdvPersonnel->getDebut();
        }
         // On ajoute au tableau des RDV déja pris tous les créneaux avant l'heure actuelle
         $currentHour = new \DateTime('now +2hours');

         $currentDay = strtolower(strftime('%A', $currentHour->getTimestamp()));
         $startCurrentDayHour = $horaires[$currentDay]['ouverture'];
         $closeCurrentDayHour = $horaires[$currentDay]['fermeture'];

        if($startCurrentDayHour != 'ferme' | $closeCurrentDayHour != 'ferme'){

        $startHourDateTime = DateTime::createFromFormat('H:i', $startCurrentDayHour);
        $closeHourDateTime = DateTime::createFromFormat('H:i', $closeCurrentDayHour);


        
            while($startHourDateTime <= $currentHour && $startHourDateTime <= $closeHourDateTime){

                $unavailableRdvs[] = clone $startHourDateTime;
                $startHourDateTime->modify('+30 minutes');
                
            }
        }
        
        //Plage de temps pour generer les créneaux 
        $todayDate = new DateTime('now');
        $endDate = (new DateTime('now'))->modify('+1 month');

        // On génère un tableau avec tous les créneaux de RDV pour le mois à venir
        $allCreneaux = [];
        while($todayDate <= $endDate){
            // On récupère le jour de la semaine
            $jourDeLaSemaine = strtolower(strftime('%A', $todayDate->getTimestamp()));
            
            // On récupère les heures d'ouverture et de fermeture du barbier
            $heureOuvertureBarber = $horaires[$jourDeLaSemaine]['ouverture'];
            $heureFermetureBarber = $horaires[$jourDeLaSemaine]['fermeture'];
            
            // Si le barbier est fermé on n'ajoute rien au tableau de créneau
            if($heureOuvertureBarber != 'ferme' | $heureFermetureBarber != 'ferme'){
                $heureOuvertureObj = DateTime::createFromFormat('H:i', $heureOuvertureBarber);
                $heureFermetureObj = DateTime::createFromFormat('H:i', $heureFermetureBarber);
                
                $creneaux = [];
                
                while($heureOuvertureObj <= $heureFermetureObj){
                    
                    $creneauDateTime = clone $todayDate;
                    $creneauDateTime->setTime(
                    $heureOuvertureObj->format('H'),
                    $heureOuvertureObj->format('i')
                    );

                    $creneaux[] = $creneauDateTime;
                    $heureOuvertureObj->modify('+30 minutes');
                }
                
                $allCreneaux[$todayDate->format('Y-m-d')] = $creneaux;
            }   
            $todayDate->modify('+1 day');
        }
        
        // On retire du tableau les rendez-vous déja réservés
        foreach($unavailableRdvs as $unavailableRdv){

            foreach ($allCreneaux as $date => $dayCreneaux) {
                // On regarde si il y a une clé ou unavailableRdv et DayCreneau sont identiques
                $key = array_search($unavailableRdv, $dayCreneaux);
                // Si la clé existe, on enleve le créneau
                if ($key !== false) {
                    unset($allCreneaux[$date][$key]);
                } 
                
            }
        }

        // Si un tableau de créneaux est vide, on l'enleve
        foreach ($allCreneaux as $date => $dayCreneaux) {

            if (empty($dayCreneaux)) {
                        
                unset($allCreneaux[$date]);
            }
        }
        
        return $this->render('rendezvous/_creneauxbyBarber.html.twig', ['allCreneaux' => $allCreneaux]);
    }


    #[Route('/rendezvous/confirmation', name: 'app_rendezvous_confirm')]
    public function confirmRdv(UserRepository $ur): Response
    {
        $user = $this->getUser();

        // Si le jeton de session hasRendezVous n'est pas défini ou n'est pas a true
        if (!isset($_SESSION['justBookedRdv']) || $_SESSION['justBookedRdv'] !== true) {

            return $this->redirectToRoute('app_home');
        }

        $lastRDV = $ur->getLastRendezVous($user);

        // Une fois la page consultée on repasse le jeton a true
        //$_SESSION['justBookedRdv'] = false;
        
        return $this->render('rendezvous/confirm.html.twig', [
            'lastRDV' => $lastRDV,
        ]);
    }

    // Supprimer un rendez-vous
    #[Route('/rendezvous/{id}/delete', name: 'delete_rdv')]
    public function deleteRdv(ManagerRegistry $doctrine,  RendezVous $rendezVous = null): Response
    {
        if ($rendezVous){
            $entityManager = $doctrine->getManager();
            $entityManager->remove($rendezVous);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_myspace');
    }
}

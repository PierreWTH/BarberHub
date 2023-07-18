<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use DateTimeImmutable;
use IntlDateFormatter;
use App\Entity\Barbershop;
use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Entity\BarberPrestation;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Repository\RendezVousRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RendezVousController extends AbstractController
{
    #[Route('/rendezvous', name: 'app_rendezvous')]
    public function index(): Response
    {
        return $this->render('rendezvous/index.html.twig', [
            'controller_name' => 'RendezVousController',
        ]);
    }

    #[Route('/barbershop/{barberPrestation}/rendezvous/add', name: 'add_rendezvous')]
    #[Route('/barbershop/rendezvous/{id}/edit', name: 'edit_rendezvous')]
    public function add(ManagerRegistry $doctrine, BarberPrestation $barberPrestation, RendezVous $rendezvous = null, Request $request, RendezVousRepository $rvr, MailerInterface $mailer) : Response
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

            if(!$debut){
                echo 'Heure du rendez vous invalide.';
                die();
            }

            // Conversion en datetime pour début
            $heureDebut = new DateTimeImmutable($debut);

            // Récupération de personnel ID et heure début en string pour requete checkIfRdvExist
            $personnelId = $form->get('personnel')->getData()->getId();
            $personnelName = $form->get('personnel')->getData()->getPseudo();
            $stringDebut = $heureDebut->format('Y-m-d H:i:s');

            // VERIF SI LE RDV EST PRIS APRES L'HEURE ET LE JOUR D'AUJOURD'HUI
            $today = new DateTime();

            if($heureDebut->getTimestamp() <= $today->getTimestamp()){
                echo 'Le rendez vous ne peux pas être pris avant le jour actuel';
                die();
            }

            // VERIF SI RDV PRIS UN JOUR DE FERMETURE

            // Récupération du jour en français
            setlocale(LC_TIME, 'fr_FR.UTF-8');
            $jour = strtolower(strftime('%A', $heureDebut->getTimestamp()));
            $horairesArray = json_decode($horaires, true);

            
            if($horairesArray[$jour]['ouverture'] == 'ferme' || $horairesArray[$jour]['fermeture'] == 'ferme' ){
                echo 'Le barbershop est fermé ce jour la.';
                die();
            }

            // VERIF SI LE RDV EST BIEN PRIS PENDANT LES HEURES D'OUVERTURE
            $ouvertureBS = new DateTime($horairesArray[$jour]['ouverture']);
            $fermetureBS = new DateTime($horairesArray[$jour]['fermeture']);

            if($heureDebut->format('H:i:s')  < $ouvertureBS->format('H:i:s')  || $heureDebut->format('H:i:s')  > $fermetureBS->format('H:i:s') )
            {
                echo 'Vous ne pouvez pas prendre rendez-vous en dehors des heures d\'ouverture.';
                die();
            }
            
            // VERIF SI LE RDV EXISTE DEJA 
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

            // Envoie du mail de confirmation 

            $email = (new Email())
            ->from('admin@barberhub.com')
            ->to($user->getEmail())
            ->subject('Votre rendez vous chez '. $barbershop->getNom())

            ->html( '<p>Cher '.$user->getPseudo().',</p> <p>Nous sommes ravis de vous confirmer votre rendez-vous chez '.$barbershop->getNom().'</p> 
            <p> Voici les détails de votre rendez-vous : </p>
            
            <p> Date : '.$heureDebut->format('d/m/Y').'</p>
            <p> Heure : '.$heureDebut->format('H:i').'</p>
            <p> Prestation : dégradé.</p> 
            <p> Barbier : '.$personnelName.'
            
            <p> Nous vous remercions de votre confiance et nous sommes impatients de vous accueillir, </p>
            <p> Cordialement, </p>
            <p> L\'équipe'. $barbershop->getNom());


            $mailer->send($email);

            // Jeton de session pour indiquer que le user vient de prendre un RDV ( pour page confirm ) 
            $_SESSION['justBookedRdv'] = true;

            return $this->redirectToRoute('app_rendezvous_confirm');
        }        

        return $this->render('rendezvous/add.html.twig', [
            'formAddRendezVous' => $form->createView(),
            'barbershop' => $barbershop,
            'prestation' => $barberPrestation,
            'horaires' => $horaires
        ]);
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
        $_SESSION['justBookedRdv'] = false;
        
        return $this->render('rendezvous/confirm.html.twig', [
            'lastRDV' => $lastRDV,
        ]);
    }
}

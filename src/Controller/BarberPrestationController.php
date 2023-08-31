<?php

namespace App\Controller;

use App\Entity\Barbershop;
use App\Entity\BarberPrestation;
use App\Form\BarberPrestationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BarberPrestationController extends AbstractController
{
    #[Route('/barberprestation', name: 'app_barber_prestation')]
    public function index(): Response
    {
        return $this->render('barberPrestation/index.html.twig', [
            'controller_name' => 'BarberPrestationController',
        ]);
    }

    #[Route('barbershop/{id}/barberprestation/add', name: 'add_barberPrestation')]
    #[Route('barbershop/{barbershop}/barberprestation/{barberprestation}/edit', name: 'edit_barberPrestation')]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER")'))]
    public function add(ManagerRegistry $doctrine, Security $security, Barbershop $barbershop, BarberPrestation $barberPrestation = null, Request $request) : Response
    {   
        $barberPrestations = $doctrine->getRepository(BarberPrestation::class)->findAll();

        if(!$barberPrestation){
            $barberPrestation = new BarberPrestation();
        }

        $personnels = $barbershop->getPersonnels();
        $user = $this->getUser();

        $allPersonnel = [];
        foreach($personnels as $personnel){
            $persUser = $personnel->getUser()->getId();
            $allPersonnel[] = $persUser;
        }

        // et que personnel est vide ou que l'user ne travaille pas dans le barbershop
        if($personnels->isEmpty() || !in_array($user->getId(), $allPersonnel, true) || $user->getPersonnel()->isManager() === false)
        {
            throw new AccessDeniedException("Vous n'avez pas les droits nécessaires pour effectuer cette action.");
        }
        

        $form = $this->createForm(BarberPrestationType::class, $barberPrestation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   
            $barberPrestation->setBarbershop($barbershop);

            $barberPrestation = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($barberPrestation);
            
            $entityManager->flush();

            return $this->redirectToRoute('admin_barbershop');
        }
        return $this->render('barberPrestation/add.html.twig', [
            'formAddBarberPrestation' => $form->createView(),
        ]);
    }
}


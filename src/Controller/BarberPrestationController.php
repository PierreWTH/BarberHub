<?php

namespace App\Controller;

use App\Entity\Barbershop;
use App\Entity\BarberPrestation;
use App\Form\BarberPrestationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BarberPrestationController extends AbstractController
{

    #[Route('barbershop/{id}/barberprestation/manage', name: 'manage_barberPrestation')]
    #[Route('barbershop/{barbershop}/barberprestation/{id}/edit', name: 'edit_barberPrestation')]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_BARBER")'))]
    public function add(ManagerRegistry $doctrine, Security $security, Barbershop $barbershop, BarberPrestation $barberPrestation = null, Request $request) : Response
    {   
        $isEditMode = ($barberPrestation !== null);
        
        $barberPrestations = $doctrine->getRepository(BarberPrestation::class)->findPrestationByBarber($barbershop);
        
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

        if(!$security->isGranted("ROLE_ADMIN")){
            // et que personnel est vide ou que l'user ne travaille pas dans le barbershop
            if($personnels->isEmpty() || !in_array($user->getId(), $allPersonnel, true) || $user->getPersonnel()->isManager() === false)
            {
                throw new AccessDeniedException("Vous n'avez pas les droits nécessaires pour effectuer cette action.");
            }
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

            $notificationMessage = ($isEditMode) ? 'Prestation modifiée.' : 'Prestation ajoutée.';
            notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addSuccess($notificationMessage);

            return $this->redirectToRoute('manage_barberPrestation', ['id' => $barbershop->getId()]);
            
        }
        return $this->render('barberPrestation/add.html.twig', [
            'formAddBarberPrestation' => $form->createView(),
            'barberPrestations' => $barberPrestations
        ]);
    }


    // Supprimer une barber prestation
    #[Route('/barberprestation/{id}/delete', name: 'delete_barberprestation')]
    public function delete(ManagerRegistry $doctrine, BarberPrestation $barberPrestation = null): Response
    {   
        
        if ($barberPrestation){
            $barbershop = $barberPrestation->getBarbershop();
            $entityManager = $doctrine->getManager();
            $entityManager->remove($barberPrestation);
            $entityManager->flush();


            notyf()
            ->position('x', 'right')
            ->position('y', 'bottom')
            ->addError('Prestation supprimée.');
        }
        
        return $this->redirectToRoute('manage_barberPrestation', ['id' => $barbershop->getId()]);
    }
}


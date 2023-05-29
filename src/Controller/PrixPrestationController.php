<?php

namespace App\Controller;

use App\Entity\Barbershop;
use App\Entity\PrixPrestation;
use App\Form\PrixPrestationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PrixPrestationController extends AbstractController
{   

    #[Route('barbershop/{id}/prixprestation/add', name: 'add_prixprestation')]
    #[Route('barbershop/{barbershop}/prixprestation/{prixprestation}/edit', name: 'edit_prixprestation')]
    public function add(ManagerRegistry $doctrine, Security $security, Barbershop $barbershop, PrixPrestation $prixPrestation = null, Request $request) : Response
    {   
        if(!$prixPrestation){
            $prixPrestation = new PrixPrestation();
        }

        $personnel = $barbershop->getPersonnels();
        $user = $this->getUser();

        // Si l'user n'a pas le role admin
        if(!$security->isGranted('ROLE_ADMIN')){
        // et que personnel est vide ou que l'user ne travaille pas dans le barbershop
            if($personnel->isEmpty() || $personnel[0]->getUser()->getId() !== $user->getId())
            {
                throw new AccessDeniedException("Vous n'avez pas les droits nécessaires pour effectuer cette action.");
            }
        }
        
        $form = $this->createForm(PrixPrestationType::class, $prixPrestation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   
            $prixPrestation->setBarbershop($barbershop);

            $prixPrestation = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($prixPrestation);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_barbershop');
        }

        return $this->render('prix_prestation/add.html.twig', [
            'formAddPrixPrestation' => $form->createView(),
        ]);
    }

}

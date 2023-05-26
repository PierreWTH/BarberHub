<?php

namespace App\Controller;

use App\Entity\PrixPrestation;
use App\Form\PrixPrestationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PrixPrestationController extends AbstractController
{   

    #[Route('/prixprestation/add', name: 'add_prixprestation')]
    #[Route('/prixprestation/{id}/edit', name: 'edit_prixprestation')]
    public function add(ManagerRegistry $doctrine, PrixPrestation $prixPrestation = null, Request $request) : Response
    {   
        if(!$prixPrestation){
            $prixPrestation = new PrixPrestation();
        }

        $form = $this->createForm(PrixPrestationType::class, $prixPrestation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
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

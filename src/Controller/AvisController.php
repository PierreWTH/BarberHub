<?php

namespace App\Controller;


use DateTime;
use App\Entity\Avis;
use App\Form\AvisType;
use App\Entity\Barbershop;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AvisController extends AbstractController
{

    #[Route('/barbershop/{id}/avis/add', name: 'add_avis')]
    #[Route('/barbershop/{barbershop}/avis/{avis}/edit', name: 'edit_avis')]
    public function add(ManagerRegistry $doctrine, Barbershop $barbershop = null, Avis $avis = null, Request $request) : Response
    {   
        if(!$avis){
            $avis = new Avis();
        }

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $avis = $form->getData();

            // SET LA DATE DE CREATION
            $date = new DateTime();
            $avis->setDate($date);

            // SET USER
            $user = $this->getUser();
            $avis->setUser($user);

            //SET BARBERSHOP
            $avis->setBarbershop($barbershop);


            $entityManager = $doctrine->getManager();
            $entityManager->persist($avis);
            
            $entityManager->flush();

            return $this->redirectToRoute('show_barbershop',['id' => $barbershop->getId()]);
        }

        return $this->render('avis/add.html.twig', [
            'formAddAvis' => $form->createView(),
            'edit' => $avis->getId(),
        ]);
    }

    // Supprimer un avis
    #[Route('/barbershop/{id}/avis/{avisId}/delete', name: 'delete_avis')]
    public function delete(ManagerRegistry $doctrine, Barbershop $barbershop, Avis $avis = null): Response
    {   
        if ($avis){
            $entityManager = $doctrine->getManager();
            $entityManager->remove($avis);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('show_barbershop',['id' => $barbershop->getId()]);
    }
}

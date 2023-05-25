<?php

namespace App\Controller;

use App\Entity\Prestation;
use App\Form\PrestationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PrestationController extends AbstractController
{
    #[Route('/prestation', name: 'app_prestation')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $prestations = $doctrine->getRepository(Prestation::Class)->findBy([], ["nom"=>"ASC"]);
        return $this->render('prestation/index.html.twig', [
            'prestations' => $prestations,
        ]);
    }

    #[Route('/prestation/add', name: 'add_prestation')]
    #[Route('/prestation/{id}/edit', name: 'edit_prestation')]
    public function add(ManagerRegistry $doctrine, Prestation $prestation = null, Request $request) : Response
    {   
        if(!$prestation){
            $prestation = new Prestation();
        }

        $form = $this->createForm(PrestationType::class, $prestation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $prestation = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($prestation);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_prestation');
        }

        return $this->render('prestation/add.html.twig', [
            'formAddPrestation' => $form->createView(),
        ]);
    }

    // Supprimer un prestation
    #[Route('admin/prestation/{id}/delete', name: 'delete_prestation')]
    public function delete(ManagerRegistry $doctrine, Prestation $prestation = null): Response
    {   
        if ($prestation){
            $entityManager = $doctrine->getManager();
            $entityManager->remove($prestation);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_prestation');
    }
}

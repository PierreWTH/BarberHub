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
    #[Route('/prestations', name: 'app_prestation')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $prestations = $doctrine->getRepository(Prestation::Class)->findBy([], ["nom"=>"ASC"]);
        return $this->render('prestation/index.html.twig', [
            'prestations' => $prestations,
        ]);
    }

    #[Route('/prestation/manage', name: 'manage_prestations')]
    #[Route('/prestation/{id}/edit', name: 'edit_prestation')]
    #[IsGranted('ROLE_ADMIN')]
    public function manage(ManagerRegistry $doctrine, Prestation $prestation = null, Request $request): Response
    {   
        $prestations = $doctrine->getRepository(Prestation::class)->findBy([], ["nom" => "ASC"]);
        
        if (!$prestation) {
            $prestation = new Prestation();
        }

        $form = $this->createForm(PrestationType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prestation = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($prestation);
            $entityManager->flush();


            return $this->redirectToRoute('manage_prestations');
        }

        return $this->render('prestation/manage.html.twig', [
            'prestations' => $prestations,
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
        
        return $this->redirectToRoute('manage_prestations');
    }
}

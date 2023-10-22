<?php

namespace App\Controller;

use App\Entity\Prestation;
use App\Form\PrestationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
#[Route('/administration')]
class PrestationController extends AbstractController
{

    #[Route('/prestation/manage', name: 'manage_prestations')]
    #[Route('/prestation/{id}/edit', name: 'edit_prestation')]
    public function manage(ManagerRegistry $doctrine, Prestation $prestation = null, Request $request): Response
    {   
        $prestations = $doctrine->getRepository(Prestation::class)->findBy([], ["nom" => "ASC"]);
        
        $isEditMode = ($prestation !== null);

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

            $notificationMessage = ($isEditMode) ? 'Prestation modifiée.' : 'Prestation ajoutée.';
            notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addSuccess($notificationMessage);


            return $this->redirectToRoute('manage_prestations');
        }

        return $this->render('prestation/manage.html.twig', [
            'prestations' => $prestations,
            'formAddPrestation' => $form->createView(),
        ]);
    }

    // Supprimer un prestation
    #[Route('/prestation/{id}/delete', name: 'delete_prestation')]
    public function delete(ManagerRegistry $doctrine, Prestation $prestation = null): Response
    {   
        if ($prestation){
            $entityManager = $doctrine->getManager();
            $entityManager->remove($prestation);
            $entityManager->flush();

            notyf()
            ->position('x', 'right')
            ->position('y', 'bottom')
            ->addError('Prestation supprimée.');
        }
        
        return $this->redirectToRoute('manage_prestations');
    }
}

<?php

namespace App\Controller;

use App\Entity\Personnel;
use App\Form\PersonnelType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PersonnelController extends AbstractController
{
    #[Route('administration/personnel/manage', name: 'manage_personnel')]
    #[Route('administration/personnel/{id}/edit', name: 'edit_personnel')]
    #[IsGranted('ROLE_ADMIN')]
    public function manage(ManagerRegistry $doctrine, Personnel $personnel = null, Request $request): Response
    {   
        $personnels = $doctrine->getRepository(Personnel::class)->findAll();

        $isEditMode = ($personnel !== null);

        if (!$personnel) {
            $personnel = new Personnel();
        }

        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personnel = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($personnel);
            $entityManager->flush();

            $notificationMessage = ($isEditMode) ? 'Personnel modifié.' : 'Personnel ajouté.';
            notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addSuccess($notificationMessage);

            return $this->redirectToRoute('manage_personnel');
        }

        return $this->render('personnel/manage.html.twig', [
            'personnel' => $personnels,
            'formAddPersonnel' => $form->createView(),
        ]);
    }
}

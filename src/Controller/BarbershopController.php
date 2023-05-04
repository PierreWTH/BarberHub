<?php

namespace App\Controller;

use App\Entity\Barbershop;
use App\Form\BarbershopType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BarbershopController extends AbstractController
{
    #[Route('/barbershop', name: 'app_barbershop')]
    public function index(): Response
    {
        return $this->render('barbershop/index.html.twig', [
            'controller_name' => 'BarbershopController',
        ]);
    }

    #[Route('/barbershop/add', name: 'add_barbershop')]
    public function add(ManagerRegistry $doctrine, Barbershop $barbershop = null, Request $request) : Response
    {
        $form = $this->createForm(BarbershopType::class, $barbershop);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
                $barbershop = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($barbershop);
                $entityManager->flush();

                return $this->redirectToRout('app_barbershop');
        }

        return $this->render('barbershop/add.html.twig', [
            'formAddBarbershop' => $form->createView()
        ]);
    }

    #[Route('/barbershop/{id}', name: 'show_barbershop')]
    public function show(Barbershop $barbershop) : Response
    {
        return $this->render('barbershop/show.html.twig', []);
    }
}



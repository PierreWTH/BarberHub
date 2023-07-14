<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{

    #[Route('/administration', name: 'control_pannel')]
    #[IsGranted('ROLE_ADMIN')]
    public function controlPannel(): Response
    {
        return $this->render('security/admin/controlPannel.html.twig', []);
    }

    #[Route('/administration/barbershops', name: 'admin_barbershop')]
    #[IsGranted('ROLE_ADMIN')]
    public function allBarbershopAdmin(ManagerRegistry $doctrine): Response
    {
        $barbershops = $doctrine->getRepository(Barbershop::Class)->findBy([], ["creationDate"=>"DESC"]);

        return $this->render('security/admin/barbershops.html.twig', [
            'barbershops' => $barbershops,
        ]);
    }

    #[Route('/administration/users', name: 'admin_barbershop')]
    #[IsGranted('ROLE_ADMIN')]
    public function listUsers(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::Class)->findBy([], ["pseudo"=>"ASC"]);

        return $this->render('security/admin/users.html.twig', [
            'users' => $users,
        ]);
    }
}

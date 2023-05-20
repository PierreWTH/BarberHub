<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/monespace', name: 'app_myspace')]
    public function index(UserRepository $ur): Response
    {
        // Récuperer les likes de l'utilisateur

        $user = $this->getUser()->getId();
        $likes = $ur->getLikedBarbershops($user);

        return $this->render('user/myspace.html.twig', [
            'likes' => $likes
        ]);
    }
}

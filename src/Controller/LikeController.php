<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{   
    #[Route('/like/barbershop/{id}', name: 'like_post')]
    public function like(Barbershop $barbershop): Response
    {
        $user = $this->getUser();

        if($barbershop)
        return new Response();
    }
}

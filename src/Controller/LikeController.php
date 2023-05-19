<?php

namespace App\Controller;

use App\Entity\Barbershop;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LikeController extends AbstractController
{   
    #[Route('/like/barbershop/{id}', name: 'like_barbershop')]
    public function like(Barbershop $barbershop, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        // Si le barbershop a déja été liké par le User
        if($barbershop->isLikedByUser($user)){
            $barbershop->removeLike($user);
            $manager->flush();

            return $this->json(['message => Like supprimé.']);
        }

        // Si le barbershop n'as pas encore été liké par le user
        $barbershop->addLike($user);
        $manager->flush();


        return $this->json(['message => Like ajouté.']);
    }
}

<?php

namespace App\Controller;

use App\Entity\Barbershop;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER") or is_granted("ROLE_BARBER")'))]
class LikeController extends AbstractController
{   
    #[Route('/like/barbershop/{id}', name: 'like_barbershop', methods :['GET'])]
    public function like(Barbershop $barbershop, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        // Si le barbershop a déja été liké par le User
        if($barbershop->isLikedByUser($user)){
            $barbershop->removeLike($user);
            $manager->flush();

            return $this->json([
                'message' => 'Like supprimé.',
                'nbLike' => $barbershop->howManyLikes()
        ]);
        }

        // Si le barbershop n'as pas encore été liké par le user
        $barbershop->addLike($user);
        $manager->flush();


        return $this->json([
            'message => Like ajouté.',
            'nbLike' => $barbershop->howManyLikes()
        ]);
    }
}

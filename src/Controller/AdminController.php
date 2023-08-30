<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Personnel;
use App\Entity\Barbershop;
use App\Form\EditUserType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/administration')]
class AdminController extends AbstractController
{

    #[Route('/', name: 'control_pannel')]
    
    public function controlPannel(ManagerRegistry $doctrine): Response
    {   
        $nbBarbershops = count($doctrine->getRepository(Barbershop::Class)->findAll());
        $nbUsers = count($doctrine->getRepository(User::Class)->findAll());
        $nbArticles = count($doctrine->getRepository(Article::Class)->findAll());
        $nbPersonnel = count($doctrine->getRepository(Personnel::Class)->findAll());

        return $this->render('security/admin/controlPannel.html.twig', [
            'nbBarbershops' => $nbBarbershops,
            'nbUsers' =>$nbUsers,
            'nbArticles' => $nbArticles,
            'nbPersonnel' => $nbPersonnel
        ]);
    }

    #[Route('/barbershops', name: 'admin_barbershop')]
    #[IsGranted('ROLE_ADMIN')]
    public function allBarbershopAdmin(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        $barbershops = $doctrine->getRepository(Barbershop::Class)->findBy([], ["creationDate"=>"DESC"]);

        $barbershops = $paginator->paginate(
            $barbershops, 
            $request->query->getInt('page', 1), 
            9
        );

        return $this->render('security/admin/barbershops.html.twig', [
            'barbershops' => $barbershops,
        ]);
    }

    #[Route('/users', name: 'admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function listUsers(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::Class)->findBy([], ["pseudo"=>"ASC"]);

        return $this->render('security/admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/articles', name: 'admin_articles')]
    #[IsGranted('ROLE_ADMIN')]
    public function listArticles(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::Class)->findBy([], ["date"=>"ASC"]);

        return $this->render('security/admin/Articles.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/users/edit/{id}', name: 'admin_edituser')]
    #[IsGranted('ROLE_ADMIN')]
    public function editUsers(User $user, ManagerRegistry $doctrine, Request $request): Response
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('admin_users');
        }
    
    return $this->render('security/admin/edituser.html.twig', [
        'userForm' => $form->createView(),
    ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{   
    // Index des articles
    #[Route('/article', name: 'app_article')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // Récuperer tous articles
        $articles = $doctrine->getRepository(Article::Class)->findBy([], ["titre"=>"ASC"]);
        return $this->render('article/index.html.twig', [
            'articles' => $articles
        ]);
    }

    // Ajouter ou éditer un article
    #[Route('/article/add', name: 'add_article')]
    #[Route('/article/{id}/edit', name: 'edit_article')]
    public function add(ManagerRegistry $doctrine, Article $article = null, Request $request) : Response
    {   
        if(!$article){
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_article');
        }

        return $this->render('article/add.html.twig', [
            'formAddArticle' => $form->createView(),
        ]);
    }

    // Afficher les details d'un article
    #[Route('/article/{id}', name: 'show_article')]
    public function detail(ManagerRegistry $doctrine, Article $article = null): Response
    {   
        if ($article)
        {
            return $this->render('article/show.html.twig', [
                'article' => $article,
            ]);
        }
        else
        {
            return $this->redirectToRoute('app_article');
        }
    }

    // Supprimer un article
    #[Route('admin/article/{id}/delete', name: 'delete_article')]
    public function delete(ManagerRegistry $doctrine, Article $article = null): Response
    {   
        if ($article){
            $entityManager = $doctrine->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_article');
    }

}

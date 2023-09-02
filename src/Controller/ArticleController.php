<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    #[Route('administration/article/add', name: 'add_article')]
    #[Route('administration/article/{id}/edit', name: 'edit_article')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(ManagerRegistry $doctrine, Article $article = null, Request $request) : Response
    {   
        $isEditMode = ($article !== null); // Check if in edit mode

        if(!$article){
            $article = new Article();
        }


        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article = $form->getData();
            $entityManager = $doctrine->getManager();
            
            date_default_timezone_set('Europe/Paris');
            $today = new DateTime();
            $user = $this->getUser();
            $article->setDate($today);
            $article->setUser($user);
            $entityManager->persist($article);

            $notificationMessage = ($isEditMode) ? 'Article modifié.' : 'Article ajouté.';
            notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addSuccess($notificationMessage);
            
            $entityManager->flush();

            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('article/add.html.twig', [
            'formAddArticle' => $form->createView(),
        ]);
    }

    // Afficher les details d'un article
    #[Route('/article/{slug}', name: 'show_article')]
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
    #[Route('administration/article/{id}/delete', name: 'delete_article')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(ManagerRegistry $doctrine, Article $article = null): Response
    {   
        if ($article){
            $entityManager = $doctrine->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            notyf()
            ->position('x', 'right')
            ->position('y', 'bottom')
            ->addError('Article supprimé.');

        }
        
        return $this->redirectToRoute('admin_articles');
    }

}

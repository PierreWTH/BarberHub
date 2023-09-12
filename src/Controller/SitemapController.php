<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\BarbershopRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ['_format' => 'xml'])]
    public function index(Request $request, BarbershopRepository $barbershopRepository, ArticleRepository $articleRepository): Response
    {

        $hostname = $request->getSchemeAndHttpHost();

        $urls = [];

        $urls[] = ['loc'    => $this->generateUrl('app_home')];
        $urls[] = ['loc'    => $this->generateUrl('app_article')];
        $urls[] = ['loc'    => $this->generateUrl('app_barbershop')];
        $urls[] = ['loc'    => $this->generateUrl('app_map')];
        $urls[] = ['loc'    => $this->generateUrl('app_myspace')];
        $urls[] = ['loc'    => $this->generateUrl('app_contact')];

        foreach($barbershopRepository->findAll() as $barbershop){
            $urls[] = [
                'loc'       => $this->generateUrl('show_barbershop', ['slug' => $barbershop->getSlug()]),
                'lastmod'   => $barbershop->getCreationDate()->format('Y-m-d')
            ];
        }

        foreach($articleRepository->findAll() as $article){
            $urls[] = [
                'loc'       => $this->generateUrl('show_article', ['slug' => $article->getSlug()]),
                'lastmod'   => $article->getDate()->format('Y-m-d')
            ];
        }


        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
            'urls' => $urls,
            'hostname' => $hostname,
            ]),
            200
        );

        $response->headers->set('Content-type', 'text/xml');


        return $response;
    }
}

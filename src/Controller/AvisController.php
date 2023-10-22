<?php

namespace App\Controller;


use DateTime;
use App\Entity\Avis;
use App\Form\AvisType;
use App\Entity\Barbershop;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AvisController extends AbstractController
{

    // Index des avis
    #[Route('/administration/avis', name: 'admin_avis')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // Récuperer tous avis
        $allAvis = $doctrine->getRepository(Avis::Class)->findBy([], ["date"=>"DESC"]);
        $barbershops = $doctrine->getRepository(Barbershop::Class)->findBy([], ["creationDate"=>"DESC"]);

        return $this->render('avis/index.html.twig', [
            'allAvis' => $allAvis,
            'barbershops' => $barbershops
        ]);
    }

    #[Route('/barbershop/{id}/avis/add', name: 'add_avis')]
    #[Route('/barbershop/{barbershop}/avis/{avis}/edit', name: 'edit_avis')]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER") or is_granted("ROLE_BARBER")'))]
    public function add(ManagerRegistry $doctrine, Barbershop $barbershop = null, Avis $avis = null, Request $request) : Response
    {   
        $isEditMode = ($avis !== null); // Check if in edit mode

        if(!$avis){
            $avis = new Avis();
        }

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $avis = $form->getData();

            // SET LA DATE DE CREATION
            $date = new DateTime();
            $avis->setDate($date);

            // SET USER
            $user = $this->getUser();
            $avis->setUser($user);

            //SET BARBERSHOP
            $avis->setBarbershop($barbershop);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($avis);
            
            $entityManager->flush();

            $notificationMessage = ($isEditMode) ? 'Avis modifié.' : 'Avis ajouté.';
            notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addSuccess($notificationMessage);

            return $this->redirectToRoute('show_barbershop',['slug' => $barbershop->getSlug()]);
        }

        return $this->render('avis/add.html.twig', [
            'formAddAvis' => $form->createView(),
            'edit' => $avis->getId(),
        ]);
    }

    // Supprimer un avis en AJAX
    #[Route('/barbershop/{barbershop}/avis/{avis}/delete', name: 'delete_avis', methods:"post")]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER") or is_granted("ROLE_BARBER")'))]
    public function delete(ManagerRegistry $doctrine,Security $security, Avis $avis = null): Response
    {   
        if ($avis->getUser()->getId() === $this->getUser()->getId() || $security->isGranted("ROLE_ADMIN")){
            $entityManager = $doctrine->getManager();
            $entityManager->remove($avis);
            $entityManager->flush();

            return new JsonResponse(['success' => true], 200);
        }
        else{
            return $this->redirectToRoute('app_home');
        }
        
    }
}

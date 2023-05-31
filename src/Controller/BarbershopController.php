<?php

namespace App\Controller;

use DateTime;
use IntlDateFormatter;
use App\Entity\Barbershop;
use App\Form\BarbershopType;
use App\Entity\BarbershopPics;
use App\Service\PictureService;
use App\HttpClient\NominatimHttpClient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BarbershopController extends AbstractController
{
    #[Route('/barbershops', name: 'app_barbershop')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // Récuperer tous les barbiers de la BDD
        $allBarbershops = $doctrine->getRepository(Barbershop::Class)->findBy([], ["nom"=>"ASC"]);

        return $this->render('barbershop/index.html.twig', [
            'allBarbershops' => $allBarbershops,
        ]);
    }
    
    #[Route('/barbershop/add', name: 'add_barbershop')]
    #[Route('/barbershop/{id}/edit', name: 'edit_barbershop')]
    public function add(ManagerRegistry $doctrine, Barbershop $barbershop = null, Request $request, PictureService $pictureService, NominatimHttpClient $nominatim, Security $security, $id = null ) : Response
    {
        if ($id !== null) {
            $entityManager = $doctrine->getManager();
            $barbershop = $entityManager->getRepository(Barbershop::class)->find($id);
            // Vérifier si le Barbershop existe
            if (!$barbershop) {
                throw $this->createNotFoundException('Barbershop introuvable.');
            }
        } else {
            // Créer un nouvel objet Barbershop pour le mode ajout
            $barbershop = new Barbershop();
        }

        $personnel = $barbershop->getPersonnels();
        $user = $this->getUser();

        // Si l'user n'a pas le role admin
        if(!$security->isGranted('ROLE_ADMIN')){
        // et que personnel est vide ou que l'user ne travaille pas dans le barbershop
            if($personnel->isEmpty() || $personnel[0]->getUser()->getId() !== $user->getId() || $personnel[0]->isIsManager() === false)
            {
                throw new AccessDeniedException("Vous n'avez pas les droits nécessaires pour effectuer cette action.");
            }
        }
        
        $horaires = $barbershop->getHoraires(); 
        $adresse = $barbershop->getAdresse(); 

        $form = $this->createForm(BarbershopType::class, $barbershop);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $barbershop = $form->getData();

            // RECUPERER LA LONGITUDE ET LA LATITUDE

            // On récupere les données du formulaire
            // Urlencode pour que l'adresse soit formatée pour être valide dans une URL (car elle vas passer dans l'API)
            $adresse = urlencode($form->get('adresse')->getData());
            $ville = urlencode($form->get('ville')->getData());
            $cp = $form->get('cp')->getData();

            // On passe les données a nominatim
            $coordinates = $nominatim->getCoordinates($adresse,$ville,$cp);

            // On set la latitude et la longitude du bar
            $barbershop->setLatitude($coordinates[0]['lat']);
            $barbershop->setLongitude($coordinates[0]['lon']);

            // RECUPERER LES IMAGES
            $image = $form->get('images')->getData();

            // On définit le dossier de destination
            $folder = 'barbershopPics';
            // On appelle le service d'ajout 
            $fichier = $pictureService->add($image, $folder, 850, 310);

            $img = new BarbershopPics();
            $img->setNom($fichier);
            $barbershop->addBarbershopPic($img);
            
            // RECUPERER LES HORAIRES
            $horaires = $form->get('horaires')->getData();
            // Enregistre en tant que tableau PHP
            $barbershop->setHoraires($horaires);

            // SET LA DATE DE CREATION
            $date = new DateTime();
            $barbershop->setCreationDate($date);

            // SET IS_VALIDATE
            $barbershop->setIsValidate(0);

            // ON ENVOIE LES DONNEES DANS LA BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($barbershop);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_barbershop');
        }

        return $this->render('barbershop/add.html.twig', [
            'formAddBarbershop' => $form->createView(),
            'edit' => $barbershop->getId(),
            'horaires' => $horaires,
            'adresse' => $adresse,
            'barbershop' => $barbershop
        ]);
    }

    #[Route('/barbershop/{id}', name: 'show_barbershop')]
    public function show(Barbershop $barbershop = null) : Response
    {
        if ($barbershop)
        {
            $dateActuelle = new DateTime();
            $locale = 'fr_FR';
        
            // Créer un objet IntlDateFormatter pour formater la date en français
            $dateFormatter = new IntlDateFormatter($locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'EEEE');
            // Formater la date actuelle en utilisant le formateur de date
            $jourActuel = $dateFormatter->format($dateActuelle);

            // Récupération d'un tableau contenant les coordonées
                $latitude = $barbershop->getLatitude();
                $longitude = $barbershop->getLongitude();
                // Ajoutez les coordonnées à un tableau
                $coordinates[] = [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ];
            
            return $this->render('barbershop/show/show.html.twig', [
                'barbershop' => $barbershop,
                'horaires' => json_decode($barbershop->getHoraires(), True),
                'jourActuel' => $jourActuel,
                'coordinates' => $coordinates,
            ]);
        }
        else
        {
            return $this->redirectToRoute('app_barbershop');
        }
        
    }

   // Publier un Barbershop
   #[Route('/barbershop/{id}/validate', name: 'validate_barbershop')]
   public function validate(ManagerRegistry $doctrine, Barbershop $barbershop): Response
   {   
        if ($barbershop->isValidate())
        {
            $barbershop->setIsValidate(false);
        }
        else
        {
            $barbershop->setIsValidate(true);
        }

        $entityManager = $doctrine->getManager();    
        $entityManager->flush();
        
        return $this->redirectToRoute('app_barbershop');
   }

    // Supprimer un Barbershop
    #[Route('/barbershop/{id}/delete', name: 'delete_barbershop')]
    public function delete(ManagerRegistry $doctrine, barbershop $barbershop = null): Response
    {   
        if ($barbershop){
            $entityManager = $doctrine->getManager();
            $entityManager->remove($barbershop);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_barbershop');
    }

    // Suppression de la photo d'un barbershop
    #[Route('/barbershop/photo/{id}/delete', name: 'delete_photo', methods: ["DELETE"])]
    public function deletePhoto(BarbershopPics $image, Request $request, EntityManagerInterface $em, PictureService $pictureService): JsonResponse
    {   
        // On récupere le contenu de la requete 

        $data = json_decode($request->getContent(), true);

        // Si le token est valide
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data["_token"] )){
            // On récupère le nom de l'image
            $nom = $image->getNom();

            if($pictureService->delete($nom, 'barbershopPics', 850, 310)){
                // On supprime l'image de la BDD 
                $em->remove($image);
                $em->flush();

                return new JsonResponse(['success' => true], 200);
            }
            // Si la suppresion a échoué
            return new JsonResponse(['error' => 'Erreur de suppression'], 400);
        }
        // Si le token est invalide
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}



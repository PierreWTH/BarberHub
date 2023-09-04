<?php

namespace App\Controller;

use DateTime;
use IntlDateFormatter;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Entity\Barbershop;
use App\Form\BarbershopType;
use App\Entity\BarbershopPics;
use App\Service\PictureService;
use App\HttpClient\NominatimHttpClient;
use App\Repository\BarbershopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class BarbershopController extends AbstractController
{
    #[Route('/barbershops', name: 'app_barbershop')]
    public function index(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request, BarbershopRepository $br): Response
    {
        // Récuperer tous les barbiers de la BDD

        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);

        // recherche
        if ($request->isXmlHttpRequest()) { 
            $searchData = new SearchData();
            $searchData->q = $request->query->get('search');
            $searchData->city = $request->query->get('city');
            $searchData->sortBy = $request->query->get('sort');

            $searchedBarbershops = $br->findBySearch($searchData);

            return $this->render('barbershop/index/_barberCards.html.twig', [
                'allBarbershops' => $searchedBarbershops
            ]);
        }

        $allBarbershops = $doctrine->getRepository(Barbershop::Class)->getAllValidBarbershop();
      
        // Pagination
        $allBarbershops = $paginator->paginate(
            $allBarbershops, 
            $request->query->getInt('page', 1), 
            12
        );
        
        return $this->render('barbershop/index/index.html.twig', [
            'form' => $form->createView(),
            'allBarbershops' => $allBarbershops,
        ]);
    }
    
    #[Route('administration/barbershop/add', name: 'add_barbershop')]
    #[Route('/barbershop/{slug}/edit', name: 'edit_barbershop')]
    public function add(ManagerRegistry $doctrine, Barbershop $barbershop = null, Request $request, PictureService $pictureService, NominatimHttpClient $nominatim, Security $security ) : Response
    {   

        $isEditMode = ($barbershop !== null);

        if(!$barbershop){
            $barbershop = new Barbershop();
        }

        $personnels = $barbershop->getPersonnels();

        // Tableaux avec tous le personnel
        $allPersonnel = [];
        foreach($personnels as $personnel){
            $persUser = $personnel->getUser()->getId();
            $allPersonnel[] = $persUser;
        }
        $user = $this->getUser();
        
        // Si on est en mode edit 
        if($isEditMode){
            // On verifie que l'utilisateur est bien manager et travaille bien dans le barber
            if($personnels->isEmpty() || !in_array($user->getId(), $allPersonnel, true) || $user->getPersonnel()->isManager() === false)
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
            $adresseRaw = $form->get('adresse')->getData();

            // Regex pour enlever la ville et le code postal de l'adresse récupérée (selectize n'affiche pas les options ayant la meme valeur donc obligé de récuperer le label)
            $pattern = '/ \d{5} [A-Za-z\-]+$/';
            $adresseShorted = preg_replace($pattern, '', $adresseRaw);
            $adresse = urlencode($adresseShorted);

            $ville = urlencode($form->get('ville')->getData());
            $cp = $form->get('cp')->getData();

            // On passe les données a nominatim
            $coordinates = $nominatim->getCoordinates($adresse,$ville,$cp);
            
            // Si coordinates est vide : message d'erreur 
            if (empty($coordinates)) {
                // Les coordonnées sont vides, on ajoute un message d'erreur dans les messages flash
                notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addSuccess("Adresse invalide. ");
                return $this->redirectToRoute('edit_barbershop', ["slug" => $barbershop->getSlug()]);
            }

            // On set la latitude et la longitude du barbershop
            $barbershop->setLatitude($coordinates[0]['lat']);
            $barbershop->setLongitude($coordinates[0]['lon']);

            // On set l'adresse modifiée
            $barbershop->setAdresse($adresseShorted);

            // RECUPERER L'IMAGE DU SALON 
            $images = $form->get('realisations')->getData();

            if($images){

                foreach($images as $image){
                // On définit le dossier de destination
                $folder = 'barbershopPics';
                // On appelle le service d'ajout 
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new BarbershopPics();
                $img->setNom($fichier);
                $barbershop->addBarbershopPic($img);
                }
            }

            // RECUPERER LES HORAIRES
            $horaires = $form->get('horaires')->getData();
            // Enregistre en tant que tableau PHP
            $barbershop->setHoraires($horaires);

            // SET LA DATE DE CREATION
            $date = new DateTime();
            $barbershop->setCreationDate($date);

            // SET IS_VALIDATE
            $barbershop->setValidate(0);
            

            // ON ENVOIE LES DONNEES DANS LA BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($barbershop);
            
            $entityManager->flush();

            $notificationMessage = ($isEditMode) ? 'Barbershop modifié.' : 'Barbershop ajouté.';
            notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addSuccess($notificationMessage);

            if($security->isGranted('ROLE_BARBER')){
                return $this->redirectToRoute('manage_barbershop');
            }
            else{
                return $this->redirectToRoute('admin_barbershop');
            }

        }

        return $this->render('barbershop/add.html.twig', [
            'formAddBarbershop' => $form->createView(),
            'edit' => $barbershop->getId(),
            'horaires' => $horaires,
            'adresse' => $adresse,
            'barbershop' => $barbershop
        ]);
    }

    #[Route('/barbershop/{slug}', name: 'show_barbershop')]
    public function show(Barbershop $barbershop = null) : Response
    {   
        if ($barbershop && $barbershop->isValidate())
        {
            $dateActuelle = new DateTime();
            $locale = 'fr_FR';
        
            // Créer un objet IntlDateFormatter pour formater la date en français
            $dateFormatter = new IntlDateFormatter($locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'EEEE');
            // Formater la date actuelle en utilisant le formateur de date
            $jourActuel = $dateFormatter->format($dateActuelle);

            $horaires = json_decode($barbershop->getHoraires(), True);
            // Récupération d'un tableau contenant les coordonées
                $latitude = $barbershop->getLatitude();
                $longitude = $barbershop->getLongitude();
                // Ajoutez les coordonnées à un tableau
                $coordinates[] = [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ];

            // Empecher apparaition d'ajouter un com si il n'a pas pris RDV

            if($this->getUser()){
                $rdvsUser = $this->getUser()->getRendezvouses();
                $userRdvIds = [];
                // Tous les ID de barbershop ou l'user a déja pris RDV dans un tableau
                foreach($rdvsUser as $rdv){
                    foreach($rdv->getBarberPrestation() as $prestation)
                    $userRdvIds[] = $prestation->getBarbershop()->getId();
                }

                // Empecher appartion d'ajouter un com si déja commenté.
                $avisUser = $this->getUser()->getAvis();
                $userAvisIds = [];
                // Tous les ID de barbershop que l'user a déja commenté dans un tableau
                foreach($avisUser as $avis){
                    $userAvisIds[] = $avis->getBarbershop()->getId();
                }
            }
            

            return $this->render('barbershop/show/show.html.twig', [
                'barbershop' => $barbershop,
                'horaires' => $horaires,
                'jourActuel' => $jourActuel,
                'coordinates' => $coordinates,
                'userRdvIds' => $this->getUser() ? $userRdvIds : null,
                'userAvisIds' => $this->getUser() ? $userAvisIds : null,
            ]);
        }
        else
        {
            return $this->redirectToRoute('app_barbershop');
        }
        
    }

   // Publier/ retirer un Barbershop en AJAX
   #[Route('/barbershop/{id}/validate', name: 'validate_barbershop', methods:"post")]
   #[IsGranted('ROLE_ADMIN')]
   public function validate(ManagerRegistry $doctrine, Barbershop $barbershop): Response
   {   
        if ($barbershop->isValidate())
        {
            $barbershop->setValidate(false);
        }
        else
        {
            $barbershop->setValidate(true);
        }

        $entityManager = $doctrine->getManager();    
        $entityManager->flush();
        
        return new JsonResponse(['success' => true], 200);

   }

    // Supprimer un Barbershop
    #[Route('administration/barbershop/{id}/delete', name: 'delete_barbershop')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(ManagerRegistry $doctrine, barbershop $barbershop = null): Response
    {   
        if ($barbershop){
            $entityManager = $doctrine->getManager();
            $entityManager->remove($barbershop);
            $entityManager->flush();

        }

        notyf()
            ->position('x', 'right')
            ->position('y', 'bottom')
            ->addError('Barbershop supprimé.');

        return $this->redirectToRoute('admin_barbershop');
    }

    // Suppression de la photo d'un barbershop
    #[Route('administration/barbershop/photo/{id}/delete', name: 'delete_photo', methods: ["DELETE"])]
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

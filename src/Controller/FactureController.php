<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\RendezVous;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FactureController extends AbstractController
{   
    // Générer une facture pour un rendez vous 
    #[Route('/monespace/rdv/{id}/facture', name: 'app_facture')]
    public function generateInvoice(RendezVous $rendezVous): Response
    {   
        // On récupère la date du rdv
        $date = $rendezVous->getDebut();

        // On récupère le client 
        $client = $rendezVous->getUser();

        // On récupère l'employé qui a fait la prestation
        $employe = $rendezVous->getPersonnel()->getUser();

        $barbershop =  $rendezVous->getPersonnel()->getBarbershop();

        // On récupère les prestations effectuées
        $prestations = $rendezVous->getBarberPrestation();

        // On fait un tableau avec le prix de toutes les prestations 
        $allPrice = [];
        foreach($prestations as $prestation){
            $allPrice[] = $prestation->getPrix();
        }

        // Calcul du total des prix avec array_sum
        $total = array_sum($allPrice);


        $pdfOptions = new Options();
        $pdfOptions->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($pdfOptions);

        // POUR METTRE LOGO SUR LA FACTURE - Conversion du logo en base64 pour l'affichage 
        // $path = '/Users/pw/Desktop/Projet_Final/BarberHub/public/images/Logo_black.png';
        // $type = pathinfo($path, PATHINFO_EXTENSION);
        // $data = file_get_contents($path);
        // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

       
        // Génération du HTML du PDF
        $html = $this->renderView('facture/template.html.twig', [
            'client' => $client,
            'employe' => $employe,
            'prestations' => $prestations,
            'total' => $total,
            //'image' => $base64,
            'barbershop' => $barbershop, 
            'date' => $date
        ]);
        
        $dompdf->loadHtml($html);

        
        $dompdf->setPaper('A4', 'portrait');
        
        $dompdf->render();

        $dompdf->stream("invoice.pdf", [
            "Attachment" => false,
        ]);

        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}

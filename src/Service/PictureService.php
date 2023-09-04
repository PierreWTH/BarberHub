<?php

namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 850, ?int $height = 310)
    {
        // On donne un nouveau nom a l'image
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        // On récupère les infos de l'image (largeur, hauteur, ect...)
        $picture_infos = getimagesize($picture);

        // Si il y a un probleme dans le get image size
        if($picture_infos === false)
        {
            throw new Exception('Format d\'image incorrect');
        }

        // On vérifie le format de l'image
        switch($picture_infos['mime'])
        {
            case 'image/png':
                // On récupere l'image dans une variable pour pouvoir la manipuler
                $picture_source = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                // On récupere l'image dans une variable pour pouvoir la manipuler
                $picture_source = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                // On récupere l'image dans une variable pour pouvoir la manipuler
                $picture_source = imagecreatefromwebp($picture);
                break;
            default:
                throw new Exception('Format d\'image incorrect');
        }

        // On recadre l'image

        // On récupere les dimensions
        $imageWidth = $picture_infos[0];
        $imageHeight = $picture_infos[1];

        // On vérifie l'orientation de l'image
        switch($imageWidth <=> $imageHeight) 
        {
            case -1: // portait
                // On découpe l'image 
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;

            case 0: // carré
                // Pas besoin de découpe
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;

            case 1: // paysage
                // On découpe l'image 
                $squareSize = $imageWidth;
                $src_x = ($imageWidth - $squareSize) / 2;
                $src_y = 0;
                break;
        } 

       // On crée une nouvelle image vierge
       $resized_picture = imagecreatetruecolor($width, $height);

       imagecopyresampled($resized_picture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory') .$folder;

        // On crée le dossier de destination s'il n'existe pas 
        if (!file_exists($path . '/barbershop/'))
        {
            mkdir($path .'/barbershop/', 0755, true);
        }

        // On stocke l'image recadrée
        imagewebp($resized_picture, $path . '/barbershop/' . $width . 'x' . $height . '-'  .$fichier);

        // On déplace l'image
        $picture->move($path .'/', $fichier);

        return $fichier;

    }

    public function delete(string $fichier, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        if($fichier !== 'defaut.webp')
        {
            $success = false;
            $path = $this->params->get('images_directory') .$folder;

            $barbershopPic = $path . '/barbershop/' . $width . 'x' . $height . '-' . $fichier;

            if(file_exists($barbershopPic))
            {
                unlink($barbershopPic);
                $success = true;
            }

            $original = $path. '/' . $fichier;

            if(file_exists($original))
            {
                unlink($original);
                $success = true;
            }
            return $success;
        }
        return false;
    }
}
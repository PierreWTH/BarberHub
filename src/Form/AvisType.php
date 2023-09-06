<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\Barbershop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('note', HiddenType::class)
        ->add('texte', TextareaType::class, [
            "label" => false,
            "attr" => [
            "placeholder" => 'Donnez nous votre ressenti sur l\'acceuil, la prestation, ou encore l\'ésthétique du salon.'
            ]
        ])
        ->add('submit', SubmitType::class, [
            'attr' => ['class' => 'submit-button-template'],
            'label' => 'Envoyer',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}

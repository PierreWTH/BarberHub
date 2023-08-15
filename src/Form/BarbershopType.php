<?php

namespace App\Form;

use App\Entity\Barbershop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class BarbershopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('description', TextareaType::class)
              
            ->add('adresse', TextType::class)
            
            ->add('cp', HiddenType::class )

            ->add('ville', HiddenType::class )
            
            ->add('horaires', HiddenType::class, [
                'required' =>true,
            ]) 
            
            ->add('telephone', TextType::class)
            ->add('email', TextType::class, [
                'required' =>false,
            ])
            

            ->add('images', FileType::class, [
                'label' =>"Photo",
                'multiple'=>false,
                'mapped' =>false,
                'required' =>false,
            ])

            ->add('instagram', UrlType::class, [
                'required' =>false,
            ])

            ->add('facebook', UrlType::class, [
                'required' =>false,
            ])
            
            ->add('submit', SubmitType::class, [
                'label' =>'Ajouter',
                'attr' => ['class' => 'submit-button-template'] 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Barbershop::class,
            'attr' => [
                'id' => 'addBarbershopForm']
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Personnel;
use App\Entity\RendezVous;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $barbershopId = $options['barbershopId'];

        $builder
        
            ->add('debut', HiddenType::class, [
                'mapped' => false,
            ])

            ->add('nom', TextType::class, [
                'mapped' => false,
                'attr' => [
                'placeholder' => "Votre nom"
                ],
            ])
            ->add('prenom', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'placeholder' => "Votre prénom"
                    ],
            ])

            ->add('telephone', TextType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'Telephone *',
                'attr' => [
                    'placeholder' => "Votre numéro"
                    ],
            ])


            // N'affiche que le personnel qui travaille dans le barber $barbershopID
            
            ->add('personnel', EntityType::class, [
                'label' => false,
                'class' => Personnel::class,
                'query_builder' => function (EntityRepository $er) use ($barbershopId) {
                    return $er->createQueryBuilder('p')
                        ->where('p.barbershop = :barbershopId')
                        ->setParameter('barbershopId', $barbershopId);
                },
                'multiple' => false, 
                'expanded' => true, 
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Réserver'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
            'barbershopId' => false,
            'attr' => [
                'id' => 'addRdvForm']
            
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Personnel;
use App\Entity\RendezVous;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $barbershopId = $options['barbershopId'];
        $plagesHoraires = $options['plageHoraires'];

        $builder
        
            ->add('debut', ChoiceType::class, [
                'choices' => $plagesHoraires,
                'label' => false,
            ])

            ->add('fin', HiddenType::class)

            // N'affiche que le personnel qui travaille dans le barber $barbershopID
            ->add('personnel', EntityType::class, [
                'class' => Personnel::class,
                'query_builder' => function (EntityRepository $er) use ($barbershopId) {
                    return $er->createQueryBuilder('p')
                        ->where('p.barbershop = :barbershopId')
                        ->setParameter('barbershopId', $barbershopId);
                }
                
            ])

            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
            'barbershopId' => false,
            'plageHoraires' =>false
            
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('debut', DateTimeType::class, [
                'minutes' => range(0, 30, 30),
                'attr' => [
                    'min' => date('Y-m-d')
                ],
                'label' => false,
            ])
            // ->add('fin', DateTimeType::class, [
            //     'time_label' => 'Starts On',
            //     'minutes' => range(0, 30, 30),
            // ])
            ->add('personnel', HiddenType::class, [
                'attr' => [
                    'id' => 'personne-input',
                ],
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}

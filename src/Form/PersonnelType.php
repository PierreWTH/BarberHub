<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Personnel;
use App\Entity\Barbershop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PersonnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isManager', ChoiceType::class, [
                'label' => 'Statut',
                'choices'  => [
                    'Manager' => true,
                    'Employé' => false,
                    ]
                ])
            ->add('barbershop', EntityType::class, [
                'class' => Barbershop::class,
                'choice_label' => 'nom',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'pseudo',
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personnel::class,
        ]);
    }
}

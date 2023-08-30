<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Personnel;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PersonnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('barbershop')
        ->add('user', EntityType::class, [
            'label' => 'Utilisateur',
            'class' => User::class, 
            // Afficher seulement les user qui ont le role barber
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.roles LIKE :role')
                    ->setParameter('role', '%ROLE_BARBER%');
            },
        ])
        ->add('manager', CheckboxType::class, [
            'required' => false,
            'attr' => ['class' => 'sc-gJwTLC ikxBAC'] 
        ])
        ->add('submit', SubmitType::class, [
            'label' => "Ajouter",
            'attr' => ['class' => 'submit-button-template'] 
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personnel::class,
            'attr' => [
                'id' => 'addPersonnelForm']
        ]);
    }
}


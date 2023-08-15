<?php

namespace App\Form;

use App\Entity\Personnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PersonnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('barbershop')
        ->add('user')
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


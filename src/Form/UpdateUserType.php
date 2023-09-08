<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UpdateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', TextType::class)
            ->add('telephone', TextType::class)

            ->add('old_password', TextType::class, [
                'mapped' => false,
                'label' => 'Mot de passe actuel', 
                'required'   => false,
            ])
            ->add('new_password', TextType::class, [
                'mapped' => false, 
                'required'   => false,
                'label' => 'Nouveau mot de passe'
            ])
            ->add('new_password_confirm', TextType::class, [
                'mapped' => false, 
                'label' => 'Confirmation', 
                'required'   => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' =>'Mettre à jour ',
                'attr' => ['class' => 'submit-button-template'] 
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'id' => 'updateUserForm']
        ]);
    }
}

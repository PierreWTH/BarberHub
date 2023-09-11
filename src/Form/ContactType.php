<?php

namespace App\Form;

use App\Form\FormExtension\HoneyPotType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends HoneyPotType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        parent::buildForm($builder, $options);
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Votre adresse mail...'
                ],
                'constraints' => [
                    new Email([
                        'message' => 'L\'adresse e-mail "{{ value }}" n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'attr' => [
                'placeholder' => 'La raison de votre demande...' ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Votre message...'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' =>'Envoyer',
                'attr' => ['class' => 'submit-button-template'] 
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'id' => 'contactForm']
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['attr'=>
            ['placeholder ' => 'Titre de l\'article']
            ])
            ->add('photo', UrlType::class, ['attr'=>
            ['placeholder ' => 'URL de la photo']
            ])
            ->add('description', TextType::class, [
                'attr'=>
                ['placeholder ' => '200 caractères max.'],
                'constraints' => [
                    new Length([
                        'max' => 200,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])

            ->add('texte', CKEditorType::class)
            
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'pseudo'
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

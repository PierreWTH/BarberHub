<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('texte', TextareaType::class, ['attr'=>
            ['placeholder ' => 'Ecrivez le contenu ici...']
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'pseudo'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

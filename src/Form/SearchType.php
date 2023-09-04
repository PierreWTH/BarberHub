<?php

namespace App\Form;

use App\Model\SearchData;
use App\Entity\Barbershop;
use App\Entity\Post\Category;
use App\Repository\BarbershopRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchType extends AbstractType
{
    private $barbershopRepository;

    public function __construct(BarbershopRepository $barbershopRepository)
    {
        $this->barbershopRepository = $barbershopRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'attr' => [
                    'placeholder' => 'Rechercher...'
                ],
                'required' => false,
            ])

            ->add('sortBy', ChoiceType::class, [
                'choices' => [
                    'Les plus likés' => 'likes',        
                    'Les plus commentés' => 'comments',
                ],
                'required' => false,
                'placeholder' => 'Tous',
            ])

            ->add('city', ChoiceType::class, [
                'choices' => array_combine(
                    $this->barbershopRepository->getAllCities(),
                    $this->barbershopRepository->getAllCities()
                ),
                'label' => false,
                'required' => false,
                'placeholder' => 'Toutes les villes',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom du produit',
            'attr' => [ 'placeholder' => 'Tapez le nom du produit']
        ])
                ->add('shortDescription', TextareaType::class, [
                    'label' => 'description courte',
                    'attr' => [
                        'placeholder' => 'Tapez une description courte mais assez parlante pour le visiteur'
                    ]
                ])
                ->add('price', MoneyType::class, [
                    'label' => 'prix du produit en ',
                    'attr' => [
                        'placeholder' => 'Tapez le prix du produit en euros'
                    ]
                ])
                ->add('picture', UrlType::class, [
                    'label' => 'Image du produit',
                    'attr' => ['placeholder' => 'Tapez une URL d\'image !']
                ])
                ->add('category', EntityType::class, [
                    'label' => 'category',
                    'placeholder' => '-- Choisir une catégorie --',
                    'class' => Category::class,
                    // 'choice_label'=> 'name',
                    'choice_label' => function (Category $category) {
                        return strtoupper($category->getName());
                        }
                    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\DataTransformer\CentimesTransformer;
use App\Form\Type\PriceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom du produit',
            'attr' => [ 'placeholder' => 'Tapez le nom du produit'],
            'required' => false,
            // 'constraints' => new NotBlank(['message' => "Validation du formulaire : le nom du produit ne peut pas être vide"])
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
                    ],
                    'divisor' =>1,
                    'required' => false,
                    // 'constraints' => new NotBlank(['message' => "Validation du formulaire : le nom du produit est obligatoire"])

                ])
                ->add('picture', UrlType::class, [
                    'label' => 'Image du produit',
                    'attr' => ['placeholder' => 'Tapez une URL d\'image !']
                ])
                ->add('category', EntityType::class, [
                    'label' => 'Catégorie',
                    'placeholder' => '-- Choisir une catégorie --',
                    'class' => Category::class,
                    // 'choice_label'=> 'name',
                    'choice_label' => function (Category $category) {
                        return strtoupper($category->getName());
                    }
            
                ]);

                // $builder->get('price')->addModeltransformer(new CentimesTransformer);
                //     function($value) {
                //         if($value === null) {
                //             return;
                //         }
                //         return $value / 1;
                //     },
                //     function($value) {
                //         if($value === null) {
                //             return;
                //         }
                //         return $value * 1;
                //     }
                // ));

                // $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
                //     $product = $event->getData();

                //     if($product->getPrice() !== null) {
                //         $product->setPrice($product->getPrice() * 1);
                //     }
                // });
            
            //     $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            //         $form = $event->getForm();
                    
                    
            //         /** @var Product */
            //         $product = $event->getData();

            //         if ($product->getPrice() !== null) {
            //             $product->setPrice($product->getPrice() / 1);
            //         }

            //         // if($product->getId() === null) {

            //         //     $form->add('category', EntityType::class, [
            //         //         'label' => 'Catégorie',
            //         //         'placeholder' => '-- Choisir une catégorie --',
            //         //         'class' => Category::class,
            //         //         // 'choice_label'=> 'name',
            //         //         'choice_label' => function (Category $category) {
            //         //             return strtoupper($category->getName());
            //         //         }
                    
            //         // ]);

            //     // }

            // });
            


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

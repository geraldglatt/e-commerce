<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category')]
    public function category($slug,CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);
        if(!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    #[Route('/{category_slug}/{slug}', name: 'product_show')]
    public function show($slug,ProductRepository $productRepository) {

    
    $product = $productRepository->findOneBy([
        'slug' => $slug,
    ]);

    if(!$product) {
        throw $this->createNotFoundException("Ce produit n'existe pas");
    }

    return $this->render('product/show.html.twig', [
        'product' => $product,
    ]);

    }

    #[Route('/admin/product/create', name: 'product_create')]
    public function create(FormFactoryInterface $factory) 
    {
    
        $builder = $factory->createBuilder();

        $builder->add('name')
                ->add('shortDescription')
                ->add('price')
                ->add('category');

        $form = $builder->getForm();

        $formView = $form->createView();



    return $this->render('/product/create.html.twig', [
        'formview' => $formView
    ]);
    }
}

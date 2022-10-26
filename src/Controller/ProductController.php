<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        'product' => $product
    ]);

    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator) {

        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        // $form->setData($product);

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            // $product = $form->getData();
            $em->flush();

            //Le code ci-dessous est les briques de bases pour la redirection

            // $url = $urlGenerator->generate('product_show', [
            //     'category_slug' => $product->getCategory()->getSlug(),
            //     'slug' => $product->getSlug()
            // ]);

            // $response = new RedirectResponse($url,302);
            // return $response;

            //fusion du dessus donne le code du dessous, ici c'est un raccourci du dessus
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);

        }


        $formview = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formview' => $formview
        ]);

    }

    #[Route('/admin/product/create', name: 'product_create')]
    public function create(Request $request, SluggerInterface $slugger,EntityManagerInterface $em) 
    {

        $product = new Product;
    
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted()) {

            $product->setSlug(strtolower($slugger->slug($product->getName())));
            
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }
        
       
        $formView = $form->createView();



    return $this->render('/product/create.html.twig', [
        'formview' => $formView
    ]);
    }
}

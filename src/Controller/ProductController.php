<?php

namespace App\Controller;

use App\Entity\Product;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category', priority:-1)]
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

    #[Route('/{category_slug}/{slug}', name: 'product_show', priority:-1)]
    public function show($slug, ProductRepository $productRepository, EventDispatcherInterface $dispatcher, Request $request) {
    
    $product = $productRepository->findOneBy([
        'slug' => $slug,
    ]);

    if(!$product) {
        throw $this->createNotFoundException("Ce produit n'existe pas");
    }

    $dispatcher->dispatch(new ProductViewEvent($productRepository), 'product.view');

    return $this->render('product/show.html.twig', [
        'product' => $product
    ]);

    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator) {

        
        
        // $client = [
        //     'nom' => 'Glatt',
        //     'prenom'=> 'Gerald',
        //     'voiture' => [
        //         "marque" => new NotBlank(["message" =>"La marque de la voiture est obligatoire"]),
        //         "couleur" => new NotBlank(["message" =>"La couleur de la voiture est obligatoire"]),
        //     ]
        //     ];

        //     $collection = new Collection([
        //         'nom' => new NotBlank(["message" => "Le nom ne doit pas être vide"]),
        //         'prenom' => [
        //             new NotBlank(["message" => "Le prénom ne doit pas être vide"]),
        //             new Length(['min' => 3, 'minMessage' => "Le prénom ne doit pas faire moins de 3 caractères"])
        //         ],
        //         'voiture' => new Collection([
        //         'marque' => new NotBlank(["message" => "La marque de la voiture est obligatoire"]),
        //             'couleur' => new NotBlank(["message" => "La couleur de la voiture est obligatoire"])
        //         ])

        //     ]);

        // $product = new Product;
        // $product->setName("Salut à tous")
        //         ->setPrice(50);

        // $resultat = $validator->validate($product);
        // if($resultat->count() > 0) {
        //     dd("Il y à des erreurs", $resultat);
        // }
        // dd("Tout va bien");

        $product = new Product;

        $resultat = $validator->validate($product);
        
        
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        // $form->setData($product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());
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

        if($form->isSubmitted() && $form->isValid()) {

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

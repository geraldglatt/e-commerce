<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    protected $productRepository;
    protected $cartService;

    public function __construct(ProductRepository $productRepository,CartService $cartService) {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    #[Route('/cart/add/{id}', name: 'cart_add', requirements: ["id" => "\d+"])]
    public function add($id,Request $request): Response
    {
       $product = $this->productRepository->find($id);
       
       if(!$product) {
            throw $this->createNotFoundException("Le produit n'existe pas !");
       }

       $this->cartService->add($id);
       
       //Le FlashBagInterface qui nous est livré permet à ne pas avoir à mettre le code ci-dessous
       /**
        * @var FlashBag 
        */
       //$flashBag = $session->getBag('flashes');

       $this->addFlash('success', 'Le produit a bien été ajouté au panier !');
       //le $this->addFlash() remplace la phrase du dessous
       //$flashBag->add('success', 'Le produit a bien été ajouté au panier !');

       if($request->query->get('returnToCart',)) {
        return $this->redirectToRoute('cart_show');
       }
       
       return $this->redirectToRoute('product_show', [
        'category_slug' => $product->getCategory()->getSlug(),
        'slug' => $product->getSlug()
       ]);
    }

    #[Route("/cart", name: "cart_show")]
    public function show(SessionInterface $session) 
    {
        $detailCart = $this->cartService->getDetailItems();

        $total = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailCart,
            'total' => $total
        ]);
    }

    #[Route("/delete/{id}", name: "cart_delete", requirements: ["id" => "\d+"])]
    public function delete($id) 
    {
        $product = $this->productRepository->find($id);

        if(!$product) {
            throw $this->createNotFoundException("Le produit n'existe pas et ne peut pas être supprimé !");
        }

        $this->cartService->remove($id);

        $this->addFlash('success', "Le produit a bien été supprimé du panier !");

        return $this->redirectToRoute('cart_show');
    }
    #[Route("/cart/decrement/{id}", name: "cart_decrement", requirements: ["id"=> "\d+"])]
    public function decrement($id, CartService $cartService, ProductRepository $productRepository) {

        $product = $productRepository->find($id);

        if(!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas ête décrémenté");
        }

        $cartService->decrement($id);

        $this->addFlash('success', "Le produit à bien décrémenté");

        return $this->redirectToRoute('cart_show');
    }
}

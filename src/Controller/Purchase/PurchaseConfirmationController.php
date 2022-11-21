<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PurchaseConfirmationController extends AbstractController
{
    protected $cartService;
    protected $em;

    public function __construct(CartService $cartService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->em = $em;
    }
    #[route('/purchase/confirm', name: 'purchase_confirm')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour confirmer une commande')]
    public function confirm(Request $request, FlashBagInterface $flashBag) 
    {
        // 1. Nous voulons lire les données du formulaire
        // => FormFactoryInterface / Request
        $form = $this->createForm(CartConfirmationType::class);
        //avec l'abstractController,nous allons donc utiliser le createForm

        // ici tu(mon form) analyses la request
        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : redirection
        if(!$form->isSubmitted()) {
            //$flashBag->add('warning', 'Vous devez remplir le formulaire de confirmation');
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');

            return $this->redirectToRoute('cart_show');
            //return new RedirectResponse($this->router->generate('cart_show'));
        }
        // 3. Si je ne suis pas connecté : rediriger vers la page login(service:Security)
        $user = $this->getUser();

        // 4. Si il n'y à pas de produits dans mon panier (service:CartService)
        $cartItems = $this->cartService->getDetailItems();

        if(count($cartItems) === 0) {
            $this->addFlash('warning', "Vous ne pouvez pas confirmer une commande avec un panier vide !");
            return $this->redirectToRoute('cart_show');
        }

        // 5. Nous allons créer une purchase
        /** @var Purchase */
        $purchase = $form->getData();
        
        // 6. Nous allons la lier avec l'utilisateur actuellement connecté(Security)
        $purchase->setUser($user)
                 ->setPurchasedAt(new DateTimeImmutable())
                 ->setTotal($this->cartService->getTotal());//ici on recupère notre total(avec la fonction getTotal dans notre cartService)

        $this->em->persist($purchase);
        // 7. Nous allons la lier avec les produits qui sont dans le panier(CartService)
        $total = 0;

        foreach($this->cartService->getDetailItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                    ->setProduct($cartItem[] = $cartItem['product'])
                    ->setProductName($cartItem['product']->getName())
                    ->setQuantity($cartItem['qty'])
                    ->setTotal($this->cartService->getTotal())
                    ->setProductPrice($cartItem['product']->getPrice());

            
            $this->em->persist($purchaseItem);

        }

        // 8. Nous allons enregistrer la commande (EntityManagerInterface)
        $this->em->flush();

        //on vide pas le panier tant que la commande n'est pas payée
        //$this->cartService->empty();//ici on vide le panier(attention,il y à une methode empty() dans notre cartService)
        //$this->addFlash('success', "La commande a bien été enregistrée");
        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);
    }

}



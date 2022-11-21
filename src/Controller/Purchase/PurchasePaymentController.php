<?php

namespace App\Controller\Purchase;

use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentController extends AbstractController {

    #[Route('/purchase/pay/{id}', name: 'purchase_payment_form')]
    public function showCardForm($id, PurchaseRepository $purchaseRepository) {

    $purchase = $purchaseRepository->find($id);

    if(!$purchase) {
        return $this->redirectToRoute('cart_show');
    }

// This is your test secret API key.
    \Stripe\Stripe::setApiKey('sk_test_51M4Vi9GuP1FelhhbungWvo9olLzUDGKQNbvLbMlMHSpBOcJrUJCSxKca4vVkyRxfuAgaubwculXgJT7f2jlKrNS700XPWClCev');

    $intent = \Stripe\PaymentIntent::create([
        'amount' => $purchase->getTotal(),
        'currency' => 'eur'
    ]);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret
        
        ]);
    }
}
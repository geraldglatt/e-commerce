<?php

namespace App\Stripe;

use App\Entity\Purchase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeService  {

    protected string $SecretKey;
    protected string $publicKey;

    public function __construct(string $secretKey ,string $publicKey){
        $this->SecretKey = $secretKey;
        $this->publicKey = $publicKey;
    }

    public function getPublicKey() :string 
    {
        return $this->publicKey;
    }

    public function getPaymentGetIntent(Purchase $purchase): string {
        \Stripe\Stripe::setApiKey($this->SecretKey);

    return  \Stripe\PaymentIntent::create([
        'amount' => $purchase->getTotal(),
        'currency' => 'eur'
    ]);
    }
}
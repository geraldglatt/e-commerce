<?php

namespace App\Event;

use App\Repository\ProductRepository;
use Symfony\Contracts\EventDispatcher\Event;

class ProductViewEvent extends Event {
    
    protected $product;

    public function __construct(ProductRepository $product)
    {
        $this->product = $product;
    }

    public function getProduct(): ProductRepository 
    {
        return $this->product;
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }
}
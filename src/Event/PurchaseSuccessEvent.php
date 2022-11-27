<?php

namespace App\Event;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Symfony\Contracts\EventDispatcher\Event;

class PurchaseSuccessEvent extends Event
{
    private $purchase;

    public function __construct(PurchaseRepository $purchase)
    {
        $this->purchase = $purchase;
    }

    public function getPurchase() : PurchaseRepository 
    {
        return $this->purchase;
    }
}
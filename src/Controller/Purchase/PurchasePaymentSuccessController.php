<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasePaymentSuccessController extends AbstractController {
    
    #[Route("/purchase/terminate/{id}", name: "purchase_payment_success")]
    #[IsGranted("ROLE_USER")]
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, 
    CartService $cartService, EventDispatcherInterface $dispatcher) {
        // 1. Je recupère la commande
        $purchase =  $purchaseRepository->find($id);

        if(
            !$purchase || 
            ($purchase && $purchase->getUser() !== $this->getUser() || 
            $purchase && $purchase->getStatus() === Purchase::STATUS_PAID )
            ) {
            $this->addFlash("warning", "La commande n'existe pas");
            return $this->redirectToRoute("purchase_index");
        }
        // 2. Je la fais passer au statut "payer" => "PAID"
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();
        // 3. je vide le panier
        $cartService->empty();
        // 4. je lance un événement qui permettra aux autres développeurs de réagir à la prise d'une commande
        // on va donc se faire livrer un EventDispatcherInterface
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, 'purchase.success');
        // 4. je redirige avec un flash vers la liste de commande
        $this->addFlash("success", "La commande a été payée et confirmée !");
        return $this->redirectToRoute("purchase_index");
    }
}
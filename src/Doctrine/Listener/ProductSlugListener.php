<?php

namespace App\Doctrine\Listener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Product;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductSlugListener {

    protected $slugger;

    public function __construct(SluggerInterface $slugger) {
        $this->slugger = $slugger;
    }

    public function prePersist(Entity $entity,LifecycleEventArgs $event) {
        $entity = $event->getObject();

        //Plus besoin car le services.yaml est renseigné(name et entity)
        // if(!$entity instanceof Product) {
        //     return;
        // }
        if(empty($entity->getSlug())) {
            //sluggerInterface
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }
        
    }
}


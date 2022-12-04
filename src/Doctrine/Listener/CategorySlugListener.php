<?php

namespace App\Doctrine\Listener;

use App\Entity\Category;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategorySlugListener {

    protected $slugger;

    public function __construct(SluggerInterface $slugger) {

        $this->slugger = $slugger;

    }

    public function prePersist(Category $category,LifecycleEventArgs $event) {
        $category = $event->getObject();

        //Plus besoin car le services.yaml est renseigné(name et entity)
        // if(!$entity instanceof Product) {
        //     return;
        // }
        if(empty($category->getSlug())) {
            //sluggerInterface
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
        }
        
    }
}
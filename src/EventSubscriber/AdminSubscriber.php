<?php

namespace App\EventSubscriber;

use App\Entity\Categorie;
use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
       return [
        BeforeEntityPersistedEvent::class => ['setCreateAt'],
        BeforeEntityUpdatedEvent::class => ['setUpdateAt']
       ];
    }

    public function setCreateAt(BeforeEntityPersistedEvent $event){
        $entityInstance = $event->getEntityInstance();
        if(!$entityInstance instanceof Produit && !$entityInstance instanceof Categorie) return;
        $entityInstance->setCreateAt(new \DateTimeImmutable());
    }

    public function setUpdateAt(BeforeEntityUpdatedEvent $event){
        $entityInstance = $event->getEntityInstance();
        if(!$entityInstance instanceof Produit && !$entityInstance instanceof Categorie) return;
        $entityInstance->setUpdateAt(new \DateTimeImmutable());
    }
}
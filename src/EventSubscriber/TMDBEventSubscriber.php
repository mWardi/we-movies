<?php

namespace WeMovies\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tmdb\Event\AfterHydrationEvent;
use Tmdb\Event\BeforeHydrationEvent;

class TMDBEventSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            BeforeHydrationEvent::class => [
                ['preHydrate', -1]
            ],
            AfterHydrationEvent::class => [
                ['postHydrate', -1]
            ]
        ];
    }

    public function preHydrate(BeforeHydrationEvent $event)
    {
        $data = $event->getData();
        foreach ($data as $item) {
            // do stuff
        }
    }
}
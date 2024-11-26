<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Event;
use Pimcore\Model\DataObject\Party;

class EventListController extends FrontendController
{
    public function action(Request $request): ?Response
    {
        $partyName = $request->attributes->get('routeDocument')->getDocument()->getProperties()['theme']->getData();
        $events = new Event\Listing();
        $parties = new Party\Listing();
        $partyId = null;

        foreach($parties as $oneParty) {
            if($oneParty->getParty() === $partyName) {
                $partyId = $oneParty->getId();
            }
        }

        $events->setCondition('party LIKE ?', ['%' . $partyId . '%']);

        $events->setOrderKey('date');

        $nextEvent = $this->getNextEvent($events->getObjects());

        return $this->render('default/event-list.html.twig',
            [
                'events' => $events,
                'nextEvent' => $nextEvent,
            ]
        );
    }

    function getNextEvent(array $events): ?Event
    {
        foreach($events as $event) {
            if($event->getDate() > new \DateTime()) {
                return $event;
            }
        }

        return null;
    }
}

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
        $events = new Event\Listing();
        $parties = new Party\Listing();
        $partyId = null;
        $nextEvent = $this->getNextEvent($events->getObjects());

        if ($this->editmode) {
            return $this->render('default/event-list.html.twig',
                [
                    'events' => $events,
                    'nextEvent' => $nextEvent,
                ]
            );
        }

        $party = explode('/', $request->attributes->getString('_site_path'))[1];

        foreach($parties as $oneParty) {
            if($oneParty->getParty() === $party) {
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

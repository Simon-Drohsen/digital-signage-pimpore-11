<?php

namespace App\Controller;

use Carbon\Carbon;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Event;
use Pimcore\Model\DataObject\Party;
use Pimcore\Model\DataObject\Fact;

class EventCountdownController extends FrontendController
{
    public function action(Request $request): Response
    {
        $events = new Event\Listing();
        $parties = new Party\Listing();
        $partyId = null;
        $facts = new Fact\Listing();
        $fact = false;
        $countdownEvent = null;
        $interval = null;

        if (count($facts) !== 0) {
            $fact = true;
        }

        if ($this->editmode) {
            return $this->render('default/event-countdown.html.twig',
                [
                    'event' => $this->getCountdownEvent($events),
                    'days' => $this->getInterval($this->getCountdownEvent($events))->days,
                    'fact' => $fact,
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

        $countdownEvent = $this->getCountdownEvent($events);
        $interval = $this->getInterval($countdownEvent);

        return $this->render('default/event-countdown.html.twig',
            [
                'event' => $countdownEvent,
                'days' => $interval->days,
                'fact' => $fact,
            ]
        );
    }

    function getCountdownEvent($events): Event
    {
        $countdownEvent = null;

        foreach($events as $event) {
            if($event->getCountdown() && $event->getDate() > new \DateTime()) {
                $countdownEvent = $event;
                $eventDate = Carbon::parse($countdownEvent->getDate()->format('Y-m-d H:i:s'))->addHour();
                $countdownEvent->setDate($eventDate);
                break;
            }
        }
        return $countdownEvent;
    }

    function getInterval($countdownEventDate)
    {
        return Carbon::now()->addHour()->diff(Carbon::parse($countdownEventDate->getDate()->format('Y-m-d H:i:s'))->addHour());
    }
}
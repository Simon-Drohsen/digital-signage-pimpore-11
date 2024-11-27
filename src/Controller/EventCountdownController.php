<?php

namespace App\Controller;

use Carbon\Carbon;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Event;
use Pimcore\Model\DataObject\Party;
use Pimcore\Model\DataObject\Fact;
use Pimcore\Model\DataObject\Redirect;

class EventCountdownController extends FrontendController
{
    public function action(Request $request): Response
    {
        $redirects = new Redirect\Listing();
        $redirect = null;

        if($request->attributes->get('contentDocument')->getKey()) {
            $partyName = $request->attributes->get('contentDocument')->getKey();
        } else {
            $partyName = '';
        }

        foreach ($redirects as $oneRedirect) {
            if (lcfirst($oneRedirect->getTitle()) === 'event-countdown') {
                $redirect = $oneRedirect;
            }
        }

        $events = new Event\Listing();
        $parties = new Party\Listing();
        $partyId = null;
        $facts = new Fact\Listing();
        $fact = false;

        if (count($facts) !== 0) {
            $fact = true;
        }


        foreach($parties as $oneParty) {
            if($oneParty->getParty() === $partyName) {
                $partyId = (int) $oneParty->getId();
                break;
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
                'url' => $redirect->getTo(),
                'timeout' => $redirect->getTimeout(),
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

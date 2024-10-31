<?php

namespace App\Document\Areabrick;

use Pimcore\Model\Document;
use Pimcore\Model\DataObject\Fact;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\DataObject\Event;
use Carbon\Carbon;

class EventCountdown extends AbstractAreabrick {
    public function getName(): string
    {
        return 'Event Countdown';
    }

    public function action(Document\Editable\Area\Info $info): ?Response
    {
        parent::action($info);

        $events = new Event\Listing();
        $facts = new Fact\Listing();
        $countdownEvent = null;
        $interval = null;

        foreach($events as $event) {
            if($event->getCountdown() && $event->getDate() > new \DateTime()) {
                $countdownEvent = $event;
                $eventDate = Carbon::parse($countdownEvent->getDate()->format('Y-m-d H:i:s'))->addHour();
                $countdownEvent->setDate($eventDate);
                $dateNow = Carbon::now()->addHour();
                $interval = $dateNow->diff($eventDate);
                break;
            }
        }

        if (count($facts) === 0) {
            $info->setParam('fact', false);
        } else {
            $info->setParam('fact', true);
        }
        $info->setParam('event', $countdownEvent);
        $info->setParam('days', $interval->days);

        return null;
    }
}


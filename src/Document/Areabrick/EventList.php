<?php

namespace App\Document\Areabrick;

use Pimcore\Model\Document;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\DataObject\Event;

class EventList extends AbstractAreabrick {
    public function getName(): string
    {
        return 'Event List';
    }

    public function action(Document\Editable\Area\Info $info): ?Response
    {
        parent::action($info);
        $events = new Event\Listing();
        $nextEvent = null;
        foreach($events as $event) {
            if($event->getDate() > new \DateTime()) {
                $nextEvent = $event;
                break;
            }
        }

        $info->setParam('events', $events);
        $info->setParam('nextEvent', $nextEvent);
        return null;
    }
}


<?php

namespace App\Document\Areabrick;

use Pimcore\Model\Document;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\DataObject\Event;
use Pimcore\Model\DataObject\Party;

class EventList extends AbstractAreabrick {
    public function getName(): string
    {
        return 'Event List';
    }

    public function action(Document\Editable\Area\Info $info): ?Response
    {
        parent::action($info);
        $events = new Event\Listing();
        $parties = new Party\Listing();
        $partyName = $info->getRequest()->get('party');
        $party = null;

        if($partyName === '') $partyName = 'all';

        if($partyName !== null && $partyName !== 'all') {
            $all = null;
            foreach($parties as $oneParty) {
                if($oneParty->getParty() === $partyName) {
                    $party = $oneParty->getId();
                } elseif ($oneParty->getParty() === 'all') {
                    $all = $oneParty->getId();
                }
            }

            if ($party !== null) {
                $events->setCondition('party = '. $party .' OR party = '. $all);
            } else {
                $events->setCondition('party IS NULL');
            }
        }

        $events->setOrderKey('date');

        $nextEvent = null;
        foreach($events as $event) {
            if($event->getDate() > new \DateTime()) {
                $nextEvent = $event;
                break;
            }
        }

        $info->setParam('events', $events);
        $info->setParam('nextEvent', $nextEvent);
        $info->setParam('party', $partyName);
        return null;
    }
}


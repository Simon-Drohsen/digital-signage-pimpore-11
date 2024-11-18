<?php

namespace App\Document\Areabrick;

use Pimcore\Model\Document;
use Pimcore\Model\DataObject\Party;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\DataObject\Fact;

class RandomFact extends AbstractAreabrick {
    public function getName(): string
    {
        return 'Fact';
    }

    public function action(Document\Editable\Area\Info $info): ?Response
    {
        parent::action($info);

        $facts = new Fact\Listing();
        $randomFact = null;
        $partyName = $info->getRequest()->get('party');
        $parties = new Party\Listing();
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
                $facts->setCondition('party = '. $party .' OR party = '. $all);
            } else {
                $facts->setCondition('party IS NULL');
            }
        }

        if (count($facts) === 0) {
            $info->setParam('empty', true);
        } else {
            $randomFact = $facts->getObjects()[rand(0, count($facts) - 1)];
            $info->setParam('empty', false);
        }

        $info->setParam('fact', $randomFact);
        $info->setParam('party', $partyName);
        return null;
    }
}


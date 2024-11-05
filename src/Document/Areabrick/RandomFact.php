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
        $partyName = $info->getRequest()->get('party');
        $parties = new Party\Listing();
        $party = null;


        if($partyName !== null && $partyName !== '') {
            foreach($parties as $oneParty) {
                if($oneParty->getParty() === $partyName) {
                    $party = $oneParty->getId();
                }
            }
            $facts->setCondition('party = ?', $party);
        }

        if (count($facts) === 0) {
            $info->setParam('empty', true);
            return null;
        }

        $randomFact = $facts->getObjects()[rand(0, count($facts) - 1)];

        $info->setParam('fact', $randomFact);
        $info->setParam('party', $partyName);
        $info->setParam('empty', false);

        return null;
    }
}


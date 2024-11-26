<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Fact;
use Pimcore\Model\DataObject\Party;

class FactController extends FrontendController
{
    public function action(Request $request): Response
    {
        $partyName = $request->attributes->get('routeDocument')->getDocument()->getProperties()['theme']->getData();
        $facts = new Fact\Listing();
        $empty = true;
        $parties = new Party\Listing();
        $partyId = null;

        foreach($parties as $oneParty) {
            if($oneParty->getParty() === $partyName) {
                $partyId = $oneParty->getId();
            }
        }

        $facts->setCondition('party LIKE ?', ['%' . $partyId . '%']);

        $randomFact = $this->getRandomFact($facts->getObjects());

        return $this->render('default/fact.html.twig',
            [
                'fact' => $randomFact,
                'empty' => $empty,
            ]
        );
    }

    function getRandomFact(array $facts): ?Fact
    {
        if (count($facts) !== 0) {
            return $facts[rand(0, count($facts) - 1)];
        }

        return null;
    }
}

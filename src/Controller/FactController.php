<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Fact;
use Pimcore\Model\DataObject\Party;
use Pimcore\Model\DataObject\Redirect;

class FactController extends FrontendController
{
    public function action(Request $request): Response
    {
        $redirects = new Redirect\Listing();
        $redirect = null;
        $facts = new Fact\Listing();
        $parties = new Party\Listing();
        $partyId = null;

        if($request->attributes->get('contentDocument')->getKey()) {
            $partyName = $request->attributes->get('contentDocument')->getKey();
        } else {
            $partyName = '';
        }

        foreach ($redirects as $oneRedirect) {
            if (lcfirst($oneRedirect->getTitle()) === 'fact') {
                $redirect = $oneRedirect;
            }
        }

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
                'url' => $redirect->getTo()[0]['link']->getData(),
                'timeout' => $redirect->getTimeout(),
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

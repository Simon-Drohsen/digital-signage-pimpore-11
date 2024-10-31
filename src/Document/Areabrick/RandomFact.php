<?php

namespace App\Document\Areabrick;

use Pimcore\Model\Document;
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

        $randomFact = $facts->getObjects()[0];

        $info->setParam('fact', $randomFact);
        $info->setParam('empty', false);

        return null;
    }
}


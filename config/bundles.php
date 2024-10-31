<?php

use ToolboxBundle\ToolboxBundle;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use Pimcore\Bundle\TinymceBundle\PimcoreTinymceBundle;

return [
    //Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    ToolboxBundle::class => ['all' => true],
    PimcoreTinymceBundle::class => ['all' => true],
    TwigComponentBundle::class => ['all' => true],
];

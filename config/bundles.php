<?php

use Pimcore\Bundle\StaticRoutesBundle\PimcoreStaticRoutesBundle;
use Sylius\Bundle\ThemeBundle\SyliusThemeBundle;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use Pimcore\Bundle\TinymceBundle\PimcoreTinymceBundle;
use EmailizrBundle\EmailizrBundle;
return [
    PimcoreTinymceBundle::class => ['all' => true],
    TwigComponentBundle::class => ['all' => true],
    SyliusThemeBundle::class => ['all' => true],
    PimcoreStaticRoutesBundle::class => ['all' => true],
    EmailizrBundle::class => ['all' => true],
];

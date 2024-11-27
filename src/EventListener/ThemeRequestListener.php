<?php

namespace App\EventListener;

use Sylius\Bundle\ThemeBundle\Context\SettableThemeContext;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[AsEventListener(
    event: RequestEvent::class,
)]
final class ThemeRequestListener
{
    /** @var ThemeRepositoryInterface */
    private $themeRepository;

    /** @var SettableThemeContext */
    private $themeContext;

    public function __construct(ThemeRepositoryInterface $themeRepository, SettableThemeContext $themeContext)
    {
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }

        if (!$event->getRequest()->attributes->has('_site_path')) {
            return;
        }

        $theme = explode('/', $event->getRequest()->attributes->get('_site_path'))[1];

        if ($this->themeRepository->findOneByName('theme/'. $theme)) $this->themeContext->setTheme($this->themeRepository->findOneByName('theme/'. $theme));
    }
}

<?php
declare(strict_types=1);

namespace ApiBundle\Annotation;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use ApiBundle\Transformer\Scope\ScopeRepository;
use ApiBundle\Transformer\Scope\ScopeInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ScopeListener
 * @package ApiBundle\Annotation
 */
class ScopeListener implements EventSubscriberInterface
{
	/**
	 * @var ScopeRepository
	 */
	private $scopeRepository;

	/**
	 * ScopeListener constructor.
	 *
	 * @param ScopeRepository $scopeRepository
	 */
	public function __construct(ScopeRepository $scopeRepository)
	{
		$this->scopeRepository = $scopeRepository;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			KernelEvents:: CONTROLLER =>
				[
					['applyWidthParameter', -1]
				]
		];
	}

	/**
	 * @param FilterControllerEvent $event
	 */
	public function applyWidthParameter(FilterControllerEvent $event)
	{
		if (!$withs = $event->getRequest()->get('with')) {
			return;
		}

		/** @var ScopeInterface $supportedScope */
		foreach ($withs as $with) {
			foreach ($this->scopeRepository->getSupportedScopes() as $supportedScope) {
				if ($supportedScope->getScopeName() === $with) {
					$this->scopeRepository->addScope($supportedScope->getScopeName());
				}
			}
		}
	}
}
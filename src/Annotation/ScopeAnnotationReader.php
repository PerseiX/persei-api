<?php
declare(strict_types=1);

namespace ApiBundle\Annotation;

use ApiBundle\Reflection\AnnotationsReader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use ApiBundle\Transformer\Scope\AllowedScopesRepository;
use ApiBundle\Transformer\Scope\ScopeRepository;
use ApiBundle\Transformer\Scope\ScopeInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\Common\Annotations\Reader;

/**
 * Class ScopeAnnotationReader
 * @package ApiBundle\Annotation
 */
class ScopeAnnotationReader implements EventSubscriberInterface
{
	/**
	 * @var AnnotationsReader
	 */
	private $annotationReader;

	/**
	 * @var AllowedScopesRepository
	 */
	private $allowedScopeRepository;

	/**
	 * @var ScopeRepository
	 */
	private $scopeRepository;

	/**
	 * ScopeAnnotationReader constructor.
	 *
	 * @param AnnotationsReader       $annotationReader
	 * @param AllowedScopesRepository $allowedScopeRepository
	 * @param ScopeRepository         $scopeRepository
	 */
	public function __construct(AnnotationsReader $annotationReader, AllowedScopesRepository $allowedScopeRepository, ScopeRepository $scopeRepository)
	{
		$this->annotationReader       = $annotationReader;
		$this->allowedScopeRepository = $allowedScopeRepository;
		$this->scopeRepository        = $scopeRepository;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::CONTROLLER =>
				[
					['onKernelController', 1]
				]
		];
	}

	/**
	 * @param FilterControllerEvent $event
	 */
	public function onKernelController(FilterControllerEvent $event)
	{
		if (!is_array($controller = $event->getController()) || true === empty($controller)) {
			return;
		}
		$methodAnnotations = $this->annotationReader->getMethodAnnotations($controller);
		foreach ($methodAnnotations as $configuration) {
			if (true === $configuration instanceof Scope) {
				/** @var ScopeInterface $scope */
				foreach ($this->allowedScopeRepository->getAllowedScopes() as $scope) {
					if ($scope->getScopeName() === $configuration->scope) {
						$this->scopeRepository->addSupportedScopes($scope);
					}
				}
			}
		}
	}

}
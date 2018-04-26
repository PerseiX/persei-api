<?php

namespace ApiBundle\Annotation;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use ApiBundle\Transformer\Scope\AllowedScopesRepository;
use ApiBundle\Transformer\Scope\ScopeRepository;
use ApiBundle\Transformer\Scope\ScopeInterface;
use ApiBundle\Reflection\AnnotationsReader;
use PHPUnit\Framework\TestCase;

/**
 * Class ScopeAnnotationReaderTest
 */
class ScopeAnnotationReaderTest extends TestCase
{
	/**
	 * @var ScopeAnnotationReader
	 */
	private $reader;

	/**
	 * @var AnnotationsReader
	 */
	private $annotationReader;

	/**
	 * @var AllowedScopesRepository
	 */
	private $allowedScopesRepository;

	/**
	 * @var ScopeRepository
	 */
	private $scopeRepository;

	public function setUp()
	{
		$this->annotationReader        = $this->createMock(AnnotationsReader::class);
		$this->allowedScopesRepository = new AllowedScopesRepository();
		$this->scopeRepository         = new ScopeRepository();
		$this->reader                  = new ScopeAnnotationReader($this->annotationReader, $this->allowedScopesRepository, $this->scopeRepository);
	}

	public function testOnKernelController()
	{
		/** @var FilterControllerEvent $event */
		$event = $this->createMock(FilterControllerEvent::class);
		$event->expects($this->once())
		      ->method('getController')
		      ->willReturn('');

		$result = $this->reader->onKernelController($event);
		$this->assertTrue(empty($result));

		/** @var FilterControllerEvent $event */
		$event = $this->createMock(FilterControllerEvent::class);

		$event->expects($this->once())
		      ->method('getController')
		      ->willReturn([]);

		$result = $this->reader->onKernelController($event);
		$this->assertTrue(empty($result));

		/** @var FilterControllerEvent $event */
		$event = $this->createMock(FilterControllerEvent::class);
		$event->expects($this->once())
		      ->method('getController')
		      ->willReturn(['']);

		$scope        = $this->createMock(Scope::class);
		$scope->scope = 'test.scope';
		$scope->value = 'test.value';

		$this->annotationReader
			->expects($this->once())
			->method('getMethodAnnotations')
			->willReturn([$scope]);

		$passedScope = $this->createMock(ScopeInterface::class);
		$passedScope->method('getScopeName')
		            ->willReturn('test.scope');

		$this->allowedScopesRepository->addScope($passedScope);

		$this->reader->onKernelController($event);
		$this->assertTrue($this->scopeRepository->hasSupportedScope('test.scope'));
		$this->assertFalse($this->scopeRepository->hasSupportedScope('fake.scope'));
		$this->assertCount(1, $this->scopeRepository->getSupportedScopes());
	}
}
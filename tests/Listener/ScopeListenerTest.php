<?php

namespace ApiBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use ApiBundle\Transformer\Scope\ScopeRepository;
use ApiBundle\Transformer\Scope\ScopeInterface;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ScopeListener
 * @package ApiBundle\Annotation
 */
class ScopeListenerTest extends TestCase
{
	/**
	 * @var ScopeRepository
	 */
	private $scopeRepository;

	/**
	 * @var MockObject
	 */
	private $event;

	/**
	 * @var ScopeListener
	 */
	private $scopeListener;

	/**
	 * @var Request
	 */
	private $request;

    protected function setUp()
	{
		$this->request         = $this->createMock(Request::class);
		$this->event           = $this->createMock(FilterControllerEvent::class);
		$this->scopeRepository = new ScopeRepository();
		$this->scopeListener   = new ScopeListener($this->scopeRepository);
	}

	public function testShouldDoNothingWithoutWithParameter()
	{
		$this->request->method('get')
		              ->with('with')
		              ->willReturn('');

		$this->event->method('getRequest')
		            ->willReturn($this->request);

		$result = $this->scopeListener->applyWithParameter($this->event);

		$this->assertTrue(empty($result));
	}

	public function testShouldAddOnlySupportedScopes()
	{
		$this->request->method('get')
		              ->with('with')
		              ->willReturn(['scope.category', 'scope.product']);

		$this->event->method('getRequest')
		            ->willReturn($this->request);

		$supportedScope = $this->getMockBuilder(ScopeInterface::class)
		                       ->disableOriginalConstructor()
		                       ->getMock();
		$supportedScope->method('getScopeName')
		               ->willReturn('scope.category');

		$this->scopeRepository->addSupportedScopes($supportedScope);
		$this->scopeListener->applyWithParameter($this->event);

		$this->assertTrue($this->scopeRepository->hasScope('scope.category'));
	}

	public function testShouldThrowExceptionWhenScopeNotSupported()
	{
		$this->request->method('get')
		              ->with('with')
		              ->willReturn(['scope.category']);

		$this->event->method('getRequest')
		            ->willReturn($this->request);

		$this->scopeListener->applyWithParameter($this->event);

		$this->expectException(\Exception::class);
		$this->assertFalse($this->scopeRepository->hasScope('scope.product'));
	}
}

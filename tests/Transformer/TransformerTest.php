<?php

namespace ApiBundle\Transformer;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ApiBundle\Representation\RepresentationInterface;
use ApiBundle\Transformer\Scope\ScopeRepository;
use ApiBundle\Transformer\Scope\ScopeInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class TransformerTest
 */
class TransformerTest extends TestCase
{
	/**
	 * @var MockObject|Transformer
	 */
	private $transformer;

	/**
	 * @var MockObject|TransformerInterface
	 */
	private $mockTransformer;

	/**
	 * @var MockObject|ScopeRepository
	 */
	private $scopeRepository;

    protected function setUp(): void
	{

		$this->scopeRepository = $this->createMock(ScopeRepository::class);
		$this->mockTransformer = $this->createMock(TransformerInterface::class);
		$this->transformer     = new Transformer($this->scopeRepository);
	}

	public function testTransformer()
	{
		$this->mockTransformer
			->expects($this->once())
			->method('support')
			->will($this->returnValue(true));

		$representation = $this->createMock(RepresentationInterface::class);

		$this->mockTransformer
			->expects($this->exactly(1))
			->method('transform')
			->will($this->returnValue($representation));

		$this->transformer->addTransformer($this->mockTransformer);
		$representation = $this->transformer->transform(null);
		$this->assertInstanceOf(RepresentationInterface::class, $representation);
	}

	public function testHandle()
	{
		/** @var RepresentationInterface $representation */
		$representation = $this->createMock(RepresentationInterface::class);
		$scope          = $this->createMock(ScopeInterface::class);
		$scopeName      = 'scope1';

		$this->scopeRepository
			->expects($this->once())
			->method('getScopes')
			->will($this->returnValue([$scopeName]));

		$scope
			->expects($this->once())
			->method('support')
			->with($representation, null)
			->will($this->returnValue(true));

		$this->scopeRepository
			->expects($this->once())
			->method('getSupportedScope')
			->with($scopeName)
			->will($this->returnValue($scope));

		$scope->expects($this->once())
		      ->method('applyScope')
		      ->with($representation, '')
		      ->will($this->returnValue($representation));

		$this->transformer->handle($representation, '');
	}

	public function testHandleNotSupport()
	{
		/** @var RepresentationInterface $representation */
		$representation = $this->createMock(RepresentationInterface::class);
		$scope          = $this->createMock(ScopeInterface::class);
		$scopeName      = 'scope1';

		$this->scopeRepository
			->expects($this->once())
			->method('getScopes')
			->will($this->returnValue([$scopeName]));

		$scope->expects($this->once())
		      ->method('support')
		      ->with($representation, null)
		      ->will($this->returnValue(false));

		$this->scopeRepository
			->expects($this->once())
			->method('getSupportedScope')
			->with($scopeName)
			->will($this->returnValue($scope));

		$scope->expects($this->any())
		      ->method('applyScope')
		      ->with($representation, '')
		      ->will($this->returnValue($representation));

		$this->transformer->handle($representation, '');
	}

	public function testRepresentationResponse()
	{
		$this->transformer->addTransformer($this->mockTransformer);
		$this->mockTransformer
			->expects($this->once())
			->method('support')
			->will($this->returnValue(true));

		$specialTransformer = $this->transformer->getTransformer($this->mockTransformer);
		$representation     = $specialTransformer->transform($this->mockTransformer);
		$this->assertInstanceOf(RepresentationInterface::class, $representation);
	}

	public function testEmptyTransformers()
	{
		$this->expectException(NotFoundHttpException::class);
		$this->transformer->getTransformer(null);
	}

	public function testDoubleTransformers()
	{
		$this->expectException(\LogicException::class);

		$this->transformer->addTransformer($this->mockTransformer);
		$this->transformer->addTransformer($this->mockTransformer);
	}
}

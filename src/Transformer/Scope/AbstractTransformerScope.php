<?php
declare(strict_types=1);

namespace ApiBundle\Transformer\Scope;

use ApiBundle\Transformer\Transformer;

/**
 * Class AbstractTransformerScope
 * @package ApiBundle\Transformer\Scope
 */
abstract class AbstractTransformerScope implements ScopeInterface
{
	/**
	 * @var Transformer
	 */
	private $transformer;

	/**
	 * @return Transformer
	 */
	public function getTransformer(): Transformer
	{
		return $this->transformer;
	}

	/**
	 * @param Transformer $transformer
	 *
	 * @return AbstractTransformerScope
	 */
	public function setTransformer(Transformer $transformer): AbstractTransformerScope
	{
		$this->transformer = $transformer;

		return $this;
	}
}
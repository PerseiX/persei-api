<?php
declare(strict_types=1);

namespace ApiBundle\Transformer;

use ApiBundle\Representation\RepresentationInterface;

/**
 * Interface TransformerInterface
 * @package ApiBundle\Transformer
 */
interface TransformerInterface
{
	/**
	 * @param $input
	 *
	 * @return bool
	 */
	public function support($input): bool;

	/**
	 * @param $input
	 *
	 * @return RepresentationInterface
	 */
	public function transform($input): RepresentationInterface;
}
<?php

namespace ApiBundle\Event;

use ApiBundle\Representation\RepresentationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FormRepresentationEvent
 * @package ApiBundle\Event
 */
class FormRepresentationEvent extends Event
{
	const POST_SUCCESS_REPRESENTATION = 'form.representation.post_success_representation';

	/**
	 * @var RepresentationInterface
	 */
	private $object;

	/**
	 * FormRepresentationEvent constructor.
	 *
	 * @param RepresentationInterface $object
	 */
	public function __construct(RepresentationInterface $object)
	{
		$this->object = $object;
	}

	/**
	 * @return RepresentationInterface
	 */
	public function getObject(): RepresentationInterface
	{
		return $this->object;
	}
	
}
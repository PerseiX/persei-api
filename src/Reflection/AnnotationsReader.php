<?php

namespace ApiBundle\Reflection;

use Doctrine\Common\Annotations\Reader;

/**
 * Class AnnotationsReader
 * @package ApiBundle\Reflection
 */
class AnnotationsReader
{
	/**
	 * @var Reader
	 */
	private $reader;

	/**
	 * AnnotationsReader constructor.
	 *
	 * @param Reader $reader
	 */
	public function __construct(Reader $reader)
	{
		$this->reader = $reader;
	}

	/**
	 * @param $controller
	 *
	 * @return array|void
	 */
	public function getMethodAnnotations($controller)
	{
		if (!isset($controller[0]) && !isset($controller[1])) {
			return;
		}
		$object = new \ReflectionObject($controller[0]);
		$method = $object->getMethod($controller[1]);

		return $this->reader->getMethodAnnotations($method);
	}
}
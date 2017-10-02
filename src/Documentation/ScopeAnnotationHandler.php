<?php
declare(strict_types=1);

namespace ApiBundle\Documentation;

use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Route;
use ApiBundle\Annotation\Scope;

/**
 * Class ScopeDocumentation
 * @package ApiBundle\Documentation
 */
class ScopeAnnotationHandler implements HandlerInterface
{
	/**
	 * @param ApiDoc            $annotation
	 * @param array             $annotations
	 * @param Route             $route
	 * @param \ReflectionMethod $method
	 */
	public function handle(ApiDoc $annotation, array $annotations, Route $route, \ReflectionMethod $method)
	{
		foreach ($annotations as $annotationElement) {
			if ($annotationElement instanceof Scope) {
				$annotation->addParameter('with[]', [
					"dataType"    => "array",
					"required"    => false,
					"description" => "Array of scopes"
				]);
			}
		}
	}
}
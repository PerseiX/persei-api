<?php
declare(strict_types=1);

namespace ApiBundle\Transformer\Scope;

use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class ScopeHandler
 * @package ApiBundle\Transformer\Scope
 */
class ScopeRepository
{
	/**
	 * @var array
	 */
	private $scopes;

	/**
	 * @var array
	 */
	private $supportedScopes;

	/**
	 * ScopeRepository constructor.
	 */
	public function __construct()
	{
		$this->scopes          = [];
		$this->supportedScopes = [];
	}

	/**
	 * @param string $scope
	 */
	public function addScope(string $scope)
	{
		if (!in_array($scope, $this->scopes)) {
			$this->scopes[] = $scope;
		}
	}

	/**
	 * @return array
	 */
	public function getScopes()
	{
		return $this->scopes;
	}

	/**
	 * @param string $scopeName
	 *
	 * @return mixed
	 */
	public function hasScope(string $scopeName)
	{
		if (in_array($scopeName, $this->scopes)) {
			return true;
		}

		throw new Exception(sprintf("Scope with name %s not found", $scopeName));
	}

	/**
	 * @param string $scopeName
	 *
	 * @return bool
	 */
	public function hasSupportedScope(string $scopeName)
	{
		if (key_exists($scopeName, $this->supportedScopes)) {
			return true;
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function getSupportedScopes(): array
	{
		return $this->supportedScopes;
	}

	/**
	 * @param ScopeInterface $supportedScopes
	 */
	public function addSupportedScopes(ScopeInterface $supportedScopes)
	{
		if (!in_array($supportedScopes, $this->supportedScopes)) {
			$this->supportedScopes[$supportedScopes->getScopeName()] = $supportedScopes;
		}
	}

	/**
	 * @param string $scopeName
	 *
	 * @return mixed
	 */
	public function getSupportedScope(string $scopeName)
	{
		if (key_exists($scopeName, $this->supportedScopes)) {
			return $this->supportedScopes[$scopeName];
		}

		throw new Exception(sprintf("Scope with name %s not found", $scopeName));
	}
}
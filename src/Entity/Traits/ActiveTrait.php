<?php
declare(strict_types=1);

namespace ApiBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ActiveTrait
 * @package ApiBundle\Entity\Traits
 */
trait ActiveTrait
{
	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean", options={"default" = true}, nullable=true)
	 */
	protected $active;

	/**
	 * @return bool
	 */
	public function isActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @param bool $active
	 *
	 * @return $this
	 */
	public function setActive(bool $active = null)
	{
		$this->active = $active;

		return $this;
	}
}
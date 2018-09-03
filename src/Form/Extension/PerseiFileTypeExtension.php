<?php

namespace ApiBundle\Form\Extension;

use ApiBundle\Form\Type\PerseiFileType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PerseiFileTypeExtension
 */
class PerseiFileTypeExtension extends AbstractTypeExtension
{
	/**
	 * @return string
	 */
	public function getExtendedType()
	{
		return PerseiFileType::class;
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setRequired('property');
		$resolver->setRequired('uploadPath');
	}

}
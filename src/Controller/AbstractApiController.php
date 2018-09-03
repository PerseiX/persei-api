<?php
declare(strict_types=1);

namespace ApiBundle\Controller;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use ApiBundle\Representation\RepresentationInterface;
use Hateoas\Representation\PaginatedRepresentation;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Request\PaginatedRequest;
use Symfony\Component\Form\Form;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;

/**
 * Class AbstractApiController
 * @package ApiBundle\Controller
 */
abstract class AbstractApiController extends FOSRestController
{
	/**
	 * @param                  $class
	 * @param Query            $query
	 * @param PaginatedRequest $paginatedRequest
	 * @param array            $parameters
	 *
	 * @return Response
	 */
	protected function paginatedResponse($class, Query $query, PaginatedRequest $paginatedRequest, $parameters = [])
	{
		//TODO limit and page default value
		$limit = $paginatedRequest->getLimit();
		$page  = $paginatedRequest->getPage();

		$paginator = $this->get('knp_paginator');

		/** @var SlidingPagination $pagination */
		$pagination = $paginator->paginate($query, $page, $limit, $parameters);

		$representation = $this->get('api.main_transformer')->transform(new $class($pagination->getItems()));

		//TODO maybe builder?
		$paginatedRepresentation = $this->paginatedRequest($paginatedRequest, $parameters, $representation, $page, $limit, $pagination);

		return $this->representationResponse($paginatedRepresentation);
	}

	/**
	 * @param     $input
	 * @param int $status
	 *
	 * @return Response
	 */
	protected function representationResponse($input, $status = Response::HTTP_OK)
	{
		$view = $this->view($input, $status);

		return $this->handleView($view);
	}

	/**
	 * @param $statusCode
	 *
	 * @return Response
	 */
	public function response($statusCode)
	{
		return $this->handleView($this->view(null, $statusCode));
	}

	/**
	 * @param Request $request
	 * @param Form    $form
	 *
	 * @return Response
	 * @throws ORMException
	 */
	protected function formResponse(Request $request, Form $form)
	{

		$form->handleRequest($request);

		$clearMissing = false;
		if ('PUT' === $request->getMethod()) {
			$clearMissing = false;
		}

		if (false === $form->isSubmitted()) {
			$requestData = array_merge($request->request->all(), $request->files->all());
			$form->submit($requestData, $clearMissing);
		}

		if (true === $form->isValid()) {
			$manager = $this->getDoctrine()->getManager();
			$manager->beginTransaction();
			try {
				$this->get('api.form_handler_persei_file_handler')->handle($form);
				$input = $form->getData();

				$manager->persist($input);
				$manager->flush();
				$manager->commit();
			} catch
			(ORMException $exception) {
				$manager->rollback();
				throw $exception;
			}

			return $this->representationResponse($this->get('api.main_transformer')->transform($input));
		}

		return $this->formErrorsResponse($form);
	}

	/**
	 * @param Form $form
	 *
	 * @return Response
	 */
	protected
	function formErrorsResponse(Form $form)
	{
		$view = $this->view($this->getErrorMessages($form), 400);

		return $this->handleView($view);
	}

	/**
	 * @param Form $form
	 *
	 * @return array
	 */
	protected
	function getErrorMessages(Form $form)
	{
		$errors = [];

		foreach ($form->getErrors() as $key => $error) {
			if ($form->isRoot()) {
				$errors['#'][] = $error->getMessage();
			} else {
				$errors[] = $error->getMessage();
			}
		}

		/** @var Form $child */
		foreach ($form->all() as $child) {
			if (!$child->isValid()) {
				$errors[$child->getName()] = $this->getErrorMessages($child);
			}
		}

		return $errors;
	}

	/**
	 * @param $entity
	 */
	protected
	function updateEntity($entity)
	{
		$em = $this->getDoctrine()->getManager();
		$em->persist($entity);
		$em->flush();
	}

	/**
	 * @param $input
	 *
	 * @return RepresentationInterface
	 */
	protected
	function transform($input): RepresentationInterface
	{
		return $representation = $this->get('api.main_transformer')->transform($input);
	}

	/**
	 * @param PaginatedRequest $paginatedRequest
	 * @param                  $parameters
	 * @param                  $representation
	 * @param                  $page
	 * @param                  $limit
	 * @param                  $pagination
	 *
	 * @return PaginatedRepresentation
	 */
	protected
	function paginatedRequest(PaginatedRequest $paginatedRequest, $parameters, $representation, $page, $limit, $pagination): PaginatedRepresentation
	{
		$paginatedRepresentation = new PaginatedRepresentation(
			$representation,
			$paginatedRequest->getRouter(),
			$parameters,
			$page,
			$limit,
			ceil($pagination->getTotalItemCount() / $limit),
			null,
			null,
			false,
			$pagination->getTotalItemCount()
		);

		return $paginatedRepresentation;
	}
}
<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/7/18
 * Time: 3:22 PM
 */

namespace PapaLocal\Controller;


use PapaLocal\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;


/**
 * Trait FormHandlerControllerTrait
 *
 * @package PapaLocal\Controller
 */
trait FormHandlerControllerTrait
{
    /**
     * Verify a form's CSRF token is correct.
     *
     * @param string $formName
     * @param Request $request
     *
     * @throws \Exception
     */
    public function validateFormToken(string $formName, Request $request)
    {
        $username = ($this->getUser() instanceof User ) ? $this->getUser()->getUsername() : $request->getClientIp();

        // validate form token
        if (! $this->isCsrfTokenValid($formName, $request->request->get('_csrf_token'))) {
            throw new \Exception(
                sprintf('Token supplied by %s is not valid. Form name: %s, Token: %s.',
                $username, $formName, $request->request->get('_csrf_token')));
        };
    }

    /**
     * Generates a response for requests that contain validation errors.
     *
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function handleValidationErrors(ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = array();
            foreach ($validationErrors as $error) {
                array_push($errors, $error->getMessage());
            }

            return new JsonResponse(array('validationErrors' => $errors),
                JsonResponse::HTTP_BAD_REQUEST);
        }
    }

}
<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/8/17
 */


namespace PapaLocal\Controller\Api;


use PapaLocal\Entity\LogStatement;
use FOS\RestBundle\Controller\FOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * Class SystemController.
 *
 * @Route("/system")
 * @Rest\RouteResource("/system", pluralize=false)
 */
class SystemController extends FOSRestController
{
    /**
     * @Rest\View()
     * @Rest\Post("/log")
     * @ParamConverter("statement", class="PapaLocal\Entity\LogStatement", converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"create"}}})
     *
     * @param Request                          $request
     * @param LogStatement                     $statement
     * @param ConstraintViolationListInterface $validationErrors
     * @param TokenStorageInterface            $tokenStorage
     * @param LoggerInterface                  $logger
     *
     * @return JsonResponse
     */
    public function log(Request $request,
                        LogStatement $statement,
                        ConstraintViolationListInterface $validationErrors,
                        TokenStorageInterface $tokenStorage,
                        LoggerInterface $logger)
    {
        //return validation errors to form
        if (count($validationErrors) > 0) {
            $errors = array();
            foreach ($validationErrors as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse(array('validationErrors' => $errors), 400);
        }

        //write requested log statement
        $level = $statement->getLevel();
        $logger->$level('[USER INTERFACE][' . $tokenStorage->getToken()->getUser()->getUsername() . ']: ' . $statement->getMessage());

        //return response
        return new JsonResponse(array('message' => 'success'), 200);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/10/19
 * Time: 2:09 PM
 */

namespace PapaLocal\Controller\Api;


use FOS\RestBundle\Controller\FOSRestController;
use PapaLocal\Controller\FormHandlerControllerTrait;
use PapaLocal\Response\RestResponseMessage;
use PapaLocal\Stripe\Message\MessageFactory;
use FOS\RestBundle\Controller\Annotations as Rest;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class StripeController
 * @package PapaLocal\Controller\Api
 */
class StripeController extends FOSRestController
{
    use FormHandlerControllerTrait;

    /**
     * @var MessageBusInterface
     */
    private $applicationBus;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * StripeController constructor.
     * @param MessageBusInterface $applicationBus
     * @param MessageFactory $messageFactory
     */
    public function __construct(MessageBusInterface $applicationBus, MessageFactory $messageFactory)
    {
        $this->applicationBus = $applicationBus;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @Rest\Post("/stripe/account/update")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAccount(Request $request)
    {
        dump($request);
        return new JsonResponse(null, 200, array());
    }
}
<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 5/11/18
 */


namespace PapaLocal\Controller\Api\Billing;


use PapaLocal\Billing\Form\UpdateRechargeSetting;
use PapaLocal\Billing\Message\MessageFactory;
use PapaLocal\Controller\FormHandlerControllerTrait;
use PapaLocal\Response\RestResponseMessage;
use FOS\RestBundle\Controller\FOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;


/**
 * Class BillingProfileController.
 *
 * @package PapaLocal\Controller\Api\Billing
 */
class BillingProfileController extends FOSRestController
{
    use FormHandlerControllerTrait;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MessageBusInterface
     */
    private $applicationBus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BillingProfileController constructor.
     *
     * @param MessageFactory      $messageFactory
     * @param MessageBusInterface $applicationBus
     * @param LoggerInterface     $logger
     */
    public function __construct(MessageFactory $messageFactory, MessageBusInterface $applicationBus, LoggerInterface $logger)
    {
        $this->messageFactory = $messageFactory;
        $this->applicationBus = $applicationBus;
        $this->logger = $logger;
    }

    /**
     * @Rest\Post("/billing/account/recharge-setting")
     * @ParamConverter("form", class="PapaLocal\Billing\Form\UpdateRechargeSetting", converter="fos_rest.request_body")
     *
     * @param Request                 $request
     * @param UpdateRechargeSetting   $form
     * @param ConstraintViolationList $validationErrors
     *
     * @return JsonResponse
     */
    public function saveRechargeSettings(Request $request,
                                         UpdateRechargeSetting $form,
                                         ConstraintViolationList $validationErrors)
    {

        try {
            // validate CSRF token
            $this->validateFormToken('accountProfileRecharge', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update recharge setting
            $updateCmd = $this->messageFactory->newUpdateRechargeSetting($form->getUserGuid(), $form->getMinBalance(), $form->getMaxBalance());
            $this->applicationBus->dispatch($updateCmd);

            return new JsonResponse(array('message' => sprintf('Your default pay method will automatically be charged each time your account falls below $%0.2f. The amount charged will be the amount required to bring your balance to $%0.2f', $form->getMinBalance(), $form->getMaxBalance()),
                'minBalance' => $form->getMinBalance(),
                'maxBalance' => $form->getMaxBalance()),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {

            // an exception occurred
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
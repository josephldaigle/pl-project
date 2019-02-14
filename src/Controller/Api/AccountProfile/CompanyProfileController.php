<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/19/18
 * Time: 2:30 PM
 */


namespace PapaLocal\Controller\Api\AccountProfile;


use PapaLocal\Core\Data\HydratorRegistry;
use PapaLocal\Core\Data\RepositoryRegistry;
use PapaLocal\Data\Hydrate\Company\CompanyHydrator;
use PapaLocal\Entity\Exception\CompanyNameExistsException;
use PapaLocal\IdentityAccess\Data\Repository\CompanyRepository;
use PapaLocal\Entity\Company;
use PapaLocal\IdentityAccess\Form\Company\CreateCompany;
use PapaLocal\IdentityAccess\Form\Company\UpdateCompanyEmail;
use PapaLocal\IdentityAccess\Form\Company\UpdateCompanyPhone;
use PapaLocal\IdentityAccess\Form\Company\UpdateCompanyWebsite;
use PapaLocal\IdentityAccess\Form\Company\UpdateName;
use PapaLocal\IdentityAccess\Form\Company\SaveCompanyAddressForm;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\Response\ResponseMessage;
use PapaLocal\Response\RestResponseMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use FOS\RestBundle\Request\RequestBodyParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use PapaLocal\Controller\FormHandlerControllerTrait;
use FOS\RestBundle\Controller\FOSRestController;


/**
 * CompanyProfileController.
 *
 * @package PapaLocal\Controller\Api\AccountProfile
 */
class CompanyProfileController extends FOSRestController
{
    use FormHandlerControllerTrait;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CompanyProfileController constructor.
     *
     * @param MessageFactory      $messageFactory
     * @param MessageBusInterface $appBus
     * @param LoggerInterface     $logger
     */
    public function __construct(MessageFactory $messageFactory, MessageBusInterface $appBus, LoggerInterface $logger)
    {
        $this->messageFactory = $messageFactory;
        $this->appBus         = $appBus;
        $this->logger         = $logger;
    }

    /**
     * Choose a company from the view selector.
     *
     * @Rest\Post("/company/profile")
     * @ParamConverter("company", class="PapaLocal\Entity\Company", converter="fos_rest.request_body")
     *
     * @param Company            $company
     * @param RepositoryRegistry $repositoryRegistry
     * @param HydratorRegistry   $hydratorRegistry
     * @param SessionInterface   $session
     *
     * @return JsonResponse
     */
    public function selectCompany(
        Company $company,
        RepositoryRegistry $repositoryRegistry,
        HydratorRegistry $hydratorRegistry,
        SessionInterface $session
    )
    {
        try {
            // find the company selected
            $company         = $repositoryRegistry->get(CompanyRepository::class)->loadCompany($company->getId());
            $companyHydrator = $hydratorRegistry->get(CompanyHydrator::class);
            $companyHydrator->setEntity($company);
            $company = $companyHydrator->hydrate(true);

            // set the company in the session
            $session->set('company_view', $company);

            // return the company's profile
            return new JsonResponse(array(
                'message' => 'Update completed successfully.',
                'view'    => $this->renderView('forms/editable/companyBasicDetail.html.twig',
                    array('company' => $company)),
            ),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Add a company to a user's profile.
     *
     * @Rest\Post("company/add")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\Company\CreateCompany", converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param CreateCompany                    $form
     * @param ConstraintViolationListInterface $validationErrors
     * @param TokenStorageInterface            $tokenStorage
     *
     * @return JsonResponse
     */
    public function createCompany(
        Request $request,
        CreateCompany $form,
        ConstraintViolationListInterface $validationErrors,
        TokenStorageInterface $tokenStorage
    )
    {
        try {

            // validate CSRF token
            $this->validateFormToken('addCompany', $request);

            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            $ownerUserGuid = $tokenStorage->getToken()->getUser()->getGuid();

            $createCompanyCmd = $this->messageFactory->newCreateCompany($ownerUserGuid, $form);
            $this->appBus->dispatch($createCompanyCmd);

            return new JsonResponse(array('message' => ResponseMessage::UPDATE_SUCCESSFULL),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a company name.
     *
     * @Rest\Post("/company/name/save")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\Company\UpdateName", converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateName                       $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function saveCompanyName(
        Request $request,
        UpdateName $form,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfileCompanyName', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update the company's name
            $command = $this->messageFactory->newUpdateCompanyName($form->getGuid(), $form->getName());
            $this->appBus->dispatch($command);

            // return response
            return new JsonResponse(array('message' => ResponseMessage::UPDATE_SUCCESSFULL),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a company's phone number.
     *
     * @Rest\Post("/company/phone-number/save")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\Company\UpdateCompanyPhone", converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateCompanyPhone               $form
     * @param ConstraintViolationListInterface $validationErrors
     * @param RepositoryRegistry               $repositoryRegistry
     *
     * @return JsonResponse
     */
    public function saveCompanyPhoneNumber(
        Request $request,
        UpdateCompanyPhone $form,
        ConstraintViolationListInterface $validationErrors,
        RepositoryRegistry $repositoryRegistry
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfilePhoneNumber', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update the phone number
            $updatePhoneCommand = $this->messageFactory->newUpdateCompanyPhoneNumber($form->getCompanyGuid(), $form->getPhoneNumber(), $form->getType());
            $this->appBus->dispatch($updatePhoneCommand);

            return new JsonResponse(array('message' => ResponseMessage::UPDATE_SUCCESSFULL),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a company's email address.
     *
     * @Rest\Post("/company/email/save")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\Company\UpdateCompanyEmail", converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateCompanyEmail               $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function saveEmailAddress(
        Request $request,
        UpdateCompanyEmail $form,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfileEmail', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update company email address
            $updateEmailCmd = $this->messageFactory->newUpdateCompanyEmailAddress($form->getGuid(), $form->getEmailAddress(), $form->getType());
            $this->appBus->dispatch($updateEmailCmd);

            return new JsonResponse(array('message' => ResponseMessage::UPDATE_SUCCESSFULL),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a company's address.
     *
     * @Rest\Post("/company/address/save")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\Company\SaveCompanyAddressForm", converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param SaveCompanyAddressForm           $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function saveAddress(
        Request $request,
        SaveCompanyAddressForm $form,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('companyProfileAddress', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) { return $response; }

            $updateAddrCmd = $this->messageFactory->newUpdateCompanyAddress($form->getCompanyGuid(), $form->getStreetAddress(), $form->getCity(), $form->getState(), $form->getPostalCode(), $form->getCountry(), $form->getType());
            $this->appBus->dispatch($updateAddrCmd);

            return new JsonResponse(array('message' => ResponseMessage::UPDATE_SUCCESSFULL),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Save a company's website.
     *
     * @Rest\Post("/company/website/save")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\Company\UpdateCompanyWebsite", converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateCompanyWebsite             $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function saveWebsite(
        Request $request,
        UpdateCompanyWebsite $form,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfileWebsite', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update company website
            $updateWebsiteCmd = $this->messageFactory->newUpdateCompanyWebsite($form->getCompanyGuid(), $form
            ->getWebsite());
            $this->appBus->dispatch($updateWebsiteCmd);

            return new JsonResponse(array('message' => ResponseMessage::UPDATE_SUCCESSFULL),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
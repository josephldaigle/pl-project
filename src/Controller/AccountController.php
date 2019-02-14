<?php
/**
 * Created by Ewebify, LLC.
 * Date: 10/17/17
 * Time: 9:27 PM
 */


namespace PapaLocal\Controller;


use PapaLocal\Core\Data\Exception\QueryException;
use PapaLocal\Core\Data\Exception\QueryExceptionCode;
use PapaLocal\Data\Hydrate\Company\CompanyHydrator;
use PapaLocal\Billing\Data\BillingProfileRepository;
use PapaLocal\Billing\Data\TransactionRepository;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * AccountController.
 *
 * @Route("/account")
 *
 * @package PapaLocal\Controller
 */
class AccountController extends AbstractController
{
    /**
     * @var MessageFactory
     */
    private $iaMessageFactory;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AccountController constructor.
     *
     * @param MessageFactory      $iaMessageFactory
     * @param MessageBusInterface $appBus
     * @param LoggerInterface     $logger
     */
    public function __construct(MessageFactory $iaMessageFactory, MessageBusInterface $appBus, LoggerInterface $logger)
    {
        $this->iaMessageFactory = $iaMessageFactory;
        $this->appBus = $appBus;
        $this->logger = $logger;
    }

    /**
     * @Route("/profile", name="account_profile", methods={"GET"})
     *
     * @param Request                  $request
     * @param TokenStorageInterface    $tokenStorage
     * @param BillingProfileRepository $billingProfileRepository
     * @param TransactionRepository    $transactionRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function accountProfile(Request $request,
                                TokenStorageInterface $tokenStorage,
                                BillingProfileRepository $billingProfileRepository,
								TransactionRepository $transactionRepository)
    {
        // fetch the user
        $token = $tokenStorage->getToken();
        $user = $token->getUser();

        $company = null;
        $billingProfile = null;

        try {
            // fetch users owned companies from database
            $findCoQry = $this->iaMessageFactory->newFindCompanyByUserGuid($user->getGuid());
            $company = $this->appBus->dispatch($findCoQry);

        } catch (QueryException $queryException) {
            $this->logger->warn(sprintf('An exception occurred at line %s of file %s: %s', $queryException->getLine(), $queryException->getFile(), $queryException->getMessage()), array($queryException));
        }

        // load payment profile
        $user->setBillingProfile($billingProfileRepository->loadBillingProfile($user->getId(), true));
        $user->getBillingProfile()->setTransactionList($transactionRepository->loadUsersTransactions($user->getId()));
		$user->getBillingProfile()->setPastYearTransactionSummary($transactionRepository->loadPastYearMonthlySummaryList($user->getId()));

        return $this->render('pages/accountProfile.html.twig', array(
            'company' => $company,
            'billingProfile' => $user->getBillingProfile()
        ));
    }
}
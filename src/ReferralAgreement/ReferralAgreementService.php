<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 9/20/18
 */


namespace PapaLocal\ReferralAgreement;


use PapaLocal\Core\Service\ServiceInterface;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\IdentityAccess\ValueObject\SystemAdmin;
use PapaLocal\Notification\Notifier;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\MessageFactory;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Exception\AgreementExistsException;
use PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException;
use PapaLocal\ReferralAgreement\Notification\NotificationFactory;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;
use Symfony\Component\Workflow\Registry;


/**
 * Class ReferralAgreementService.
 *
 * @package PapaLocal\ReferralAgreement
 */
class ReferralAgreementService implements ServiceInterface
{
    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * @var ReferralAgreementRepository
     */
    private $referralAgreementRepository;

    /**
     * @var InviteeRepository
     */
    private $inviteeRepository;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var MessageFactory
     */
    private $refAgmtMsgFactory;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var NotificationFactory
     */
    private $notificationFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ReferralAgreementService constructor.
     *
     * @param Registry                    $workflowRegistry
     * @param ReferralAgreementRepository $referralAgreementRepository
     * @param InviteeRepository           $inviteeRepository
     * @param MessageBusInterface         $mysqlBus
     * @param MessageFactory              $refAgmtMsgFactory
     * @param Notifier                    $notifier
     * @param NotificationFactory         $notificationFactory
     * @param LoggerInterface             $logger
     */
    public function __construct(Registry $workflowRegistry, ReferralAgreementRepository $referralAgreementRepository, InviteeRepository $inviteeRepository, MessageBusInterface $mysqlBus, MessageFactory $refAgmtMsgFactory, Notifier $notifier, NotificationFactory $notificationFactory, LoggerInterface $logger)
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->referralAgreementRepository = $referralAgreementRepository;
        $this->inviteeRepository = $inviteeRepository;
        $this->mysqlBus = $mysqlBus;
        $this->refAgmtMsgFactory = $refAgmtMsgFactory;
        $this->notifier = $notifier;
        $this->notificationFactory = $notificationFactory;
        $this->logger = $logger;
    }

    /**
     * Creates a referral agreement for a user or administrator(on behalf of user).
     *
     * @param ReferralAgreement $referralAgreement
     * @param GuidInterface              $userId
     *
     * @throws \Exception
     * @throws AgreementExistsException         If the $referralAgreement cannot be uniquely identified from an
     *                                          existing agreement.
     * @throws NotEnabledTransitionException    If the Referral Agreement cannot be created. This exception
     *                                          contains a TransitionBlockerList and each Blocker in the list has a
     *                                          code to help clients determine root causality of the block.
     */
    public function createReferralAgreement(ReferralAgreement $referralAgreement, GuidInterface $userId)
    {
        try {
            // save agreement in database, using workflow
            $workflow = $this->workflowRegistry->get($referralAgreement, 'referral_agreement');
            $workflow->apply($referralAgreement, 'create');

        } catch (AgreementExistsException $agreementExistsException) {
            $this->logger->error(sprintf(
                'User %s attempted to create a %s using an existing guid: %s',
                $userId->value(),
                ReferralAgreement::class,
                $referralAgreement->getGuid()->value()));

            throw $agreementExistsException;

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), array('exception' => $exception, 'trace' => $exception->getTrace(), 'agreement' => $referralAgreement));

            throw $exception;
        }

        return;

    }

    /**
     * @param GuidInterface $agreementGuid
     * @param string        $name
     *
     * @throws AgreementNotFoundException
     */
    public function updateReferralAgreementName(GuidInterface $agreementGuid, string $name)
    {
        // load the agreement
        $agreement = $this->referralAgreementRepository->findByGuid($agreementGuid);
        $invitees = $this->inviteeRepository->findAllByAgreementGuid($agreementGuid);

        // invoke repo call
        $updateNameCommand = $this->refAgmtMsgFactory->newUpdateAgreementName($agreementGuid, $name);
        $this->mysqlBus->dispatch($updateNameCommand);

        // notify participants of change
        foreach ($invitees as $invitee) {
            if ($invitee->isParticipant()) {
                try {

                    $notification = $this->notificationFactory->newAgreementNameChanged($invitee->getEmailAddress()->getEmailAddress(), $agreement->getName(), $name);

                    $this->notifier->sendUserNotification($invitee->getUserId(), $notification);

                } catch (\Exception $exception) {
                    $this->logger->debug(sprintf('An exception occurred while sending out notifications: %s.', $exception->getMessage()), array('context' => $exception, 'trace' => $exception->getTrace()));
                }
            }
        }

        return;
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param string        $description
     *
     * @throws AgreementNotFoundException
     */
    public function updateAgreementDescription(GuidInterface $agreementGuid, string $description)
    {
        // load the agreement
        $agreement = $this->referralAgreementRepository->findByGuid($agreementGuid);
        $invitees = $this->inviteeRepository->findAllByAgreementGuid($agreementGuid);

        // update the agreement
        $updateDescriptionCommand = $this->refAgmtMsgFactory->newUpdateAgreementDescription($agreementGuid, $description);
        $this->mysqlBus->dispatch($updateDescriptionCommand);

        // notify participants of change
        foreach ($invitees as $invitee) {
            if ($invitee->isParticipant()) {
                try {

                    $notification = $this->notificationFactory->newAgreementChanged($invitee->getEmailAddress()->getEmailAddress(), $agreement->getName(), 'description', $agreement->getDescription(), $description);

                    $this->notifier->sendUserNotification($invitee->getUserId(), $notification);

                } catch (\Exception $exception) {
                    $this->logger->debug(sprintf('An exception occurred while sending out notifications: %s.', $exception->getMessage()), array('context' => $exception, 'trace' => $exception->getTrace()));
                }
            }
        }

        return;
    }


    /**
     * @param GuidInterface $agreementGuid
     *
     * @throws AgreementNotFoundException
     */
    public function publishAgreement(GuidInterface $agreementGuid)
    {
        // load the agreement
        $referralAgreement = $this->referralAgreementRepository->findByGuid($agreementGuid);
        $invitees = $this->inviteeRepository->findAllByAgreementGuid($agreementGuid);
        $referralAgreement->setInvitees($invitees);

        // start publish workflow (invokes 'invite' transition for invitees
        $workflow = $this->workflowRegistry->get($referralAgreement, 'referral_agreement');
        $workflow->apply($referralAgreement, 'publish');

        // notifications are embedded in workflow handlers

        return;
    }

    /**
     * Transitions a ReferralAgreement to an inactive state,
     * and records the status change in the history.
     *
     * @param GuidInterface   $agreementGuid
     * @param AgreementStatus $status
     *
     * @throws AgreementNotFoundException
     */
    public function pauseAgreement(GuidInterface $agreementGuid, AgreementStatus $status)
    {
        // prepare workflow subject
        $referralAgreement = $this->referralAgreementRepository->findByGuid($agreementGuid);
        if ($referralAgreement->getCurrentPlace() === 'Inactive') {
            return;
        }
        $referralAgreement->updateStatus($status);

        // invoke workflow
        $workflow = $this->workflowRegistry->get($referralAgreement, 'referral_agreement');
        $workflow->apply($referralAgreement, 'pause');

        $invitees = $this->inviteeRepository->findAllByAgreementGuid($referralAgreement->getGuid());

        // notify participants of change
        foreach ($invitees as $invitee) {
            if ($invitee->isParticipant()) {
                try {

                    $notification = $this->notificationFactory->newAgreementStatusChanged($invitee->getEmailAddress()->getEmailAddress(), $referralAgreement->getName(), $referralAgreement->getStatusHistory()->getCurrentStatus());

                    $this->notifier->sendUserNotification($invitee->getUserId(), $notification);

                } catch (\Exception $exception) {
                    $this->logger->debug(sprintf('An exception occurred while sending out notifications: %s.', $exception->getMessage()), array('context' => $exception, 'trace' => $exception->getTrace()));
                }
            }
        }

        return;
    }

    /**
     * Activate a paused agreement.
     *
     * @param GuidInterface   $agreementGuid
     * @param AgreementStatus $status
     *
     * @throws AgreementNotFoundException
     */
    public function activateAgreement(GuidInterface $agreementGuid, AgreementStatus $status)
    {
        // prepare workflow subject
        $referralAgreement = $this->referralAgreementRepository->findByGuid($agreementGuid);
        $referralCount = $this->referralAgreementRepository->getCurrentPeriodReferralCount($agreementGuid);
        $referralAgreement->setReferralCount($referralCount);
        if ($referralAgreement->getCurrentPlace() === 'Active') {
            // current status is same as requested status
            return;
        }
        $referralAgreement->updateStatus($status);
        $invitees = $this->inviteeRepository->findAllByAgreementGuid($referralAgreement->getGuid());
        $referralAgreement->setInvitees($invitees);

        // invoke workflow
        $workflow = $this->workflowRegistry->get($referralAgreement, 'referral_agreement');
        $workflow->apply($referralAgreement, 'activate');

        return;
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param int           $quantity
     *
     * @throws AgreementNotFoundException
     */
    public function updateQuantity(GuidInterface $agreementGuid, int $quantity)
    {
        // prepare workflow subject
        $referralAgreement = $this->referralAgreementRepository->findByGuid($agreementGuid);
        if ($referralAgreement->getQuantity() === $quantity) {
            // current quantity is same as requested quantity
            return;
        }

        $updateQtyCmd = $this->refAgmtMsgFactory->newUpdateAgreementQuantity($agreementGuid, $quantity);
        $this->mysqlBus->dispatch($updateQtyCmd);

        $invitees = $this->inviteeRepository->findAllByAgreementGuid($agreementGuid);
        // notify participants of change
        foreach ($invitees as $invitee) {
            if ($invitee->isParticipant()) {
                try {

                    $notification = $this->notificationFactory->newAgreementChanged($invitee->getEmailAddress()->getEmailAddress(), $referralAgreement->getName(), 'quantity', $referralAgreement->getQuantity(), $quantity);

                    $this->notifier->sendUserNotification($invitee->getUserId(), $notification);

                } catch (\Exception $exception) {
                    $this->logger->debug(sprintf('An exception occurred while sending out notifications: %s.', $exception->getMessage()), array('context' => $exception, 'trace' => $exception->getTrace()));
                }
            }
        }

        // if current count is over agreement limit, change agreement status to paused
        if ($quantity <= $this->referralAgreementRepository->getCurrentPeriodReferralCount($agreementGuid)) {
            $agreementStatus = $this->serializer->denormalize(array(
                'agreementId' => array('value' => $agreementGuid->value()),
                'status'      => array('value' => Status::INACTIVE()->getValue()),
                'reason'      => array('value' => StatusChangeReason::REFERRAL_QUOTA_REACHED()->getValue()),
                'updater'     => array('value' => SystemAdmin::GUID),
                'timeUpdated' => date('Y-m-d H:i:s', time())
            ), AgreementStatus::class, 'array');

            $this->pauseAgreement($agreementGuid, $agreementStatus);
        }
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param string        $strategy
     *
     * @throws AgreementNotFoundException
     */
    public function updateStrategy(GuidInterface $agreementGuid, string $strategy)
    {
        // prepare workflow subject
        $referralAgreement = $this->referralAgreementRepository->findByGuid($agreementGuid);
        if ($referralAgreement->getStrategy() === $strategy) {
            // current quantity is same as requested quantity
            return;
        }

        $updateStrategyCmd = $this->refAgmtMsgFactory->newUpdateAgreementStrategy($agreementGuid, $strategy);
        $this->mysqlBus->dispatch($updateStrategyCmd);

        $invitees = $this->inviteeRepository->findAllByAgreementGuid($agreementGuid);

        // notify participants of change
        foreach ($invitees as $invitee) {
            if ($invitee->isParticipant()) {
                try {

                    $notification = $this->notificationFactory->newAgreementChanged($invitee->getEmailAddress()->getEmailAddress(), $referralAgreement->getName(), 'strategy', $referralAgreement->getStrategy(), $strategy);

                    $this->notifier->sendUserNotification($invitee->getUserId(), $notification);

                } catch (\Exception $exception) {
                    $this->logger->debug(sprintf('An exception occurred while sending out notifications: %s.', $exception->getMessage()), array('context' => $exception, 'trace' => $exception->getTrace()));
                }
            }
        }

        // if change puts agreement over limit - then pause it.
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param float         $price
     *
     * @throws AgreementNotFoundException
     */
    public function updateReferralPrice(GuidInterface $agreementGuid, float $price)
    {
        // prepare workflow subject
        $referralAgreement = $this->referralAgreementRepository->findByGuid($agreementGuid);
        if ($referralAgreement->getBid() === $price) {
            // current price is same as requested price
            return;
        }

        $updatePriceCmd = $this->refAgmtMsgFactory->newUpdateReferralPrice($agreementGuid, $price);
        $this->mysqlBus->dispatch($updatePriceCmd);

        $invitees = $this->inviteeRepository->findAllByAgreementGuid($agreementGuid);

        // notify participants of change
        foreach ($invitees as $invitee) {
            if ($invitee->isParticipant()) {
                try {

                    $notification = $this->notificationFactory->newAgreementChanged($invitee->getEmailAddress()->getEmailAddress(), $referralAgreement->getName(), 'price', $referralAgreement->getBid(), $price);

                    $this->notifier->sendUserNotification($invitee->getUserId(), $notification);

                } catch (\Exception $exception) {
                    $this->logger->debug(sprintf('An exception occurred while sending out notifications: %s.', $exception->getMessage()), array('context' => $exception, 'trace' => $exception->getTrace()));
                }
            }
        }
        // if change puts agreement over limit - then pause it.
    }

    /**
     * @param GuidInterface      $agreementGuid
     * @param IncludeExcludeList $locationList
     *
     * @throws AgreementNotFoundException
     */
    public function updateLocations(GuidInterface $agreementGuid, IncludeExcludeList $locationList)
    {
        // prepare workflow subject
        $referralAgreement = $this->referralAgreementRepository->findByGuid($agreementGuid);

        if ($locationList->getIncludes()->count() > 0) {
            // updating includes
            $locationList->addAll($referralAgreement->getExcludedLocations());
        } else {
            // updating excludes
            $locationList->addAll($referralAgreement->getIncludedLocations());
        }

        $updateLocationsCmd = $this->refAgmtMsgFactory->newUpdateLocations($agreementGuid, $locationList);
        $this->mysqlBus->dispatch($updateLocationsCmd);

        $invitees = $this->inviteeRepository->findAllByAgreementGuid($agreementGuid);

        // notify participants of change
        foreach ($invitees as $invitee) {
            if ($invitee->isParticipant()) {
                try {
                    $notification = $this->notificationFactory->newAgreementListUpdated($invitee->getEmailAddress()->getEmailAddress(), $referralAgreement->getName(), 'location', $locationList);

                    $this->notifier->sendUserNotification($invitee->getUserId(), $notification);

                } catch (\Exception $exception) {
                    $this->logger->debug(sprintf('An exception occurred while sending out notifications: %s.', $exception->getMessage()), array('context' => $exception, 'trace' => $exception->getTrace()));
                }
            }
        }
    }
}
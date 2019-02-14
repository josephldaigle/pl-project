<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/4/18
 * Time: 11:12 AM
 */

namespace PapaLocal\Referral\Event;


use PapaLocal\IdentityAccess\Event\UserRegistered;
use PapaLocal\Referral\Data\ReferralRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Registry;


/**
 * Class ContactRegisteredSubscriber
 * @package PapaLocal\Referral\Event
 */
class ContactRegisteredSubscriber implements EventSubscriberInterface
{
    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * ContactRegisteredSubscriber constructor.
     * @param ReferralRepository $referralRepository
     * @param Registry $workflowRegistry
     */
    public function __construct(ReferralRepository $referralRepository, Registry $workflowRegistry)
    {
        $this->referralRepository = $referralRepository;
        $this->workflowRegistry = $workflowRegistry;
    }

    public function acquireReferral(UserRegistered $event)
    {
        $referralCollection = $this->referralRepository->fetchByRecipientEmailAddress($event->getUsername());

        foreach ($referralCollection as $referral) {
            $referral->getRecipient()->setContactGuid($event->getUserGuid());

            $workflow = $this->workflowRegistry->get($referral);
            $workflow->apply($referral, 'acquire');
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegistered::class => array('acquireReferral')
        ];
    }
}
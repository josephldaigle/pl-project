<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/4/18
 * Time: 9:14 PM
 */


namespace PapaLocal\ReferralAgreement\Workflow\Invitee;


use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Exception\InviteeExistsForAgreementException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;


/**
 * Class CreateGuardSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Invitee
 */
class CreateGuardSubscriber implements EventSubscriberInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var InviteeRepository
     */
    private $inviteeRepository;

    /**
     * CreateGuardSubscriber constructor.
     *
     * @param ValidatorInterface $validator
     * @param InviteeRepository  $inviteeRepository
     */
    public function __construct(ValidatorInterface $validator, InviteeRepository $inviteeRepository)
    {
        $this->validator = $validator;
        $this->inviteeRepository = $inviteeRepository;
    }

    /**
     * @param GuardEvent $event
     *
     * @throws InviteeExistsForAgreementException
     */
    public function guardReview(GuardEvent $event)
    {
        $invitee = $event->getSubject();
        $errors = $this->validator->validate($invitee, null, array('create'));

        if ($errors->count() > 0) {
            $event->addTransitionBlocker(new TransitionBlocker($errors->get(0)->getMessage(), 0));
        }

        // save invitee
        $agmtInvitees = $this->inviteeRepository->findAllByAgreementGuid($invitee->getAgreementId());

        foreach ($agmtInvitees as $invitation) {
            if ($invitee->getEmailAddress()->getEmailAddress() === $invitation->getEmailAddress()->getEmailAddress()) {
                throw new InviteeExistsForAgreementException(sprintf('An invitee with email address %s has already been added to the agreement.', $invitee->getEmailAddress()->getEmailAddress()));
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.agreement_invitee.guard.create' => 'guardReview'
        ];
    }


}
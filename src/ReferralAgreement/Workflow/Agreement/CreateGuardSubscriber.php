<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/20/18
 * Time: 11:01 PM
 */


namespace PapaLocal\ReferralAgreement\Workflow\Agreement;


use PapaLocal\Core\Validation\DoesNotExist;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;


/**
 * Class CreateGuardSubscriber
 *
 *  * Handles validating that a Referral Agreement entity can be persisted.

 *
 * @package PapaLocal\ReferralAgreement\Workflow\Agreement
 */
class CreateGuardSubscriber implements EventSubscriberInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * CreateGuardSubscriber constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validates that a ReferralAgreement can be created in persistence.
     *
     * @param GuardEvent $event
     */
    public function guardReview(GuardEvent $event)
    {
        $errors = $this->validator->validate($event->getSubject(), null, array('create'));

        if ($errors->count() > 0) {
            // agreement not valid
            if ($err = $errors->findByCodes(array(DoesNotExist::EXISTS_ERROR))) {
                $event->addTransitionBlocker(new TransitionBlocker($err[0]->getMessage(), CreateTransitionBlockCode::GUID_EXISTS));
            } else {
                $event->addTransitionBlocker(new TransitionBlocker('Unhandled validation errors.', CreateTransitionBlockCode::AGMT_NOT_VALIDATED));
            }
        }

        return;
    }

    /**
     * @return array
     *
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_agreement.guard.create' => 'guardReview'
        ];
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 11:02 AM
 */

namespace PapaLocal\IdentityAccess\Workflow\User;


use PapaLocal\Core\Factory\VOFactory;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Entity\Exception\UsernameExistsException;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\IdentityAccess\Data\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;


/**
 * Class CreateGuardSubscriber
 *
 * @package PapaLocal\IdentityAccess\Workflow\User
 */
class CreateGuardSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var VOFactory
     */
    private $voFactory;

    /**
     * CreateGuardSubscriber constructor.
     *
     * @param UserRepository $userRepository
     * @param VOFactory      $voFactory
     */
    public function __construct(UserRepository $userRepository, VOFactory $voFactory)
    {
        $this->userRepository = $userRepository;
        $this->voFactory      = $voFactory;
    }

    /**
     * @param GuardEvent $event
     */
    public function guardReview(GuardEvent $event)
    {
        $user = $event->getSubject()->getUser();

        // verify user does not exist for username
        try {

            $emailVO = $this->voFactory->createEmailAddress($user->getUsername(), EmailAddressType::PERSONAL());
            $this->userRepository->findUserByUsername($emailVO);

            $event->addTransitionBlocker(new TransitionBlocker(sprintf('A user account exists for the email address %s', $user->getUsername()),
                CreateTransitionBlockCode::USER_EXISTS));

            throw new UsernameExistsException();

        } catch (UserNotFoundException $userNotFoundException) {
            return;
        }
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.user_account.guard.create' => 'guardReview'
        ];
    }

}
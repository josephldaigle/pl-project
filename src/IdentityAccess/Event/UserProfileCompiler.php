<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/5/18
 * Time: 8:52 PM
 */


namespace PapaLocal\IdentityAccess\Event;


use PapaLocal\Billing\Data\BillingProfileHydrator;
use PapaLocal\Core\Data\HydratorRegistry;
use PapaLocal\Core\Data\RepositoryRegistry;
use PapaLocal\IdentityAccess\Data\UserContactDetailHydrator;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\Entity\Billing\BillingProfile;
use PapaLocal\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class UserProfileCompiler
 *
 * Fill out the user's profile detail on each request.
 *
 * @package PapaLocal\IdentityAccess\Event
 */
class UserProfileCompiler implements EventSubscriberInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var RepositoryRegistry
     */
    private $repositoryRegistry;

    /**
     * @var HydratorRegistry
     */
    private $hydratorRegistry;

    /**
     * UserProfileCompiler constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param SerializerInterface   $serializer
     * @param RepositoryRegistry    $repositoryRegistry
     * @param HydratorRegistry      $hydratorRegistry
     */
    public function __construct(TokenStorageInterface $tokenStorage,
                                SerializerInterface $serializer,
                                RepositoryRegistry $repositoryRegistry,
                                HydratorRegistry $hydratorRegistry)
    {
        $token = $tokenStorage->getToken();
        if ($token instanceof AbstractToken) {
            if ($token->getUser() instanceof User) {
                $this->user = $token->getUser();
            }
        }

        $this->serializer = $serializer;
        $this->repositoryRegistry = $repositoryRegistry;
        $this->hydratorRegistry = $hydratorRegistry;
    }

    /**
     * Loads a user's contact detail.
     *
     * @param FilterControllerEvent $event
     */
    public function hydrateUserContactDetail(FilterControllerEvent $event)
    {
        // ignore sub-requests
        if (! $event->isMasterRequest()) {
            return;
        }

        if (is_object($this->user)) {
            // hydrate user's contact profile
            $userContactHydrator = $this->hydratorRegistry->get(UserContactDetailHydrator::class);
            $userContactHydrator->setEntity($this->user);
            $userContactHydrator->hydrate();
        }
    }

    /**
     * Load a user's company list.
     *
     * @param FilterControllerEvent $event
     */
    public function hydrateUserCompanyList(FilterControllerEvent $event)
    {
        // ignore sub-requests
        if (! $event->isMasterRequest() || !$this->user instanceof User) {
            return;
        }

        $userRepository = $this->repositoryRegistry->get(UserRepository::class);
        $companyList = $userRepository->getUserOwnedCompanies($this->user->getId());
        $this->user->setCompanyList($companyList);
    }

    /**
     * Loads a user's billing profile.
     *
     * @param FilterControllerEvent $event
     */
    public function hydrateUserBillingProfile(FilterControllerEvent $event)
    {
        // ignore sub-requests
        if (! $event->isMasterRequest()) {
            return;
        }

        if (is_object($this->user)) {
            // hydrate user's billing profile
            $billingProfile = $this->serializer->denormalize(array(), BillingProfile::class, 'array');
            $billingProfile->setUserId($this->user->getId());

            $billingProfileHydrator = $this->hydratorRegistry->get(BillingProfileHydrator::class);
            $billingProfileHydrator->setEntity($billingProfile);
            $profile = $billingProfileHydrator->hydrate();
            $this->user->setBillingProfile($profile);
        }
    }


    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                array('hydrateUserContactDetail', 0),
                array('hydrateUserBillingProfile', -1),
                array('hydrateUserCompanyList', -2)
            )
        );
    }


}
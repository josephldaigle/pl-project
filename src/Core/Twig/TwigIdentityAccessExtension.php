<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/4/18
 * Time: 8:00 AM
 */

namespace PapaLocal\Core\Twig;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\IdentityAccess\ValueObject\SecurityRole;
use PapaLocal\IdentityAccess\ValueObject\UserViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


/**
 * Class TwigIdentityAccessExtension
 *
 * @package PapaLocal\Core\Twig
 */
class TwigIdentityAccessExtension extends AbstractExtension
{
    /**
     * @var MessageFactory
     */
    private $iaMessageFactory;

    /**
     * @var MessageBusInterface
     */
    private $applicationBus;

    /**
     * @var GuidGeneratorInterface
     */
    private $guidGenerator;

    /**
     * @var UserViewFactory
     */
    private $userViewFactory;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorageInterface;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TwigIdentityAccessExtension constructor.
     *
     * @param MessageFactory         $iaMessageFactory
     * @param MessageBusInterface    $applicationBus
     * @param GuidGeneratorInterface $guidGenerator
     * @param UserViewFactory        $userViewFactory
     * @param TokenStorageInterface  $tokenStorageInterface
     * @param LoggerInterface        $logger
     */
    public function __construct(
        MessageFactory $iaMessageFactory,
        MessageBusInterface $applicationBus,
        GuidGeneratorInterface $guidGenerator,
        UserViewFactory $userViewFactory,
        TokenStorageInterface $tokenStorageInterface,
        LoggerInterface $logger
    )
    {
        $this->iaMessageFactory      = $iaMessageFactory;
        $this->applicationBus        = $applicationBus;
        $this->guidGenerator         = $guidGenerator;
        $this->userViewFactory       = $userViewFactory;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->logger                = $logger;
    }


    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('find_user_by_guid', [$this, 'findUserByGuid'])
        ];
    }

    /**
     * @param string $guid
     *
     * @return array|mixed
     */
    public function findUserByGuid(string $guid)
    {
        try {
            $findUserQry = $this->iaMessageFactory->newFindUserByGuid($this->guidGenerator->createFromString($guid));
            $user = $this->applicationBus->dispatch($findUserQry);

            // TODO: Restrict view based on security role
//            $roles = $this->tokenStorageInterface->getToken()->getUser()->getRoles();

            $userView = $this->userViewFactory->newUserViewForUser($user->getFirstName(), $user->getLastName());

            return $userView;

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(), $exception->getFile(), $exception->getMessage()), array($exception));

            return ['unknown'];
        }
    }
}
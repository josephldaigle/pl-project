<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/21/18
 * Time: 7:13 PM
 */

namespace PapaLocal\IdentityAccess\Message\Query\User;


use PapaLocal\Core\Factory\VOFactory;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\IdentityAccess\Data\UserRepository;


/**
 * Class FindUserByUsernameHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Query\User
 */
class FindUserByUsernameHandler
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
     * FindUserByUsernameHandler constructor.
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
     * @param FindUserByUsername $query
     *
     * @return mixed
     */
    public function __invoke(FindUserByUsername $query)
    {
        $username = $this->voFactory->createEmailAddress($query->getUsername(), EmailAddressType::USERNAME());
        return $this->userRepository->findUserByUsername($username);
    }

}
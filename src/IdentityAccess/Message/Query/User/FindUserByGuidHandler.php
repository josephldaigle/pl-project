<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/21/18
 * Time: 7:53 PM
 */

namespace PapaLocal\IdentityAccess\Message\Query\User;


use PapaLocal\IdentityAccess\Data\UserRepository;


/**
 * Class FindUserByGuidHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Query\User
 */
class FindUserByGuidHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * FindUserByGuidHandler constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(FindUserByGuid $query)
    {
        return $this->userRepository->findUserByGuid($query->getUserGuid());
    }


}
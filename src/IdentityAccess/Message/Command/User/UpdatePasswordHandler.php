<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/8/18
 * Time: 3:54 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


use PapaLocal\IdentityAccess\Service\UserService;


/**
 * Class UpdatePasswordHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class UpdatePasswordHandler
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UpdatePasswordHandler constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param UpdatePassword $command
     */
    function __invoke(UpdatePassword $command)
    {
        $this->userService->updatePassword($command->getUserGuid(), $command->getPassword());
    }


}
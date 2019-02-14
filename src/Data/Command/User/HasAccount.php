<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/3/17
 * Time: 1:41 PM
 */

namespace PapaLocal\Data\Command\User;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\User;

/**
 * HasAccount.
 *
 * Checks if a user exists in the system.
 *
 * @return bool returns a message when user already exists.
 */
class HasAccount extends QueryCommand
{
    /**
     * @var User
     */
    private $user;

    /**
     * UserExists constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        try {
            //query for user
            $this->tableGateway->setTable('v_user');
            $result = $this->tableGateway->findBy('username', $this->user->getUsername());


            //if user exists for email, return true
            if (count($result) > 0) {
                return true;
            }

            //no user exists, return false
            return false;

        } catch (\Exception $exception) {

            throw $this->filterException($exception);
        }
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }
}
<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/25/17
 * Time: 9:49 PM
 */

namespace PapaLocal\Data\Command\User;


use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Data\AttrType;
use PapaLocal\Entity\Exception\QueryCommandFailedException;
use PapaLocal\Entity\User;


/**
 * CreateUser.
 *
 * Encapsulates the algorithm for creating users.
 */
class CreateUser extends QueryCommand
{
    /**
     * @var User
     */
    private $user;

    /**
     * CreateUser constructor.
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
            // begin transaction
            $this->tableGateway->connection->beginTransaction();

            $ids = array();

            // save person
            $this->tableGateway->setTable('Person');

            $ids['personId'] = $this->tableGateway->create(array(
                'guid' => $this->user->getPerson()->getGuid()->value(),
                'firstName' => $this->user->getPerson()->getFirstName(),
                'lastName' => $this->user->getPerson()->getLastName()
            ));

            // save user
            $this->tableGateway->setTable('User');
            $userId = $this->tableGateway->create(array(
                'guid' => $this->user->getGuid()->value(),
                'personId' => $ids['personId'],
                'password' => $this->user->getPassword(),
                'timeZone' => 'America/New York'
            ));

            // save username
            $this->tableGateway->setTable('EmailAddress');
            $existingEmail = $this->tableGateway->findBy('emailAddress', $this->user->getUsername());
            if (count($existingEmail) > 0) {
                $ids['emailId'] = $existingEmail[0]['id'];
            } else {
                $ids['emailId'] = $this->tableGateway->create(array(
                    'emailAddress' => $this->user->getUsername()
                ));
            }


            // save person->email relationship
            $this->tableGateway->setTable('L_EmailAddressType');
            $emailType = $this->tableGateway->findBy('description', AttrType::EMAIL_USERNAME);
            if (count($emailType) < 1) {
                throw new QueryCommandFailedException(sprintf('Failed to find EmailAddressType: [%s]', AttrType::EMAIL_USERNAME));
            }
            $ids['typeId'] = $emailType[0]['id'];

            $this->tableGateway->setTable('R_PersonEmailAddress');
            $this->tableGateway->create($ids);

            //save user roles
            $this->tableGateway->setTable('L_UserRole');
            $role = $this->tableGateway->findBy('name', AttrType::SECURITY_ROLE_USER);

            //user only gets ROLE_USER by default
            $this->tableGateway->setTable('R_UserApplicationRole');
            $ids['roleId'] = $this->tableGateway->create(array(
                'userId' => $userId,
                'roleId' => $role[0]['id']
            ));

            // commit transaction
            $this->tableGateway->connection->commit();

            $this->user->setId($userId);
            return $this->user;

        } catch (\Exception $e) {
            // rollback transaction
            $this->tableGateway->connection->rollBack();

            throw ($this->filterException($e));
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
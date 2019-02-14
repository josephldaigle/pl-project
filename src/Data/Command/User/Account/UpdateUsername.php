<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/19/18
 * Time: 8:14 AM
 */

namespace PapaLocal\Data\Command\User\Account;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Exception\QueryCommandFailedException;
use PapaLocal\Entity\User;

/**
 * UpdateUsername.
 *
 * Updates the username for a user account.
 *
 * @package PapaLocal\Data\Command\User\Account
 */
class UpdateUsername extends QueryCommand
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $newUsername;

    /**
     * UpdateUsername constructor.
     *
     * @param User   $user
     * @param string $newUsername
     */
    public function __construct(User $user, string $newUsername)
    {
        $this->user = $user;
        $this->newUsername = $newUsername;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {

        $this->tableGateway->connection->beginTransaction();

        try {
            // find the R_PersonEmailAddress row for the user
            $this->tableGateway->setTable('L_EmailAddressType');
            $emailTypeRows = $this->tableGateway->findBy('description', 'Username');

            if (count($emailTypeRows) !== 1) {
                throw new QueryCommandFailedException('Unable to load the email type: Username');
            }

            $this->tableGateway->setTable('EmailAddress');
            $emailRows = $this->tableGateway->findBy('emailAddress', $this->newUsername);

            if (count($emailRows) !== 1) {
                // new username does not yet exist
                $emailId = $this->tableGateway->create(array('emailAddress' => $this->newUsername));
            } else {
                $emailId = $emailRows[0]['id'];
            }

            // update username record
            $this->tableGateway->setTable('R_PersonEmailAddress');
            $usernameRows = $this->tableGateway->findByColumns(array(
                'personId' => $this->user->getPerson()->getGuid(),
                'typeId' => $emailTypeRows[0]['id']
            ));

            $usernameRow = $usernameRows[0];
            $usernameRow['emailId'] = $emailId;
            $this->tableGateway->update($usernameRow);

            $this->tableGateway->connection->commit();

        } catch (\Exception $exception) {
            $this->tableGateway->connection->rollBack();
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
<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 1:57 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdatePhoneNumberHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdatePhoneNumberHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdatePhoneNumberHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdatePhoneNumber $command)
    {
        $this->tableGateway->setTable('v_user');
        $userRecs = $this->tableGateway->findBy('userGuid', $command->getUserGuid());

        $this->tableGateway->setTable('L_PhoneNumberType');
        $phoneTypeRecs = $this->tableGateway->findBy('description', $command->getPhoneType());

        $this->tableGateway->setTable('PhoneNumber');
        $phoneRecords = $this->tableGateway->findBy('phoneNumber', $command->getPhoneNumber());

        $phoneId = null;
        if ($phoneRecords->count() > 0) {
            $phoneId = $phoneRecords->current()['id'];
        } else {
            $this->tableGateway->create(['phoneNumber' => $command->getPhoneNumber()]);

            $newPhoneRecords = $this->tableGateway->findBy('phoneNumber', $command->getPhoneNumber());
            if (! $newPhoneRecords->count() > 0) {
                throw new CommandException(sprintf('Unable to save phone number %s for user %s.', $command->getPhoneNumber(), $command->getUserGuid()));
            }

            $phoneId = $newPhoneRecords->current()['id'];
        }

        $this->tableGateway->setTable('R_PersonPhoneNumber');
        $userPhoneRecs = $this->tableGateway->findByColumns([
            'personId' => $userRecs->current()['personId'],
            'typeId' => $phoneTypeRecs->current()['id']
        ]);

        if ($userPhoneRecs->count() > 0) {
            $this->tableGateway->delete($userPhoneRecs->current()['id']);
        }

        $this->tableGateway->create([
            'personId' => $userRecs->current()['personId'],
            'phoneId' => $phoneId,
            'typeId' => $phoneTypeRecs->current()['id']
        ]);

        return;
    }
}
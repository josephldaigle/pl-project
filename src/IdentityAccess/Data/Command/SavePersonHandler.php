<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\IdentityAccess\Data\Command;


use PapaLocal\Core\Data\TableGateway;


/**
 * Class SavePersonHandler.
 *
 * @package PapaLocal\IdentityAccess\Data\Command
 */
class SavePersonHandler
{
    /**
     * @var TableGateway
     */
    private $tableGateway;

    /**
     * SavePersonHandler constructor.
     *
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param SavePerson $command
     */
    public function __invoke(SavePerson $command)
    {
        $this->tableGateway->setTable('Person');
        $this->tableGateway->create(array(
            'guid' => $command->getGuid(),
            'firstName' => $command->getFirstName(),
            'lastName' => $command->getLastName(),
            'about' => $command->getAbout()));
        return;
    }
}
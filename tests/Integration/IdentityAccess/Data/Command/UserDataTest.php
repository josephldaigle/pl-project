<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/25/18
 * Time: 9:37 AM
 */

namespace Test\Integration\IdentityAccess\Data\Command;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use PapaLocal\IdentityAccess\Entity\Person;
use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UserDataTest
 *
 * @package PapaLocal\IdentityAccess\Data\Command
 */
class UserDataTest  extends WebDatabaseTestCase
{
    /**
     * @var MessageBusInterface
     */
    private $mysqlDataBus;

    /**
     * @var MessageFactory
     */
    private $mysqlMsgFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->configureDataSet([]);

        parent::setUp();

        // fetch services
        $this->mysqlDataBus = $this->diContainer->get('messenger.bus.mysql');
        $this->mysqlMsgFactory = $this->diContainer->get('PapaLocal\IdentityAccess\Data\MessageFactory');
    }

    public function testCanCreateUser()
    {
        // set up fixtures
        $begPersonRowCount = $this->getConnection()->getRowCount('Person');
        $begUserRowCount = $this->getConnection()->getRowCount('User');

        $person = new Person(new Guid('78021402-3868-41bb-be9d-8b5880aef21b'), 'Guy', 'Tester');

        $user = new User();
        $user->setGuid(new Guid('8f109772-87a3-4725-9ca7-1f929144bb11'));
        $user->setUsername('gtester@papalocal.com');
        $user->setPassword('Som3P@$s1');
        $user->setNotificationSavePoint(0);
        $user->setIsActive(true);
        $user->setPerson($person);

        // exercise SUT
        $command = $this->mysqlMsgFactory->newCreateUser($user);
        $this->mysqlDataBus->dispatch($command);

        // make assertions
        $this->assertTableRowCount('Person', $begPersonRowCount + 1);
        $this->assertTableRowCount('User', $begUserRowCount + 1);
    }
}
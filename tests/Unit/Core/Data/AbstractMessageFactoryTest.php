<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 2:14 AM
 */

namespace Test\Unit\Core\Data;


use PapaLocal\Core\Data\AbstractMessageFactory;
use PapaLocal\Core\Data\Command\CommitTransaction;
use PapaLocal\Core\Data\Command\RollbackTransaction;
use PapaLocal\Core\Data\Command\StartTransaction;
use PapaLocal\Core\Data\Query\FindBy;
use PapaLocal\Core\Data\Query\FindByCols;
use PapaLocal\Core\Data\Query\FindByGuid;
use PapaLocal\Core\Data\Query\FindByRowId;
use PapaLocal\Core\ValueObject\GuidInterface;
use PHPUnit\Framework\TestCase;


/**
 * Class AbstractCommandFactoryTest
 *
 * @package Test\Unit\Core\Data
 */
class AbstractMessageFactoryTest extends TestCase
{
    public function testCanCreateStartTransactionCommand()
    {
        // set up fixtures
        $commandFactory = $this->getMockBuilder(AbstractMessageFactory::class)
                               ->setMethodsExcept(['newStartTransaction'])
                               ->getMockForAbstractClass();

        // exercise SUT
        $command = $commandFactory->newStartTransaction();

        // make assertions
        $this->assertInstanceOf(StartTransaction::class, $command);
    }

    public function testCanCreateRollbackTransaction()
    {
        // set up fixtures
        $commandFactory = $this->getMockBuilder(AbstractMessageFactory::class)
                               ->setMethodsExcept(['newRollbackTransaction'])
                               ->getMockForAbstractClass();

        // exercise SUT
        $command = $commandFactory->newRollbackTransaction();

        // make assertions
        $this->assertInstanceOf(RollbackTransaction::class, $command);
    }

    public function testCanCreateCommitTransaction()
    {
        // set up fixtures
        $commandFactory = $this->getMockBuilder(AbstractMessageFactory::class)
                               ->setMethodsExcept(['newCommitTransaction'])
                               ->getMockForAbstractClass();

        // exercise SUT
        $command = $commandFactory->newCommitTransaction();

        // make assertions
        $this->assertInstanceOf(CommitTransaction::class, $command);
    }

    public function testCanCreateFindByCols()
    {
        // set up fixtures
        $queryFactory = $this->getMockBuilder(AbstractMessageFactory::class)
                             ->setMethodsExcept(['newFindByCols'])
                             ->getMockForAbstractClass();

        $filter = array(
            'col1' => 'value1',
            'col2' => 'value2'
        );

        // exercise SUT
        $query = $queryFactory->newFindByCols('SomeTable', $filter);

        // make assertions
        $this->assertInstanceOf(FindByCols::class, $query);
    }

    public function testCanCreateFindByGuid()
    {
        // set up fixtures
        $queryFactory = $this->getMockBuilder(AbstractMessageFactory::class)
                             ->setMethodsExcept(['newFindByGuid'])
                             ->getMockForAbstractClass();

        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $query = $queryFactory->newFindByGuid('SomeTable', $guidMock);

        // make assertions
        $this->assertInstanceOf(FindByGuid::class, $query);
    }

    public function testCanCreateFindByRowId()
    {
        // set up fixtures
        $queryFactory = $this->getMockBuilder(AbstractMessageFactory::class)
                             ->setMethodsExcept(['newFindByRowId'])
                             ->getMockForAbstractClass();

        // exercise SUT
        $query = $queryFactory->newFindByRowId('SomeTable', 3);

        // make assertions
        $this->assertInstanceOf(FindByRowId::class, $query);
    }

    public function testCanCreateFindBy()
    {
        // set up fixtures
        $queryFactory = $this->getMockBuilder(AbstractMessageFactory::class)
                             ->setMethodsExcept(['newFindBy'])
                             ->getMockForAbstractClass();

        // exercise SUT
        $query = $queryFactory->newFindBy('SomeTable', 'colName', 'value');

        // make assertions
        $this->assertInstanceOf(FindBy::class, $query);
    }
}
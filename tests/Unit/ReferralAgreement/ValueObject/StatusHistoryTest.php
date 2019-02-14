<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 7/18/18
 * Time: 9:11 PM
 */


namespace Test\Unit\ReferralAgreement\ValueObject;



use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\StatusHistory;
use PapaLocal\Test\TestDummy;
use PHPUnit\Framework\TestCase;


/**
 * Class StatusHistoryTest.
 *
 * @package Test\Unit\ReferralAgreement\ValueObject
 */
class StatusHistoryTest extends TestCase
{
	public function testCanConstruct()
	{
		// set up fixtures
		$collectionMock = $this->createMock(Collection::class);

		// exercise SUT
		$statusHistory = new StatusHistory($collectionMock);

		// make assertions
		$this->assertInstanceOf(StatusHistory::class, $statusHistory);

	}

    public function testAddIsSuccess()
    {
        // set up fixtures
        $statusMock = $this->createMock(AgreementStatus::class);

        $collectionMock = $this->createMock(Collection::class);

        $collectionMock->expects($this->once())
                       ->method('prepend')
                       ->with($this->equalTo($statusMock));

        // exercise SUT
        $statusHistory = new StatusHistory($collectionMock);
        $statusHistory->add($statusMock);
	}

	public function testGetCurrentStatusReturnsCorrectValueWhenCollectionIsEmpty()
	{
		// set up fixtures
		$collectionMock = $this->createMock(Collection::class);
		$collectionMock->expects($this->once())
			->method('count')
			->willReturn(0);

		// exercise SUT
		$statusHistory = new StatusHistory($collectionMock);
		$result = $statusHistory->getCurrentStatus();

		// make assertions
		$this->assertSame('Unknown', $result);
	}

	/**
	 * @dataProvider getCurrentStatusFailureProvider
	 * @expectedException \LogicException
	 * @expectedExceptionMessageRegExp /^(The underlying collection must contain objects or arrays which possess a timeUpdated attribute or key)/
	 */
	public function testGetCurrentStatusThrowsExceptionWhenCollectionContainsPrimitives($items)
	{
		// set up fixtures
		$collectionMock = $this->getMockBuilder(Collection::class)
			->setMethodsExcept(['add', 'first', 'count'])
			->getMock();

		foreach($items as $item) {
			$collectionMock->add(true);
		}

		// exercise SUT
		$statusHistory = new StatusHistory($collectionMock);
		$statusHistory->getCurrentStatus();
	}

	public function getCurrentStatusFailureProvider()
	{
		return [
			'first' => [[true, false, true]], // first item not an object, not an array
			'second' => [[$this->createMock(TestDummy::class), $this->createMock(TestDummy::class), $this->createMock(TestDummy::class), $this->createMock(TestDummy::class)]], // items are object, but don't have timeUpdated attr
			'third' => [[[1, 2, 3], [1,2,3], [1,2,3]]], // items are array, but don't have timeUpdated attr
		];
	}

	public function testGetCurrentStatusSuccessWhenCollectionContainsObjects()
    {
        // set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add', 'addAll', 'first', 'count', 'all'])
            ->getMock();

        $mockBuilder = $this->getMockBuilder(AgreementStatus::class)
            ->disableOriginalConstructor();

        $item1 = $mockBuilder->getMock();

		$item1->expects($this->any())
            ->method('getTimeUpdated')
            ->willReturn('2018-03-13 11:07:55'); // last

		$item2 = $mockBuilder->getMock();
		$item2->expects($this->any())
            ->method('getTimeUpdated')
            ->willReturn('2018-03-13 11:07:51'); // middle

		$item3 = $mockBuilder->getMock();
		$item3->expects($this->any())
            ->method('getTimeUpdated')
            ->willReturn('2018-03-12 11:07:51'); // first

		$collectionMock->add($item1);
		$collectionMock->add($item2);
		$collectionMock->add($item3);

		// exercise SUT
		$statusHistory = new StatusHistory($collectionMock);
		$result = $statusHistory->getCurrentStatus();

		$this->assertInstanceOf(AgreementStatus::class, $result, 'unexpected type');
		$this->assertSame($result, $item1, 'unexpected value');
	}

    public function testGetCurrentStatusSuccessWhenCollectionContainsArrays()
	{
        // set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add', 'first', 'count', 'all'])
            ->getMock();

        $item1 = ['id' => 1, 'timeUpdated' => '2018-03-13 11:07:55']; // last
        $item2 = ['id' => 2, 'timeUpdated' => '2018-03-13 11:07:51']; // middle
        $item3 = ['id' => 3, 'timeUpdated' => '2018-03-12 11:07:51']; // first

        $collectionMock->add($item1);
        $collectionMock->add($item2);
        $collectionMock->add($item3);

        // exercise SUT
        $statusHistory = new StatusHistory($collectionMock);
        $result = $statusHistory->getCurrentStatus();

        $this->assertTrue(is_array($result), 'unexpected type');
        $this->assertSame($result, $item1, 'unexpected value');
	}

}
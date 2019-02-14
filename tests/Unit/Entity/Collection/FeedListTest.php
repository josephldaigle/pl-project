<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/6/18
 * Time: 1:14 PM
 */

namespace Ewebify\Test\Unit\Entity\Collection;


use PapaLocal\Entity\Collection\FeedList;
use PapaLocal\Entity\FeedItemInterface;
use PapaLocal\Test\TestDummy;
use PHPUnit\Framework\TestCase;


/**
 * Class FeedListTest
 *
 * @package Ewebify\Test\Unit\Entity\Collection
 *
 * Unit tests for PapaLocal\Entity\Collection\FeedList
 */
class FeedListTest extends TestCase
{
	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessageRegExp  /^(.)+(expects param 1 to be instance of PapaLocal\\Entity\\FeedItemInterface)(.)+(given)/
	 */
	public function testAddThrowsExceptionWhenParamOneNotAValidType()
	{
		// set up fixtures
		$feedList = new FeedList();
		$item = new TestDummy();

		// exercise SUT
		$feedList->add($item);
	}

	public function testCanAddFeedItemToList()
	{
		// set up fixtures
		$title = 'Test feed item title';

		$item = $this->createMock(FeedItemInterface::class);
		$item->method('getTitle')
			->willReturn($title);

		$feedList = new FeedList();

		// exercise SUT
		$feedList->add($item);

		// make assertions
		$this->assertEquals(1, $feedList->count(), 'unexpected count');
		$this->assertSame($title, $feedList->get(0)->getTitle(), 'unexpected value');
	}

	public function testFindTypeByReturnsCorrectValueForNotification(  )
	{
		// TODO: Implement
		$this->markTestIncomplete();
	}

	public function testFindTypeByReturnsCorrectValueForReferral(  )
	{
		// TODO: Implement
		$this->markTestIncomplete();
	}

	public function testFindTypeByReturnsCorrectValueForAgreement()
	{
		// TODO: Implement
		$this->markTestIncomplete();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessageRegExp /(.)+(expects param 1 to be a valid feed type)(.)+(given)/
	 */
	public function testFindTypeByThrowsExceptionWhenInvalidTypeProvided()
	{
		// TODO: Implement
		// valid types are in FeedItemInterface
		$this->markTestIncomplete();
	}

	public function testSortByTimeCreatedReturnsAscendingListOnSuccess()
	{
		// set up fixtures
		$dates = [
			'2018-03-12 15:47:58',
			'2018-03-12 15:47:59',
			'2018-03-12 15:48:00',
			'2018-03-12 15:48:01',
			'2018-04-12 15:48:01'
		];

		$feedList = new FeedList();
		for($i = 0; $i < count($dates); $i++) {
			$item = $this->createMock(FeedItemInterface::class);
			$item->method('getTimeCreated')
			     ->willReturn($dates[$i]);
			$feedList->add($item);
		}

		// exercist SUT
		$feedList->sortByTimeCreated();

		// make assertions
		$this->assertGreaterThan($feedList->get(0)->getTimeCreated(), $feedList->get(1)->getTimeCreated(), 'unexpected order');
	}

	public function testSortByTimeCreatedReturnsDescendingListOnSuccess()
	{
		// set up fixtures
		$dates = [
			'2018-03-12 15:47:58',
			'2018-03-12 15:47:59',
			'2018-03-12 15:48:00',
			'2018-03-12 15:48:01',
			'2018-04-12 15:48:01'
		];

		$feedList = new FeedList();
		for($i = 0; $i < count($dates); $i++) {
			$item = $this->createMock(FeedItemInterface::class);
	        $item->method('getTimeCreated')
				->willReturn($dates[$i]);
			$feedList->add($item);
		}

		// exercist SUT
		$feedList->sortByTimeCreated(FeedList::SORT_DESC);

		// make assertions
		$this->assertGreaterThan($feedList->get(1)->getTimeCreated(), $feedList->get(0)->getTimeCreated(), 'unexpected order');
	}
}
<?php

/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 5/14/18
 * Time: 9:43 AM
 */


namespace Test\Unit\ValueObject;


use PapaLocal\Entity\Notification;
use PHPUnit\Framework\TestCase;
use PapaLocal\Entity\Notification\NotificationList;


/**
 * Class NotificationListTest
 * @package Test\Unit\ValueObject
 */
class NotificationListTest extends TestCase
{
    public function testSortByTimeSentSortsListItemsCorrectlyOnSuccess()
    {
        // set up fixtures
        $mockNotificationOne = $this->getMockBuilder(Notification::class)
            ->getMock();
        $mockNotificationOne->expects($this->any())
            ->method('getTimeSent')
            ->willReturn('2018-01-10 12:13:31');

        $mockNotificationTwo = $this->getMockBuilder(Notification::class)
            ->getMock();
        $mockNotificationTwo->expects($this->any())
            ->method('getTimeSent')
            ->willReturn('2018-01-11 12:13:31');

        $mockNotificationThree = $this->getMockBuilder(Notification::class)
            ->getMock();
        $mockNotificationThree->expects($this->any())
            ->method('getTimeSent')
            ->willReturn('2018-01-10 11:22:44');

        $list = new NotificationList();
        $list->add($mockNotificationOne);
        $list->add($mockNotificationTwo);
        $list->add($mockNotificationThree);

        // exercise SUT
        $result = $list->sortByTimeSent();

        // make assertions
        foreach($result as $key => $val) {
            if (array_key_exists($key + 1, $result)) {
                $this->assertLessThan($result[$key + 1]->getTimeSent(), $result[$key]->getTimeSent(),
                    sprintf('element %s out of order', $key));
            }
        }
    }

    /**
     * @expectedException \BadFunctionCallException
     * @expectedExceptionMessageRegExp /(Unable to call)(.)+(sortByTimeSent on empty list)/
     */
    public function testSortByTimeSentThrowsExceptionWhenListIsEmpty()
    {
        $list = new NotificationList();

        // exercise SUT
        $list->sortByTimeSent();
    }

    /**
     * SUT: sliceByIndexRange
     */

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp
     */
    public function testSliceByIndexRangeThrowsExceptionWhenParamOneLessThanParamTwo()
    {
        // Exercise SUT
        $notificationList = new NotificationList();
        $notificationList->sliceByIndexRange(4, 2);

    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /(Unable to call)(.)+(sliceByIndexRange when index is a non integer value)/
     */
    public function testSliceByIndexRangeThrowsExceptionWhenListContainsNonNumericIndex()
    {
        // Test throw exception when list have none numeric index

        // FIXTURE
        $notificationList = new NotificationList();
        $notificationList->add('NotificationOne', 1);
        $notificationList->add('NotificationTwo', 'Bad Key');
        $notificationList->add('NotificationThree', 5);

        // SUT
        $notificationList->sliceByIndexRange(0, 10);

    }

    public function testSliceByIndexRangeReturnsExpectedResultOnSuccess()
    {
        // FIXTURE
        $notificationList = new NotificationList();
        $notificationList->add('NotificationOne');
        $notificationList->add('NotificationTwo');
        $notificationList->add('NotificationThree');
        $notificationList->add('NotificationFour');
        $notificationList->add('NotificationFive');

        // SUT
        $result = $notificationList->sliceByIndexRange(2, 4);

        // ASSERTION
        $this->assertNotContains('NotificationOne', $result->all());
        $this->assertNotContains('NotificationTwo', $result->all());
        $this->assertContains('NotificationThree', $result->all());
        $this->assertContains('NotificationFive', $result->all());
        $this->assertContains('NotificationFour', $result->all());

    }

    /**
     * SUT: countUnreadMessages
     */

    public function testCountUnreadMessagesReturnsCorrectCountOnSuccess()
    {
        // fixture
        $mockNotificationOne = $this->getMockBuilder(Notification::class)
            ->getMock();
        $mockNotificationOne->expects($this->once())
            ->method('isRead')
            ->willReturn(true);

        $mockNotificationTwo = $this->getMockBuilder(Notification::class)
            ->getMock();
        $mockNotificationTwo->expects($this->once())
            ->method('isRead')
            ->willReturn(false);

        $mockNotificationThree = $this->getMockBuilder(Notification::class)
            ->getMock();
        $mockNotificationThree->expects($this->once())
            ->method('isRead')
            ->willReturn(true);

        $mockNotificationFour = $this->getMockBuilder(Notification::class)
            ->getMock();
        $mockNotificationFour->expects($this->once())
            ->method('isRead')
            ->willReturn(false);

        $mockNotificationFive = $this->getMockBuilder(Notification::class)
            ->getMock();
        $mockNotificationFive->expects($this->once())
            ->method('isRead')
            ->willReturn(false);

        $notificationList = new NotificationList();
        $notificationList->add($mockNotificationOne);
        $notificationList->add($mockNotificationTwo);
        $notificationList->add($mockNotificationThree);
        $notificationList->add($mockNotificationFour);
        $notificationList->add($mockNotificationFive);

        //sut
        $result = $notificationList->countUnreadMessages();

        //assertion
        $this->assertEquals(3, $result);
    }

    public function testCountUnreadMessagesReturnsZeroWhenListIsEmpty()
    {
        //sut
        $notificationList = new NotificationList();
        $result = $notificationList->countUnreadMessages();

        //assertion
        $this->assertEquals(0, $result);
    }

}
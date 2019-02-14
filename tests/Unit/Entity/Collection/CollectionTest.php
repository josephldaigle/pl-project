<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/16/17
 */


namespace Ewebify\Test\Unit\Entity\Collection;


use PapaLocal\Entity\FeedItemInterface;
use PHPUnit\Framework\TestCase;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Test\TestDummy;


/**
 * Class CollectionTest.
 *
 * @package Ewebify\Test\Unit\Entity\Collection
 */
class CollectionTest extends TestCase
{

    /*
    |-----------------------------------------------------
    | SUT: add()
    |-----------------------------------------------------
    |
    */

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Param 1 passed to)(.)+(cannot be null or empty.)$/
     */
    public function testAddThrowsExceptionWhenParamOneIsNull()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add'])
            ->getMock();

        //exercise SUT
        $collectionMock->add(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Param 1 passed to)(.)+(cannot be null or empty.)$/
     */
    public function testAddThrowsExceptionWhenParamOneIsEmptyArray()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add'])
            ->getMock();

        //exercise SUT
        $collectionMock->add(array());
    }


    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Param 1 passed to)(.)+(cannot be null or empty.)$/
     */
    public function testAddThrowsExceptionWhenParamOneIsEmptyString()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add'])
            ->getMock();

        //exercise SUT
        $collectionMock->add('');
    }


    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Key)(.)+(is already assigned.)$/
     */
    public function testAddThrowsExceptionWhenKeyExists()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add'])
            ->getMock();

        $key = 'dummy';
        $d1 = new TestDummy();
        $d2 = new TestDummy();

        //exercise SUT
        $collectionMock->add($d1, $key);
        $collectionMock->add($d2, $key);
    }

	/*
	|-----------------------------------------------------
	| SUT: first()
	|-----------------------------------------------------
	|
	*/
	public function testFirstReturnsCorrectElementOnSuccess()
	{
		//set up fixtures
		$collectionMock = $this->getMockBuilder(Collection::class)
		                       ->setMethodsExcept(['add', 'first'])
		                       ->getMock();

		$key = 'dummy';
		$value = new TestDummy();

		//exercise SUT
		$collectionMock->add($value, $key);
		$result = $collectionMock->first();

		//assert results
		$this->assertSame($value, $result);
	}

	public function testFirstReturnsCorrectElementWhenMultipleEntriesExist()
	{
		//set up fixtures
		$collectionMock = $this->getMockBuilder(Collection::class)
		                       ->setMethodsExcept(['add', 'first'])
		                       ->getMock();

		$key = 'dummyOne';
		$value = (new TestDummy())
			->setMember('dummyOne');
		$key2 = 'dummyTwo';
		$value2 = (new TestDummy())
			->setMember('dummyTwo');

		//exercise SUT
		$collectionMock->add($value, $key);
		$collectionMock->add($value2, $key2);
		$result = $collectionMock->first();

		//assert results
		$this->assertSame($value, $result);
	}

	public function testFirstReturnsNullWhenCollectionIsEmpty()
	{
		//set up fixtures
		$collectionMock = $this->getMockBuilder(Collection::class)
		                       ->setMethodsExcept(['first'])
		                       ->getMock();

		//exercise SUT
		$result = $collectionMock->first();

		//assert results
		$this->assertNull($result);
	}


	/*
		|-----------------------------------------------------
		| SUT: last()
		|-----------------------------------------------------
		|
		*/
	public function testLastReturnsCorrectElementOnSuccess()
	{
		//set up fixtures
		$collectionMock = $this->getMockBuilder(Collection::class)
		                       ->setMethodsExcept(['add', 'last'])
		                       ->getMock();

		$key = 'dummy';
		$value = new TestDummy();

		//exercise SUT
		$collectionMock->add($value, $key);
		$result = $collectionMock->last();

		//assert results
		$this->assertSame($value, $result);
	}

	public function testLastReturnsCorrectElementWhenMultipleEntriesExist()
	{
		//set up fixtures
		$collectionMock = $this->getMockBuilder(Collection::class)
		                       ->setMethodsExcept(['add', 'last'])
		                       ->getMock();

		$key = 'dummyOne';
		$value = (new TestDummy())
			->setMember('dummyOne');
		$key2 = 'dummyTwo';
		$value2 = (new TestDummy())
			->setMember('dummyTwo');

		//exercise SUT
		$collectionMock->add($value, $key);
		$collectionMock->add($value2, $key2);
		$result = $collectionMock->last();

		//assert results
		$this->assertSame($value2, $result);
	}

	public function testLastReturnsNullWhenCollectionIsEmpty()
	{
		//set up fixtures
		$collectionMock = $this->getMockBuilder(Collection::class)
		                       ->setMethodsExcept(['last'])
		                       ->getMock();

		//exercise SUT
		$result = $collectionMock->last();

		//assert results
		$this->assertNull($result);
	}


    /*
    |-----------------------------------------------------
    | SUT: get()
    |-----------------------------------------------------
    |
    */
    public function testGetReturnsCorrectElementOnSuccess()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add', 'get'])
            ->getMock();

        $key = 'dummy';
        $value = new TestDummy();

        //exercise SUT
        $collectionMock->add($value, $key);
        $result = $collectionMock->get($key);

        //assert results
        $this->assertSame($value, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Key)(.)+(does not exist.)$/
     */
    public function testGetThrowsExceptionWhenKeyDoesNotExist()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['get'])
            ->getMock();

        //exercise SUT
        $collectionMock->get('badKey');
    }

    /*
    |-----------------------------------------------------
    | SUT: remove()
    |-----------------------------------------------------
    |
    */

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Key supplied does not exist)/
     */
    public function testRemoveThrowsExceptionWhenKeyIsNull()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['remove'])
            ->getMock();

        //exercise SUT
        $collectionMock->remove(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Key supplied does not exist)/
     */
    public function testRemoveThrowsExceptionWhenKeyIsEmpty()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
        ->setMethodsExcept(['remove'])
        ->getMock();

            //exercise SUT
        $collectionMock->remove('');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Key)(.)+(does not exist.)$/
     */
    public function testRemoveThrowsExceptionWhenKeyDoesNotExist()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['remove'])
            ->getMock();

        //exercise SUT
        $collectionMock->remove('badKey');
    }

    public function testHasReturnsFalseWhenKeyDoesNotExist()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['has'])
            ->getMock();

        //exercise SUT
        //make assertions
        $this->assertFalse($collectionMock->has('badKey'));
    }

    public function testHasReturnsTrueWhenKeyExists()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add', 'has'])
            ->getMock();

        $key = 'dummy';
        $value = new TestDummy();

        //exercise SUT
        $collectionMock->add($value, $key);

        //make assertions
        $this->assertFalse($collectionMock->has('badKey'));
    }

    public function testFindByReturnsNullWhenCollectionIsEmpty()
    {
        $this->collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['findBy'])
            ->getMock();

        $this->assertNull($this->collectionMock->findBy('property', 'value'));
    }

    public function testFindByReturnsNullWhenItemNotFound()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add', 'findBy', 'count'])
            ->getMock();

        $key = 'dummy';
        $value = (new TestDummy())
            ->setMember('someValueToCheck');

        $collectionMock->add($value, $key);

        //exercise SUT
        $item = $collectionMock->findBy('member', 'nonExistingValue');

        //make assertions
        $this->assertNull($item);
    }

    public function testFindByReturnsCorrectItemWhenFoundAndCollectionContainsArrays()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add', 'addAll', 'findBy', 'count'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $key1 = 'key1';
        $item1 = array('member' => 'someValueToCheck');

        $key2 = 'key2';
        $item2 = array('member' => 'member2');

        $collectionMock->add($item1, $key1);
        $collectionMock->add($item2, $key2);

        //exercise SUT
        $item = $collectionMock->findBy('member', 'someValueToCheck');

        //make assertions
        $this->assertSame($item1, $item);
    }

    public function testFindByReturnsCorrectItemWhenFoundAndCollectionContainsObjects()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add', 'addAll', 'findBy', 'count'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $key1 = 'dummy';
        $item1 = (new TestDummy())
            ->setMember('someValueToCheck');

        $key2 = 'dummy2';
        $item2 = (new TestDummy())
            ->setMember('anotherValue');

        //exercise SUT
        $collectionMock->add($item1, $key1);
        $collectionMock->add($item2, $key2);

        //exercise SUT
        $item = $collectionMock->findBy('member', 'someValueToCheck');

        //make assertions
        $this->assertSame($item1, $item);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessageRegExp /^(Cannot call findBy on a collection that contains)/
     */
    public function testFindByThrowsExceptionWhenCalledOnEmptyCollection()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add', 'addAll', 'findBy', 'count'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $item1 = 'somePrimitive';
        $item2 = 'somePrimitive';

        //exercise SUT
        $collectionMock->add($item1);
        $collectionMock->add($item2);

        //exercise SUT
        $collectionMock->findBy('member', 'somePrimitive');

    }

    public function testCountReturnsCorrectCountOnSuccess()
    {
        //set up fixtures
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethodsExcept(['add', 'count'])
            ->getMock();

        $key = 'dummy';
        $value = new TestDummy();

        $key2 = 'dummy2';
        $value2 = new TestDummy();

        //exercise SUT
        $collectionMock->add($value, $key);
        $collectionMock->add($value2, $key2);

        //make assertions
        $this->assertSame(2, $collectionMock->count());
    }

    /*
    |-----------------------------------------------------
    | SUT: remove()
    |-----------------------------------------------------
    |
    */

    public function testCanIterateUsingForLoop()
    {
        //set up fixtures
        $collection = new Collection();

        $key = 'dummy';
        $value = new TestDummy();

        $key2 = 'dummy2';
        $value2 = new TestDummy();

        $testArr = array(
            $key => $value,
            $key2 => $value2
        );

        //exercise SUT
        $collection->add($value, $key);
        $collection->add($value2, $key2);

        //make assertions
        foreach($collection as $key => $val)
        {
            $this->assertArrayHasKey($key, $testArr, sprintf('key %s not found', $key));
            $this->assertSame($val, $testArr[$key], sprintf('unexpected value for key %s', $key));
        }
    }

    public function testSortBy()
    {
        //set up fixtures
        $collection = new Collection();

        $value = new TestDummy('Should be last');
        $value2 = new TestDummy('Should be first');

        $sortFunc = function (TestDummy $a, TestDummy $b)
        {
            return strcmp($a->getMember(), $b->getMember());
        };

        $collection->add($value);
        $collection->add($value2);

        //exercise SUT
        $collection->sortBy($sortFunc);

        // make assertions
        $this->assertEquals($value2, $collection->first(), 'unexpected element order');
        $this->assertCount(2, $collection, 'unexpected count');
    }

    public function testSlice()
    {
        //set up fixtures
        $collection = new Collection();

        $value = new TestDummy('Member One');
        $value2 = new TestDummy('Member Two');
        $value3 = new TestDummy('Member Three');
        $value4 = new TestDummy('Member Four');

        $collection->add($value);
        $collection->add($value2);
        $collection->add($value3);
        $collection->add($value4);

        //exercise SUT
        $collection->slice(2, 3);

        // make assertions
        $this->assertCount(2, $collection);
        $this->assertEquals($value3, $collection->first());
    }

    public function testReduceTo()
    {
        //set up fixtures
        $collection = new Collection();

        $value = new TestDummy('Member One');
        $value2 = new TestDummy('Member Two');
        $value3 = new TestDummy('Member Three');
        $value4 = new TestDummy('Member Four');

        $collection->add($value);
        $collection->add($value2);
        $collection->add($value3);
        $collection->add($value4);

        //exercise SUT
        $collection->reduceTo(1);

        // make assertions
        $this->assertCount(1, $collection);
        $this->assertEquals($value, $collection->first());
    }
}
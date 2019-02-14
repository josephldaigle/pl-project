<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/3/18
 * Time: 7:38 PM
 */


namespace Test\Unit\Data\Repository\Person;


use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Data\Repository\Person\PersonContactRepository;
use PapaLocal\Core\Data\TableGateway;
use PHPUnit\Framework\TestCase;


/**
 * Class PersonContactRepositoryTest
 *
 * @package PapaLocal\Test\Unit\Data\Repository\Person
 */
class PersonContactRepositoryTest extends TestCase
{

	public function testHasEmailAddressReturnsTrueOnSuccess()
	{
		// set up fixtures
		$qryResult = [
			array('personId' => 1)
		];
		$tableGatewayMock = $this->createMock(TableGateway::class);
		$tableGatewayMock->expects($this->once())
			->method('setTable')
			->with('v_person_email_address');
		$tableGatewayMock->expects($this->once())
			->method('findByColumns')
			->willReturn($qryResult);

		$resourcePoolMock = $this->createMock(DataResourcePool::class);
		$resourcePoolMock->expects($this->any())
			->method('getTableGateway')
			->willReturn($tableGatewayMock);

		// exercise SUT
		$repo = new PersonContactRepository($resourcePoolMock);
		$result = $repo->hasEmailAddress(1, 'test@example.com');

		// make assertions
		$this->assertTrue(is_bool($result), 'unexpected type');
		$this->assertSame(true, $result, 'unexpected value');
	}

	public function testHasEmailAddressReturnFalseWhenNoneFound()
	{
		// set up fixtures
		$tableGatewayMock = $this->createMock(TableGateway::class);
		$tableGatewayMock->expects($this->once())
		                 ->method('setTable')
		                 ->with('v_person_email_address');
		$tableGatewayMock->expects($this->once())
		                 ->method('findByColumns')
		                 ->willReturn([]);

		$resourcePoolMock = $this->createMock(DataResourcePool::class);
		$resourcePoolMock->expects($this->any())
		                 ->method('getTableGateway')
		                 ->willReturn($tableGatewayMock);

		// exercise SUT
		$repo = new PersonContactRepository($resourcePoolMock);
		$result = $repo->hasEmailAddress(1, 'test@example.com');

		// make assertions
		$this->assertTrue(is_bool($result), 'unexpected type');
		$this->assertSame(false, $result, 'unexpected value');
	}
}
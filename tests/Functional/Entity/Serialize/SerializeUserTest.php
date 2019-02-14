<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/7/18
 * Time: 1:46 PM
 */


namespace Test\Functional\Entity\Serialize;


use PapaLocal\Entity\User;
use PapaLocal\Test\WebTestCase;


/**
 * Class SerializeUserTest
 *
 * @package Test\Functional\Entity\Serialize
 */
class SerializeUserTest extends WebTestCase
{
    /**
     * @var
     */
    private $serializer;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        // set up fixtures
        $this->serializer = $this->diContainer->get('serializer');
    }

    public function testDenormalizeReturnsExpectedArrayKeysOnSuccess()
    {
        // set up fixtures
        $userArr = array(
            "personId" => "1",
            "firstName" => "Devon",
            "lastName" => "Clark",
            "about" => " I once dated Margaret Thatcher in high school.",
            "personTimeCreated" => "2017-10-24 11:53:19",
            "userId" => "1",
            "password" => "no login password",
            "timeZone" => "America/New_York",
            "isActive" => "1",
            "userTimeCreated" => "2017-11-08 20:52:38",
            "emailId" => "1",
            "username" => "devon@ewebify.com"
        );

        // exercise SUT
        $result = $this->serializer->denormalize($userArr, User::class, 'array');

        // make assertions
        $this->assertObjectHasAttribute('username', $result);
        $this->assertFalse(is_null($result->getUsername()));
        $this->assertObjectHasAttribute('password', $result);
        $this->assertFalse(is_null($result->getPassword()));
        $this->assertObjectHasAttribute('isActive', $result);
        $this->assertFalse(is_null($result->getIsActive()));
        $this->assertObjectHasAttribute('timeZone', $result);
        $this->assertFalse(is_null($result->getTimeZone()));
    }
}
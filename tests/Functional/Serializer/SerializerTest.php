<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/26/18
 * Time: 8:34 PM
 */


namespace Test\Functional\Serializer;


use PapaLocal\Test\SerializerDummy;
use PapaLocal\Test\TestDummy;
use PapaLocal\Test\WebTestCase;
use Symfony\Component\Serializer\Serializer;


/**
 * SerializerTest.
 */
class SerializerTest extends WebTestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        // fetch serializer
        $this->serializer = $this->diContainer->get('serializer');
    }

    /**
     * Tests that the serializer returns only obj members that have been set,
     * even if the attribute is requested in the call to normalize.
     */
    public function testNormalizeOmitsEmptyMembersWhenRequested()
    {
        // set up fixtures
        $obj = new SerializerDummy();
        $obj->setMemberOne('Member One');
        $obj->setMemberTwo('Member Two');
        $obj->setMemberThree('Member Three');

        // exercise SUT
        $result = $this->serializer->normalize($obj, 'array', array('attributes' => array(
            'memberOne',
            'memberTwo',
            'memberThree',
            'memberFour'
        )));

        // make assertions
        $this->assertArrayHasKey('memberOne', $result);
        $this->assertArrayHasKey('memberTwo', $result);
        $this->assertArrayHasKey('memberThree', $result);
        $this->assertArrayNotHasKey('memberFour', $result);
    }

    public function testSerializerCanUtilizeConstructorInjectionDuringDenormalization()
    {
        // set up fixtures
        $controlValue = 'controlValue';

        // exercise SUT
        $result = $this->serializer->denormalize(array('member' => $controlValue), TestDummy::class, 'array');

        // make assertions
        $this->assertInstanceOf(TestDummy::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('member', $result, 'attr [member] not found');
        $this->assertSame($controlValue, $result->getMember(), 'unexpected value');
    }

}
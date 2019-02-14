<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/25/18
 * Time: 11:39 AM
 */

namespace Test\Unit\Entity\Validation;


use PapaLocal\Data\AttrType;
use PapaLocal\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * AddressValidationTest.
 *
 * @package Test\Unit\Entity\Validation
 */
class AddressValidationTest extends KernelTestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');
    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: create
     |-----------------------------------
    */

    public function testValidateReturnsNoErrorOnSuccessOnCreate()
    {
        // set up fixtures
        $address = (new Address())
            ->setStreetAddress('200 Any Rd.')
            ->setCity('Anytown')
            ->setState('Georgia')
            ->setPostalCode(22222)
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_PHYSICAL);

        // exercise SUT
        $errors = $this->validator->validate($address, null, array('create'));

        // make assertions
        $this->assertCount(0, $errors, $errors->__toString());
    }

    public function testValidateReturnsErrorWhenIdIsNotBlankOnCreate()
    {
        // set up fixtures
        $address = (new Address())
            ->setId(3)
            ->setStreetAddress('200 Any Rd.')
            ->setCity('Anytown')
            ->setState('Georgia')
            ->setPostalCode(22222)
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_PHYSICAL);

        // exercise SUT
        $errors = $this->validator->validate($address, null, array('create'));

        //assertions
        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
        $this->assertLessThan(2, $errors->count(), 'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
        $this->assertSame('Id must be blank.', $errors->get(0)->getMessage(),  'unexpected error message: ' . $errors->get(0)->getMessage());
    }

    public function testValidateReturnsErrorWhenStreetAddressIsBlankOnCreate()
    {
        // set up fixtures
        $address = (new Address())
            ->setCity('Anytown')
            ->setState('Georgia')
            ->setPostalCode(22222)
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_PHYSICAL);

        // exercise SUT
        $errors = $this->validator->validate($address, null, array('create'));

        //assertions
        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
        $this->assertLessThan(2, $errors->count(), 'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
        $this->assertSame('Street address must be present.', $errors->get(0)->getMessage(),  'unexpected error message: ' . $errors->get(0)->getMessage());
    }

    public function testValidateReturnsErrorWhenCityIsBlankOnCreate()
    {
        // set up fixtures
        $address = (new Address())
            ->setStreetAddress('200 Any Rd.')
            ->setState('Georgia')
            ->setPostalCode(22222)
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_PHYSICAL);

        // exercise SUT
        $errors = $this->validator->validate($address, null, array('create'));

        //assertions
        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
        $this->assertLessThan(2, $errors->count(), 'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
        $this->assertSame('City must be present.', $errors->get(0)->getMessage(),  'unexpected error message: ' . $errors->get(0)->getMessage());
    }

    public function testValidateReturnsErrorWhenStateIsBlankOnCreate()
    {
        // set up fixtures
        $address = (new Address())
            ->setStreetAddress('200 Any Rd.')
            ->setCity('Anytown')
            ->setPostalCode(22222)
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_PHYSICAL);

        // exercise SUT
        $errors = $this->validator->validate($address, null, array('create'));

        //assertions
        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
        $this->assertLessThan(2, $errors->count(), 'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
        $this->assertSame('State must be present.', $errors->get(0)->getMessage(),  'unexpected error message: ' . $errors->get(0)->getMessage());
    }

    public function testValidateReturnsErrorWhenPostalCodeIsBlankOnCreate()
    {
        // set up fixtures
        $address = (new Address())
            ->setStreetAddress('200 Any Rd.')
            ->setCity('Anytown')
            ->setState('Georgia')
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_PHYSICAL);

        // exercise SUT
        $errors = $this->validator->validate($address, null, array('create'));

        //assertions
        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
        $this->assertLessThan(2, $errors->count(), 'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
        $this->assertSame('Postal code must be present.', $errors->get(0)->getMessage(),  'unexpected error message: ' . $errors->get(0)->getMessage());
    }

    public function testValidateReturnsErrorWhenCountryIsBlankOnCreate()
    {
        // set up fixtures
        $address = (new Address())
            ->setStreetAddress('200 Any Rd.')
            ->setCity('Anytown')
            ->setState('Georgia')
            ->setPostalCode(22222)
            ->setType(AttrType::ADDRESS_PHYSICAL);

        // exercise SUT
        $errors = $this->validator->validate($address, null, array('create'));

        //assertions
        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
        $this->assertLessThan(2, $errors->count(), 'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
        $this->assertSame('Country must be present.', $errors->get(0)->getMessage(),  'unexpected error message: ' . $errors->get(0)->getMessage());
    }

//    public function testValidateReturnsErrorWhenTypeIsBlankOnCreate()
//    {
//        // set up fixtures
//        $address = (new Address())
//            ->setStreetAddress('200 Any Rd.')
//            ->setCity('Anytown')
//            ->setState('Georgia')
//            ->setPostalCode(22222)
//            ->setCountry('United States');
//
//        // exercise SUT
//        $errors = $this->validator->validate($address, null, array('create'));
//
//        //assertions
//        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
//        $this->assertLessThan(2, $errors->count(), 'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
//        $this->assertSame('Type must be present.', $errors->get(0)->getMessage(),  'unexpected error message: ' . $errors->get(0)->getMessage());
//    }

    public function testValidateReturnsErrorWhenTimeCreatedIsNotBlankOnCreate()
    {
        // set up fixtures
        $address = (new Address())
            ->setStreetAddress('200 Any Rd.')
            ->setCity('Anytown')
            ->setState('Georgia')
            ->setPostalCode(22222)
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_PHYSICAL)
            ->setTimeCreated('2017-10-23 13:56:38');

        // exercise SUT
        $errors = $this->validator->validate($address, null, array('create'));

        //assertions
        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
        $this->assertLessThan(2, $errors->count(),
            'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
        $this->assertSame('Time created must be blank.', $errors->get(0)->getMessage(),
            'unexpected error message: ' . $errors->get(0)->getMessage());
    }

    public function testValidateReturnsErrorWhenTimeUpdatedIsNotBlankOnCreate()
    {
        // set up fixtures
        $address = (new Address())
            ->setStreetAddress('200 Any Rd.')
            ->setCity('Anytown')
            ->setState('Georgia')
            ->setPostalCode(22222)
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_PHYSICAL)
            ->setTimeUpdated('2017-10-23 13:56:38');

        // exercise SUT
        $errors = $this->validator->validate($address, null, array('create'));

        //assertions
        $this->assertGreaterThan(0, $errors->count(), 'expected error not found');
        $this->assertLessThan(2, $errors->count(), 'unexpected validation errors exists' . PHP_EOL . $errors->__toString());
        $this->assertSame('Time updated must be blank.', $errors->get(0)->getMessage(),  'unexpected error message: ' . $errors->get(0)->getMessage());
    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: create
     |-----------------------------------
    */
    public function testValidateReturnsNoErrorOnSuccessOnUpdate()
    {
        // set up fixtures
        $address = (new Address())
            ->setId(3)
            ->setStreetAddress('200 Any Rd.')
            ->setCity('Anytown')
            ->setState('Georgia')
            ->setPostalCode(22222)
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_PHYSICAL);

        // exercise SUT
        $errors = $this->validator->validate($address, null, array('update'));

        // make assertions
        $this->assertCount(0, $errors, $errors->__toString());
    }
}
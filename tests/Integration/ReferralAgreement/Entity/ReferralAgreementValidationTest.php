<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 12/6/17
 * Time: 12:39 PM
 */


namespace Test\Integration\ReferralAgreement\Entity;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\ValueObject\Strategy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class ReferralAgreementValidationTest.
 *
 * @package Test\Unit\ReferralAgreement\Entity
 */
class ReferralAgreementValidationTest extends KernelTestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * {@inheritdoc}
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
        //set up fixtures
        $guidmock = $this->createMock(Guid::class);

        $referralAgreement = (new ReferralAgreement(
            $guidmock,
            $guidmock,
            'Test Agreement',
            'This is a test referral agreement.',
            60,
            Strategy::WEEKLY()->getValue(),
            30.0)
        );

        //exercise SUT
        $errors = $this->validator->validate($referralAgreement, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsCorrectErrorWhenReferralNameIsBlankOnCreate()
    {
        //set up fixtures
        $guidmock = $this->createMock(Guid::class);

        $referralAgreement = (new ReferralAgreement(
            $guidmock,
            $guidmock,
            '',
            'This is a test referral agreement.',
            60,
            Strategy::WEEKLY()->getValue(),
            30.0)
        );

        //exercise SUT
        $errors = $this->validator->validate($referralAgreement, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('name', $errors->get(0)->getPropertyPath(), 'incorrect property');
        $this->assertEquals(NotBlank::IS_BLANK_ERROR, $errors->get(0)->getCode(), 'unexpected error code');
    }

    public function testValidateReturnsCorrectErrorWhenReferralDescriptionIsBlankOnCreate()
    {
        //set up fixtures
        $guidmock = $this->createMock(Guid::class);

        $referralAgreement = (new ReferralAgreement(
            $guidmock,
            $guidmock,
            'Test Agreement',
            '',
            60,
            Strategy::WEEKLY()->getValue(),
            30.0)
        );

        //exercise SUT
        $errors = $this->validator->validate($referralAgreement, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('description', $errors->get(0)->getPropertyPath(), 'incorrect property');
        $this->assertEquals(NotBlank::IS_BLANK_ERROR, $errors->get(0)->getCode(), 'unexpected code');
    }

}
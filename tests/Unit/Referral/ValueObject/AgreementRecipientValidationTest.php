<?php

/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/17/18
 * Time: 12:08 PM
 */

namespace Test\Unit\Referral\ValueObject;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;


class AgreementRecipientValidationTest extends KernelTestCase
{
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');

    }

    public function testValidateReturnsNoErrorOnSuccess()
    {
        //set up fixtures
        $agreementRecipient = new AgreementRecipient(new Guid('guid'));

        //exercise SUT
        $errors = $this->validator->validate($agreementRecipient, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenAgreementRecipientGuidIsNotPresent()
    {
        //set up fixtures
        $referral = new AgreementRecipient(null);

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

}
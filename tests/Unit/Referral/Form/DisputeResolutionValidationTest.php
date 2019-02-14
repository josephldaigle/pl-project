<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 12/6/17
 * Time: 12:01 PM
 */

namespace Test\Unit\Referral\Form;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\Form\DisputeResolution;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;


class DisputeResolutionValidationTest extends KernelTestCase
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
        $referral = new DisputeResolution(
            'guid',
            'approved',
            'Reviewer Note'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenGuidIsNotPresent()
    {
        //set up fixtures
        $referral = new DisputeResolution(
            null,
            'denied',
            'Reviewer Note'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenResolutionIsNotPresent()
    {
        //set up fixtures
        $referral = new DisputeResolution(
            'guid',
            '',
            'Reviewer Note'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(Choice::NO_SUCH_CHOICE_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenResolutionIsInvalid()
    {
        //set up fixtures
        $referral = new DisputeResolution(
            'guid',
            'Resolution',
            'Reviewer Note'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(Choice::NO_SUCH_CHOICE_ERROR, $errors[0]->getCode(),'unexpected error message');
    }



    public function testValidateReturnsErrorWhenReviewerNoteIsNotPresent()
    {
        //set up fixtures
        $referral = new DisputeResolution(
            'guid',
            'approved',
            ''
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }
}
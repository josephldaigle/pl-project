<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 12/6/17
 * Time: 12:01 PM
 */

namespace Test\Unit\Referral\Form;


use PapaLocal\Referral\Form\ReferralRate;
use PapaLocal\Referral\Validation\ScoreConstraint;
use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;


class ReferralRateValidationTest extends WebDatabaseTestCase
{
    protected function setUp()
    {
        $this->configureDataSet(array('Referral'));
        parent::setUp();

        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');
    }

    public function testValidateReturnsNoErrorOnSuccess()
    {
        $referralGuid = $this->getConnection()
            ->createQueryTable('status', "SELECT guid FROM Referral WHERE currentPlace='finalized' LIMIT 1")
            ->getRow(0)['guid'];
        
        //set up fixtures
        $referral = new ReferralRate(
            $referralGuid,
            3,
            'feedback'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenGuidIsNotPresent()
    {
        //set up fixtures
        $referral = new ReferralRate(
            '',
            3,
            'feedback'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors->get(0)->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenRateIsNotPresent()
    {
        $referralGuid = $this->getConnection()
            ->createQueryTable('status', "SELECT guid FROM Referral WHERE currentPlace='finalized' LIMIT 1")
            ->getRow(0)['guid'];

        //set up fixtures
        $referral = new ReferralRate(
            $referralGuid,
            null,
            'feedback'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors->get(0)->getCode(), 'unexpected error message');
    }

    public function testValidateReturnsErrorWhenRateIsTooLow()
    {
        $referralGuid = $this->getConnection()
            ->createQueryTable('status', "SELECT guid FROM Referral WHERE currentPlace='finalized' LIMIT 1")
            ->getRow(0)['guid'];

        //set up fixtures
        $referral = new ReferralRate(
            $referralGuid,
            0,
            'feedback'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(Range::TOO_LOW_ERROR, $errors->get(0)->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenRateIsLowerThanThreeWithContactRecipient()
    {
        $referralGuid = $this->getConnection()
            ->createQueryTable('status', "SELECT guid FROM Referral WHERE currentPlace='created' LIMIT 1")
            ->getRow(0)['guid'];

        //set up fixtures
        $referral = new ReferralRate(
            $referralGuid,
            1,
            'feedback'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(ScoreConstraint::IS_INVALID_SCORE, $errors->get(0)->getCode(),'unexpected error message');
    }

    public function testValidateReturnsNoErrorWhenRateIsLowerThanThreeWithAgreementRecipient()
    {
        $referralGuid = $this->getConnection()
            ->createQueryTable('status', "SELECT guid FROM Referral WHERE currentPlace='disputed' LIMIT 1")
            ->getRow(0)['guid'];

        //set up fixtures
        $referral = new ReferralRate(
            $referralGuid,
            2,
            'feedback'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenRateIsTooHigh()
    {
        $referralGuid = $this->getConnection()
            ->createQueryTable('status', "SELECT guid FROM Referral WHERE currentPlace='finalized' LIMIT 1")
            ->getRow(0)['guid'];

        //set up fixtures
        $referral = new ReferralRate(
            $referralGuid,
            6,
            'feedback'
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(Range::TOO_HIGH_ERROR, $errors->get(0)->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenFeedbackIsNotPresent()
    {
        $referralGuid = $this->getConnection()
            ->createQueryTable('status', "SELECT guid FROM Referral WHERE currentPlace='finalized' LIMIT 1")
            ->getRow(0)['guid'];

        //set up fixtures
        $referral = new ReferralRate(
            $referralGuid,
            3,
            ''
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors->get(0)->getCode(),'unexpected error message');
    }
}
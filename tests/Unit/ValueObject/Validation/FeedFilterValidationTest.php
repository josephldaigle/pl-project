<?php
/**
 * Created by PhpStorm.
 * Date: 6/10/18
 * Time: 10:59 AM
 */

namespace Test\Unit\ValueObject\Validation;


use PapaLocal\Core\Validation\BeforeNow;
use PapaLocal\Feed\Form\FeedFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\NotBlank;


/**
 * Class RegisterUserValidationTest
 */
class FeedFilterValidationTest extends KernelTestCase
{
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');

    }

    /*
     |-----------------------------------
     | DATA PROVIDER
     |-----------------------------------
    */
    public function validTypeProvider()
    {
        return [
            ['transaction'],
            ['agreement'],
            ['referral'],
        ];
    }

    public function twoValidTypesProvider()
    {
        return [
            ['transaction', 'agreement'],
            ['transaction', 'referral'],
            ['agreement', 'referral'],
        ];
    }

    public function threeValidTypesProvider()
    {
        return [
            ['transaction', 'agreement', 'referral'],
        ];
    }

    public function invalidTypeProvider()
    {
        return [
            ['transaction', 'agreement', 'bad type'],
        ];
    }
    public function validDateProvider()
    {
        return [
            ['2018-04-03 16:15:04', '2018-04-03 16:15:10'],
            ['2018-04-03 16:15:04', '2018-04-03 16:59:04'],
            ['2018-04-03 16:15:04', '2018-04-03 22:15:04'],
            ['2018-04-03 16:15:04', '2018-04-13 16:15:04'],
            ['2018-04-03 16:15:04', '2018-11-13 16:15:04'],
            ['2018-04-03 16:15:04', '2020-04-13 16:15:04'],
        ];
    }

    public function invalidDateProvider()
    {
        return [
//            'start date (year) is too late' is covered by the @BeforeNow constraint
            'start date (month) is too late' => ['2018-07-03 16:15:04', '2018-04-03 16:15:04'],
            'start date (day) is too late' => ['2018-04-12 16:15:04', '2018-04-03 16:15:04'],
            'start date (hours) is too late' => ['2018-04-03 23:15:04', '2018-04-03 16:15:04'],
            'start date (minutes) is too late' => ['2018-04-03 16:50:04', '2018-04-03 16:15:04'],
            'start date (seconds) is too late' => ['2018-04-03 16:15:40', '2018-04-03 16:15:04'],
        ];
    }

    public function testValidateReturnsNoErrorWhenFilterIsValid()
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array('transaction'))
            ->setStartDate('2018-04-03 16:15:04')
            ->setEndDate('2018-04-03 16:15:04')
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenTypeIsEmpty()
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array())
            ->setStartDate('2018-04-03 16:15:04')
            ->setEndDate('2018-04-03 16:15:04')
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(2, $errors, 'unexpected validation errors exists');
        $this->assertEquals(NotBlank::IS_BLANK_ERROR, $errors->get(0)->getCode(), 'unexpected error message code');
        $this->assertEquals(Choice::NO_SUCH_CHOICE_ERROR, $errors->get(1)->getCode(), 'unexpected error message code');

    }

    /**
     * @dataProvider validTypeProvider
     */
    public function testValidateReturnsNoErrorWhenTypeIsValid($type)
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array($type))
            ->setStartDate('2018-04-03 16:15:04')
            ->setEndDate('2018-04-03 16:15:04')
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');

    }

    /**
     * @dataProvider twoValidTypesProvider
     */
    public function testValidateReturnsNoErrorWhenTwoValidTypesPresent($type1, $type2)
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array($type1, $type2))
            ->setStartDate('2018-04-03 16:15:04')
            ->setEndDate('2018-04-03 16:15:04')
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');

    }

    /**
     * @dataProvider threeValidTypesProvider
     */
    public function testValidateReturnsNoErrorWhenThreeValidTypesPresent($type1, $type2, $type3)
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array($type1, $type2, $type3))
            ->setStartDate('2018-04-03 16:15:04')
            ->setEndDate('2018-04-03 16:15:04')
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');

    }

    /**
     * @dataProvider invalidTypeProvider
     */
    public function testValidateReturnsErrorWhenTypeIsInvalid($type1, $type2, $type3)
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array($type1, $type2, $type3))
            ->setStartDate('2018-04-03 16:15:04')
            ->setEndDate('2018-04-03 16:15:04')
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Invalid type selected.', $errors[0]->getMessage(),  'unexpected error message');

    }

    public function testValidateReturnsErrorWhenStartDateIsNotPresent()
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array('transaction'))
            ->setStartDate('')
            ->setEndDate('2018-04-03 16:15:04')
            ->setSortOrder('LAST_UPDATED');


        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors->get(0)->getCode(),  'unexpected error code');

    }

    public function testValidateReturnsErrorWhenStartDateIsGreaterThanOrEqualNow()
    {
        //set up fixtures
        $startDate = date('Y-m-d H:i:s', strtotime('+1 week'));
        $endDate = date('Y-m-d H:i:s', strtotime('+2 week'));

        $filter = (new FeedFilter())
            ->setTypes(array('transaction'))
            ->setStartDate($startDate)
            ->setEndDate($endDate)
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(BeforeNow::AFTER_NOW_ERROR, $errors->get(0)->getCode(),  'unexpected error code');

    }

    /**
     * @dataProvider validDateProvider
     */
    public function testValidateReturnsNoErrorWhenDateIsValid($startDate, $endDate)
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array('transaction'))
            ->setStartDate($startDate)
            ->setEndDate($endDate)
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenEndDateIsNotPresent()
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array('transaction'))
            ->setStartDate('2018-04-03 16:15:04')
            ->setEndDate('')
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(Expression::EXPRESSION_FAILED_ERROR,  $errors->get(0)->getCode(), 'unexpected error code');

    }

    /**
     * @dataProvider invalidDateProvider
     */
    public function testValidateReturnsErrorWhenDateIsInvalid($startDate, $endDate)
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array('transaction'))
            ->setStartDate($startDate)
            ->setEndDate($endDate)
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The start date must be earlier than end date.', $errors[0]->getMessage(),  'unexpected error message');

    }

    public function testValidateReturnsErrorWhenSortOrderIsNotPresent()
    {
        //set up fixtures
        $filter = (new FeedFilter())
            ->setTypes(array('transaction'))
            ->setStartDate('2018-04-03 16:15:04')
            ->setEndDate('2018-04-03 16:15:04')
            ->setSortOrder('LAST_UPDATED');

        //exercise SUT
        $errors = $this->validator->validate($filter);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }
}
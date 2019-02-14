<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/1/18
 * Time: 9:52 AM
 */


namespace Test\Integration\Core\ValueObject\Validation;


use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class PhoneNumberValidationTest
 *
 * @package Test\Integration\Core\ValueObject\Validation
 */
class PhoneNumberValidationTest extends KernelTestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');
    }

    public function testValidateReturnsNoErrorWhenPhoneNumberIsValid()
    {
        // set up fixtures
        $phoneNumber = new PhoneNumber('5555555555', PhoneNumberType::BUSINESS());

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber);

        //assertions
        $this->assertCount(0, $errors, (string) $errors);
    }

    public function annotationRulesProvider()
    {
        return [
            'invalid type test, non-numeric' =>
                ['55ab5555is', PhoneNumberType::PERSONAL(), 1, [Type::INVALID_TYPE_ERROR]],
            'invalid regex test, begins with zero' =>
                ['0555555555', PhoneNumberType::PERSONAL(), 1, [Regex::REGEX_FAILED_ERROR]],
            'invalid length test, too long (11)' =>
                ['55555555555', PhoneNumberType::PERSONAL(), 1, [Length::TOO_LONG_ERROR]],
            'invalid length test, too short (9)' =>
                ['555555555', PhoneNumberType::PERSONAL(), 1, [Length::TOO_SHORT_ERROR]]
        ];
    }

    /**
     * @dataProvider annotationRulesProvider()
     *
     * @param $number
     * @param $type
     * @param $expectedErrCount
     * @param $expectedErrCodes
     */
    public function testAnnotationRules($number, $type, $expectedErrCount, $expectedErrCodes)
    {
        // set up fixtures
        $phoneNumber = new PhoneNumber($number, $type);

        // exercise SUT
        $errors = $this->validator->validate($phoneNumber);

        // make assertions
        $this->assertEquals($errors->count(), $expectedErrCount, 'unexpected errors count');

        foreach ($expectedErrCodes as $expectedErrCode) {
            $this->assertCount(1, $errors->findByCodes($expectedErrCode), 'code not found: ' . $expectedErrCode);
        }
    }

}
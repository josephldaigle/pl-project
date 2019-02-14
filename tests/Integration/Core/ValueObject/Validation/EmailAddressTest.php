<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/1/18
 * Time: 11:12 AM
 */


namespace Test\Integration\Core\ValueObject\Validation;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class EmailAddressTest
 *
 * @package Test\Integration\Core\ValueObject\Validation
 */
class EmailAddressTest extends KernelTestCase
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

    public function testValidateReturnsNoErrorWhenEmailAddressIsValid()
    {
        // set up fixtures
        $emailAddress = new EmailAddress('test@papalocal.com', EmailAddressType::PERSONAL());

        //exercise SUT
        $errors = $this->validator->validate($emailAddress);

        //assertions
        $this->assertCount(0, $errors, (string) $errors);

    }

    public function annotationRulesProvider()
    {
        return [
            'invalid format test' =>
                ['test.com', EmailAddressType::PERSONAL(), 1, [Email::INVALID_FORMAT_ERROR]],
            'cannot be blank' =>
                ['', EmailAddressType::PERSONAL(), 1, [NotBlank::IS_BLANK_ERROR]],
            'invalid length test, too long (37)' =>
                ['abcdefghijklmnopqrstuvwxyzab@test.com', EmailAddressType::PERSONAL(), 1, [Length::TOO_LONG_ERROR]]
        ];
    }

    /**
     * @dataProvider annotationRulesProvider()
     *
     * @param $emailAddress
     * @param $type
     * @param $expectedErrCount
     * @param $expectedErrCodes
     */
    public function testAnnotationRules($emailAddress, $type, $expectedErrCount, $expectedErrCodes)
    {
        // set up fixtures
        $emailAddress = new EmailAddress($emailAddress, $type);

        // exercise SUT
        $errors = $this->validator->validate($emailAddress);

        // make assertions
        $this->assertEquals($errors->count(), $expectedErrCount, 'unexpected errors count');

        foreach ($expectedErrCodes as $expectedErrCode) {
            $this->assertCount(1, $errors->findByCodes($expectedErrCode), 'code not found: ' . $expectedErrCode);
        }
    }

}
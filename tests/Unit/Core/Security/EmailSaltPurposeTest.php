<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/3/18
 * Time: 6:41 PM
 */


namespace Test\Unit\Core\Security;


use PapaLocal\Core\Enum\AbstractEnum;
use PapaLocal\Core\Security\EmailSaltPurpose;
use PHPUnit\Framework\TestCase;


/**
 * Class EmailSaltPurposeTest
 *
 * @package Test\Unit\Core\Security
 */
class EmailSaltPurposeTest extends TestCase
{
	public function testCanInstantiate()
	{
		$enum = EmailSaltPurpose::PURPOSE_FORGOT_PASS();
		$this->assertInstanceOf(AbstractEnum::class, $enum, 'unexpected type');
		$this->assertEquals('ForgotPassword', $enum->getValue(), 'unexpected value');
	}

	/**
	 * @dataProvider canValidateExternalValuesProvider
	 */
	public function testCanValidateExternalValues($value, $expectedResult)
	{
		$result = EmailSaltPurpose::isValid($value);

		$this->assertSame($expectedResult, $result, 'unexpected result');
	}

	public function canValidateExternalValuesProvider()
	{
		return [
			'PURPOSE_FORGOT_PASS' => ['ForgotPassword', true],
			'PURPOSE_RESET_PASS' => ['ResetPassword', true],
			'PURPOSE_REFERRAL_AGMT_INVITE' => ['ReferralAgreementInvite', true],
			'invalid value' => ['bad value', false],
		];
	}
}
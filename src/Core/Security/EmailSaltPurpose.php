<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/3/18
 */


namespace PapaLocal\Core\Security;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class EmailSaltPurpose
 *
 * @package PapaLocal\Core\Security
 */
class EmailSaltPurpose extends AbstractEnum
{
	private const PURPOSE_FORGOT_PASS = 'ForgotPassword';
	private const PURPOSE_RESET_PASS = 'ResetPassword';
	private const PURPOSE_REFERRAL_AGMT_INVITE = 'ReferralAgreementInvite';
}
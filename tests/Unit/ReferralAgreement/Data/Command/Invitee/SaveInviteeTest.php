<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/1/18
 * Time: 4:08 PM
 */

namespace Test\Unit\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\ReferralAgreement\Data\Command\Invitee\SaveInvitee;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PHPUnit\Framework\TestCase;


/**
 * Class SaveInviteeTest
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Invitee
 */
class SaveInviteeTest extends TestCase
{
    public function testCanInstantiate()
    {
        // set up fixtures
        $referralAgreementMock = $this->createMock(ReferralAgreementInvitee::class);

        // exercise SUT
        $command = new SaveInvitee($referralAgreementMock);

        // make assertions
        $this->assertInstanceOf(SaveInvitee::class, $command);
    }
}
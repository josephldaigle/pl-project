<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/3/18
 */

namespace Test\Integration\ReferralAgreement\Form;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Class CreateAgreementFormValidationTest.
 *
 * @package Test\Integration\ReferralAgreement\Form
 */
class CreateAgreementFormValidationTest extends KernelTestCase
{
    private $validator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // boot kernel
        self::bootKernel();

        // fetch validator
        $this->validator = static::$kernel->getContainer()->get('validator');
    }

    public function testValidateCreateAgreementForm()
    {
        $this->markTestIncomplete();
    }
}
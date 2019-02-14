<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/29/18
 */


namespace Test\Integration\ReferralAgreement\Entity;


use PapaLocal\Core\Data\Record;
use PapaLocal\Core\Data\RecordSet;
use PapaLocal\Core\ValueObject\Collection\ListBuilder;
use PapaLocal\ReferralAgreement\Entity\Factory\ReferralAgreementFactory;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Class ReferralAgreementFactoryTest
 *
 * @package Test\Integration\ReferralAgreement\Entity
 */
class ReferralAgreementFactoryTest extends KernelTestCase
{
    /**
     * @var ReferralAgreementFactory
     */
    private $referralAgreementFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // boot kernel
        self::bootKernel();

        // fetch services
        $this->referralAgreementFactory = self::$container->get('PapaLocal\ReferralAgreement\Entity\Factory\ReferralAgreementFactory');
    }

    public function testCanCreateFromRecords()
    {

        $agreementGuid = 'fba15a6e-6a76-46d6-b0cb-3adacebeec97';
        // set up fixtures
        $headerRecord = new Record(array(
            'guid' => $agreementGuid,
            'companyGuid' => '0939885d-22cb-4300-9647-05dcd6282285',
            'name' => 'Test Agreement',
            'description' => 'A referral agreement',
            'quantity' => 3,
            'bid' => 30.00,
            'strategy' => 'weekly',
            'ownerGuid' => 'b896064e-2fe1-491b-9c31-3344678277f5',
            'timeCreated' => '2018-06-27 18:18:50'
        ));

        $locationRecords = new RecordSet(array(
            new Record(array(
                'id' => 1,
                'agreementId' => 3,
                'location' => 'Peachtree City, GA',
                'type' => 'include',
                'agreementGuid' => $agreementGuid,
            )),
            new Record(array(
                'id' => 1,
                'agreementId' => 3,
                'location' => 'Newnan, GA',
                'type' => 'exclude',
                'agreementGuid' => $agreementGuid,
            ))
        ));

        $serviceRecords = new RecordSet(array(
            new Record(array(
                'id' => 1,
                'agreementId' => 3,
                'service' => 'PVC Plumbing',
                'type' => 'include',
                'agreementGuid' => $agreementGuid,
            )),
            new Record(array(
                'id' => 1,
                'agreementId' => 3,
                'service' => 'Copper plumbing',
                'type' => 'exclude',
                'agreementGuid' => $agreementGuid,
            ))
        ));

        $statusRecords = new RecordSet(array(
            new Record(array(
                'id' => 1,
                'agreementGuid' => $agreementGuid,
                'status' => 'Inactive',
                'reasonId' => 1,
                'reason' => 'Created',
                'timeUpdated' => '2018-10-20 22:13:58',
                'updaterUserGuid' => '00929a97-0c57-432c-905a-074e4b6ec9c6',
                'updaterFirstName' => 'Guy',
                'updaterLastName' => 'Tester',
            ))
        ));

        // exercise SUT
        $referralAgreement = $this->referralAgreementFactory->createFromRecords($headerRecord, $locationRecords, $serviceRecords, $statusRecords);

        $this->assertInstanceOf(ReferralAgreement::class, $referralAgreement, 'unexpected type');
    }

    public function testCanCreateListBuilder()
    {
        // exercise SUT
        $result = $this->referralAgreementFactory->getListBuilder();

        // make assertions
        $this->assertInstanceOf(ListBuilder::class, $result);

    }
}
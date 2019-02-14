<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 11/15/18
 * Time: 2:12 PM
 */

namespace Test\Integration\Referral\Entity;


use PapaLocal\Core\Data\Record;
use PapaLocal\Core\Data\RecordSet;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Referral\Entity\Factory\ReferralFactory;
use PapaLocal\Referral\Entity\Referral;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class ReferralFactoryTest
 *
 * @package Test\Integration\Referral\Entity
 */
class ReferralFactoryTest extends KernelTestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp()
    {
        parent::setUp();

        // boot kernel
        self::bootKernel();

        // fetch serializer
        $this->serializer = self::$kernel->getContainer()->get('serializer');
    }

    public function testFromRecordReturnsSingleReferral()
    {
        $referralArray = array(
            'id' => '3',
            'guid' => '',
            'providerUserGuid' => '',
            'currentPlace' => '',
            'firstName' => '',
            'lastName' => '',
            'phoneNumber' => '',
            'emailAddress' => '',
            'address' => "{\"streetAddress\":\"3453 Lindenwood Drive\",\"city\":\"Laurel\",\"state\":\"Maryland\",\"stateAbbreviated\":\"\",\"postalCode\":\"20724\",\"country\":\"United States\",\"countryAbbreviated\":\"\",\"type\":null}",
            'about' => '',
            'note' => '',
            'agreementGuid' => '',
            'recipientFirstName' => '',
            'recipientLastName' => '',
            'recipientPhoneNumber' => '',
            'recipientEmailAddress' => '',
            'contactGuid' => '',
            'score' => 1,
            'feedback' => '',
            'resolution' => '',
            'reviewerGuid' => '',
            'reviewerNote' => '',
            'timeCreated' => '',
            'timeUpdated' => ''
        );
        $record = new Record($referralArray);

        $factory = self::$container->get(ReferralFactory::class);
        $referral = $factory->fromRecord($record);

        $this->assertInstanceOf(Referral::class, $referral, 'unexpected type');
    }

    public function testFromRecordSetReturnsCollectionOfReferrals()
    {
        $referralArray1 = array(
            'id' => '1',
            'guid' => '',
            'providerUserGuid' => '',
            'currentPlace' => '',
            'firstName' => '',
            'lastName' => '',
            'phoneNumber' => '',
            'emailAddress' => '',
            'address' => "{\"streetAddress\":\"3453 Lindenwood Drive\",\"city\":\"Laurel\",\"state\":\"Maryland\",\"stateAbbreviated\":\"\",\"postalCode\":\"20724\",\"country\":\"United States\",\"countryAbbreviated\":\"\",\"type\":null}",
            'about' => '',
            'note' => '',
            'agreementGuid' => '',
            'recipientFirstName' => '',
            'recipientLastName' => '',
            'recipientPhoneNumber' => '',
            'recipientEmailAddress' => '',
            'contactGuid' => '',
            'score' => 1,
            'feedback' => '',
            'resolution' => '',
            'reviewerGuid' => '',
            'reviewerNote' => '',
            'timeCreated' => '',
            'timeUpdated' => ''
        );
        $record1 = new Record($referralArray1);

        $referralArray2 = array(
            'id' => '2',
            'guid' => '',
            'providerUserGuid' => '',
            'currentPlace' => '',
            'firstName' => '',
            'lastName' => '',
            'phoneNumber' => '',
            'emailAddress' => '',
            'address' => "{\"streetAddress\":\"3453 Lindenwood Drive\",\"city\":\"Laurel\",\"state\":\"Maryland\",\"stateAbbreviated\":\"\",\"postalCode\":\"20724\",\"country\":\"United States\",\"countryAbbreviated\":\"\",\"type\":null}",
            'about' => '',
            'note' => '',
            'agreementGuid' => '',
            'recipientFirstName' => '',
            'recipientLastName' => '',
            'recipientPhoneNumber' => '',
            'recipientEmailAddress' => '',
            'contactGuid' => '',
            'score' => 1,
            'feedback' => '',
            'resolution' => '',
            'reviewerGuid' => '',
            'reviewerNote' => '',
            'timeCreated' => '',
            'timeUpdated' => ''
        );
        $record2 = new Record($referralArray2);

        $referralArray = array(
            $record1,
            $record2,
        );

        $recordSet = new RecordSet($referralArray);

        $factory = self::$container->get(ReferralFactory::class);
        $referralCollection = $factory->fromRecordSet($recordSet);

        $this->assertInstanceOf(Collection::class, $referralCollection, 'unexpected type');
    }
}
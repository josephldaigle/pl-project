<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/18/18
 * Time: 5:27 PM
 */


namespace Test\Functional\Data\Command\User\Billing;


use PapaLocal\Data\Command\User\Billing\CreateCreditCardProfile;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CreateCreditCardProfileTest.
 *
 * @package Test\Functional\Data\Command\User\Billing
 */
class CreateCreditCardProfileTest extends WebDatabaseTestCase
{
    /**
     * @var DataService
     */
    private $persistence;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
    	$this->configureDataSet([
    		'Person',
		    'User',
		    'L_CreditCardType',
		    'Address',
		    'BillingProfile',
		    'CreditCard'
	    ]);

        parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testCreateCreditCardProfileReturnsIdOnSuccessWhenAddressExists()
    {
        // set up fixtures
        $begCCRowCount = $this->getConnection()->getRowCount('CreditCard');
        $begAddressRowCount = $this->getConnection()->getRowCount('Address');

        $qryTable = $this->getConnection()
            ->createQueryTable('billing_profile',
                'SELECT userId, customerId FROM BillingProfile WHERE isActive = 1 LIMIT 1');

        $userId = intval($qryTable->getRow(0)['userId']);
        $customerId = intval($qryTable->getRow(0)['customerId']);

        $addressRow = $this->getConnection()
            ->createQueryTable('address', 'SELECT * FROM Address LIMIT 1')
            ->getRow(0);

        $address = (new Address())
            ->setId($addressRow['id'])
            ->setStreetAddress($addressRow['streetAddress'])
            ->setCity($addressRow['city'])
            ->setState($addressRow['state'])
            ->setPostalCode($addressRow['postalCode'])
            ->setCountry($addressRow['country']);

        $creditCard = (new CreditCard())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setCustomerId($customerId)
            ->setCardNumber(5555)
            ->setCardType(CreditCard::CARD_TYPE_VISA)
            ->setExpirationMonth(12)
            ->setExpirationYear(2024)
            ->setAddress($address);

        // exercise SUT
        $result = $this->persistence->execute(new CreateCreditCardProfile($userId, $creditCard));



        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertTableRowCount('CreditCard', $begCCRowCount + 1,
            'CreditCard table not incremented');
        $this->assertTableRowCount('Address', $begAddressRowCount,
            'unexpected Address table row count');
    }

    public function testCreateCreditCardProfileReturnsIdOnSuccessWhenAddressNotExists()
    {
        // set up fixtures
        $begCCRowCount = $this->getConnection()->getRowCount('CreditCard');
        $begAddressRowCount = $this->getConnection()->getRowCount('Address');

        $qryTable = $this->getConnection()
            ->createQueryTable('billing_profile',
                'SELECT userId, customerId FROM BillingProfile WHERE isActive = 1 LIMIT 1');

        $userId = intval($qryTable->getRow(0)['userId']);
        $customerId = intval($qryTable->getRow(0)['customerId']);

        $address = (new Address())
            ->setStreetAddress('444 Topfloor Dr')
            ->setCity('Sometownin')
            ->setState('Alabama')
            ->setPostalCode('22222')
            ->setCountry('United States');

        $creditCard = (new CreditCard())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setCustomerId($customerId)
            ->setCardNumber(5555)
            ->setCardType(CreditCard::CARD_TYPE_VISA)
            ->setExpirationMonth(12)
            ->setExpirationYear(2024)
            ->setAddress($address);

        // exercise SUT
        $result = $this->persistence->execute(new CreateCreditCardProfile($userId, $creditCard));

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertTableRowCount('CreditCard', $begCCRowCount + 1,
            'CreditCard table not incremented');
        $this->assertTableRowCount('Address', $begAddressRowCount + 1,
            'Address table not incremented');
    }

}
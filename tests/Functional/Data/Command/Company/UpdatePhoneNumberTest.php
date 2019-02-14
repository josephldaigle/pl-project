<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/9/18
 * Time: 8:20 PM
 */


namespace Test\Functional\Data\Command\Company;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Company\UpdatePhoneNumber;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\Exception\QueryCommandFailedException;
use PapaLocal\Entity\PhoneNumber;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * UpdatePhoneNumberTest.
 *
 * @package Test\Functional\Data\Command\Company
 */
class UpdatePhoneNumberTest extends WebDatabaseTestCase
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
    		'Company',
		    'PhoneNumber',
		    'L_PhoneNumberType',
		    'R_CompanyPhoneNumber'
	    ]);

        parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    /**
     * When company does not have phone type, use CreatePhoneNumber command
     * @expectedException PapaLocal\Entity\Exception\QueryCommandFailedException
     * @expectedExceptionMessageRegExp /^(Unable to find phone type)/
     */
    public function testUpdatePhoneThrowsExceptionWhenCompanyDoesNotHavePhoneType()
    {
        // set up fixtures
        $companyId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT companyId FROM R_CompanyPhoneNumber LIMIT 1')
            ->getRow(0)['companyId']);

        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber(2222222222)
            ->setType(AttrType::PHONE_FAX);

        // exercise SUT
        $cmd = new UpdatePhoneNumber($companyId, $phoneNumber);
        $this->persistence->execute($cmd);
    }

    public function testUpdatePhoneIsSuccessWhenPhoneNumberNotExists()
    {
        // set up fixtures
        $phoneRowCount = $this->getConnection()->getRowCount('PhoneNumber');
        $compPhoneRowCount = $this->getConnection()->getRowCount('R_CompanyPhoneNumber');

        $companyId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT companyId FROM R_CompanyPhoneNumber LIMIT 1')
            ->getRow(0)['companyId']);

        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber(9999999999)
            ->setType(AttrType::PHONE_BUSINESS);

        // exercise SUT
        $cmd = new UpdatePhoneNumber($companyId, $phoneNumber);
        $result = $this->persistence->execute($cmd);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertSame(1, $result, 'unexpected value');
        $this->assertTableRowCount('PhoneNumber', $phoneRowCount + 1,
            'PhoneNumber table not incremented');
        $this->assertTableRowCount('R_CompanyPhoneNumber', $compPhoneRowCount,
            'unexpected row count in R_CompanyPhoneNumber');
    }

    public function testUpdatePhoneIsSuccessWhenPhoneNumberExists()
    {
        // set up fixtures
        $phoneRowCount = $this->getConnection()->getRowCount('PhoneNumber');
        $compPhoneRowCount = $this->getConnection()->getRowCount('R_CompanyPhoneNumber');

        $companyId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT companyId FROM R_CompanyPhoneNumber LIMIT 1')
            ->getRow(0)['companyId']);

        $phoneArr = $this->getConnection()
            ->createQueryTable('phone_number', 'SELECT * FROM PhoneNumber LIMIT 1')
            ->getRow(0);

        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber($phoneArr['phoneNumber'])
            ->setType(AttrType::PHONE_BUSINESS);

        // exercise SUT
        $cmd = new UpdatePhoneNumber($companyId, $phoneNumber);
        $result = $this->persistence->execute($cmd);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertSame(1, $result, 'unexpected value');
        $this->assertTableRowCount('PhoneNumber', $phoneRowCount,
            'PhoneNumber table not incremented');
        $this->assertTableRowCount('R_CompanyPhoneNumber', $compPhoneRowCount,
            'unexpected row count in R_CompanyPhoneNumber');
    }

}
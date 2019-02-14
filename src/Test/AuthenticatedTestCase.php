<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/2/18
 */


namespace PapaLocal\Test;
use PHPUnit\DbUnit\DataSet\CsvDataSet;
use Symfony\Bundle\FrameworkBundle\Client;


/**
 * Class AuthenticatedTestCase.
 *
 * @package PapaLocal\Test
 */
abstract class AuthenticatedTestCase extends WebDatabaseTestCase
{
    use AuthenticatedTestTrait;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Called by setUp function before each test case.
     *
     * @return CsvDataSet
     */
    protected function getDataSet()
    {
        // test data dir
        $dir = $GLOBALS['DB_CSV_DIR'];

        // create a new CSV data set
        $dataSet = new CsvDataSet();
        $dataSet->addTable('Person', $dir . "Person.csv");
        $dataSet->addTable('EmailAddress', $dir . "EmailAddress.csv");
        $dataSet->addTable('L_EmailAddressType', $dir . "L_EmailAddressType.csv");
        $dataSet->addTable('R_PersonEmailAddress', $dir . "R_PersonEmailAddress.csv");
        $dataSet->addTable('User', $dir . "User.csv");
        $dataSet->addTable('L_UserRole', $dir . "L_UserRole.csv");
        $dataSet->addTable('R_UserApplicationRole', $dir . "R_UserApplicationRole.csv");
        $dataSet->addTable('R_UserNotification', $dir . "R_UserNotification.csv");

        return $dataSet;
    }


    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        // set up fixtures
        $this->client = self::createClient();
        $this->client->followRedirects();
        $this->client = $this->login($this->client);
    }
}
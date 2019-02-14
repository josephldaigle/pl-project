<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/6/18
 * Time: 4:53 PM
 */


namespace Test\Integration\Core\Security;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Core\Security\Cryptographer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Class CryptographerGuidIntegrationTest
 *
 * @package Test\Integration\Core\Security
 */
class CryptographerGuidIntegrationTest extends KernelTestCase
{
    /**
     * @var GuidGeneratorInterface
     */
    private $guidGenerator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->guidGenerator = static::$kernel->getContainer()->get('papalocal_core.guid_generator');
    }

    public function testCryptographerCanVerifyEmailKeyUsingGuidAsSalt()
    {
        // set up fixture
        $cryptographer = new Cryptographer();

        $guid = $this->guidGenerator->generate();

//        $salt = $cryptographer->createSalt($guid->value());
        $hash = $cryptographer->createHash($guid->value());

        //exercise SUT
        $compare = $cryptographer->verify($hash, $guid->value());

        // make assertions
        $this->assertTrue($compare, 'failed to verify hash');
    }

}
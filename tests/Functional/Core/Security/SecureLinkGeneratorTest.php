<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/7/18
 * Time: 8:24 AM
 */


namespace Test\Functional\Core\Security;


use PapaLocal\Core\Security\EmailSaltPurpose;
use PapaLocal\Core\Security\SecureLinkGenerator;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * Class SecureLinkGeneratorTest
 *
 * @package Test\Functional\Core\Security
 */
class SecureLinkGeneratorTest extends WebDatabaseTestCase
{
    /**
     * @var SecureLinkGenerator
     */
    private $secureLinkGenerator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->configureDataSet([
            'EmailKey'
        ]);

        parent::setUp();

        self::bootKernel();

        $this->secureLinkGenerator = static::$kernel->getContainer()->get('PapaLocal\Core\Security\SecureLinkGenerator');
    }

    public function testGetUrlIsSuccess()
    {
        // exercise SUT
        $url = $this->secureLinkGenerator->getUrl('login', array('var' => 'someValue'), UrlGeneratorInterface::ABSOLUTE_URL);

        // make assertions
        $expectedUrl = 'https://localhost/login?var=' . urlencode('someValue');

        $this->assertTrue(is_string($url), 'unexpected return type');
        $this->assertSame($expectedUrl, $url, 'unexpected value');
    }

    public function testGenerateSecureLinkIsSuccess()
    {
        // set up fixtures
        $begTableRowCount = $this->getConnection()->getRowCount('EmailKey');

        $emailAddress = new EmailAddress('test@papalocal.com', EmailAddressType::PERSONAL());
        $expiry = new \DateInterval('PT30M');
        $linkParams = ['var' => 'someValue'];

        // exercise SUT
        $url = $this->secureLinkGenerator->generateSecureLink($emailAddress, EmailSaltPurpose::PURPOSE_FORGOT_PASS(), 'login', $expiry, $linkParams);

        // make assertions
        $this->assertTableRowCount('EmailKey', $begTableRowCount + 1, 'unexpected table row count');
        $this->assertTrue(is_string($url), 'unexpected return type');
        $this->assertStringStartsWith('https://', $url, 'unexpected value');
        $this->assertContains('var=someValue', $url
        , 'could not find [var] param in url');
        $this->assertContains('key=', $url
        , 'could not find [salt] param in url');
        $this->assertContains('emailAddress=', $url
        , 'could not find [emailAddress] param in url');
    }
}
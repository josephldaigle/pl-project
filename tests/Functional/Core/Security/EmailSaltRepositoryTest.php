<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/6/18
 * Time: 10:07 PM
 */


namespace Test\Functional\Core\Security;


use PapaLocal\Core\Security\EmailSaltPurpose;
use PapaLocal\Core\Security\EmailSaltRepository;
use PapaLocal\Core\Security\ValueObject\EmailSalt;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class EmailSaltRepositoryTest
 *
 * @package Test\Functional\Core\Security
 */
class EmailSaltRepositoryTest extends WebDatabaseTestCase
{
    /**
     * @var EmailSaltRepository
     */
    private $emailSaltRepository;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->configureDataSet([
            'EmailKey'
        ]);
        parent::setUp();

        $this->emailSaltRepository = $this->diContainer->get('PapaLocal\Core\Data\RepositoryRegistry')->get(EmailSaltRepository::class);
    }

    public function testSaveIsSuccess()
    {
        // set up fixtures
        $salt = 6962172143427679; // this was used to create hash. if lost, then new values should be created
        $hash = '$2y$13$kN7N9wAm/2mXZ4ydnoAtqOzeJJXT09/al1iIJKmzhyy6g4pazx7Gu';

        $begTblRowCount = $this->getConnection()->getRowCount('EmailKey');

        $guidMock = $this->createMock(Guid::class);
        $guidMock->method('value')
            ->willReturn('56307056-da07-468f-9c04-df3d95ce54a9');

        $emailAddressMock = $this->createMock(EmailAddress::class);
        $emailAddressMock->method('getEmailAddress')
            ->willReturn('test@papalocal.com');

        $purposeMock = $this->createMock(EmailSaltPurpose::class);
        $purposeMock->method('getValue')
            ->willReturn(EmailSaltPurpose::PURPOSE_FORGOT_PASS()->getValue());

        $emailSaltMock = $this->createMock(EmailSalt::class);
        $emailSaltMock->method('getId')
            ->willReturn($guidMock);
        $emailSaltMock->method('getHash')
            ->willReturn($hash);
        $emailSaltMock->method('getEmailAddress')
            ->willReturn($emailAddressMock);
        $emailSaltMock->method('getPurpose')
            ->willReturn($purposeMock);
        $emailSaltMock->method('getExpirationPolicy')
            ->willReturn(new \DateInterval('PT30M'));

        // exercise SUT
        $this->emailSaltRepository->save($emailSaltMock);

        // make assertions
        $this->assertTableRowCount('EmailKey', $begTblRowCount + 1, 'unexpected table row count');
    }

    public function testFindByGuidIsSuccess()
    {
        // set up fixtures
        $guid = $this->getConnection()
            ->createQueryTable('email_salt_guid', 'SELECT guid FROM EmailKey LIMIT 1')
            ->getRow(0)['guid'];

        $guidMock = $this->createMock(Guid::class);
        $guidMock->method('value')
            ->willReturn($guid);

        // exercise SUT
        $emailSalt = $this->emailSaltRepository->findByGuid($guidMock);

        // make assertions
        $this->assertInstanceOf(\PapaLocal\Core\Security\Entity\EmailSalt::class, $emailSalt, 'unexpected type');
    }
}
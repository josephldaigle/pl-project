<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/6/18
 */


namespace PapaLocal\IdentityAccess\Data\Repository;


use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Core\Factory\VOFactory;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Company\CreateCompanyAddress;
use PapaLocal\Data\Command\Company\CreateCompanyEmail;
use PapaLocal\Data\Command\Company\UpdateAddress;
use PapaLocal\Data\Command\Company\UpdateEmailAddress;
use PapaLocal\Data\Hydrate\Company\CompanyHydrator;
use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Company;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Entity\Exception\CompanyNameExistsException;
use PapaLocal\Entity\Exception\ServiceOperationFailedException;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;
use PapaLocal\ValueObject\ContactProfile;


/**
 * Class CompanyRepository
 *
 * @package PapaLocal\IdentityAccess\Data\Repository
 */
class CompanyRepository extends AbstractRepository
{
    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var MessageFactory
     */
    private $mysqlMsgFactory;

    /**
     * @var CompanyHydrator
     */
    private $companyHydrator;

    /**
     * @var VOFactory
     */
    private $voFactory;

    /**
     * @var GuidGeneratorInterface
     */
    private $guidGenerator;

    /**
     * CompanyRepository constructor.
     *
     * @param DataResourcePool $dataResourcePool
     * @param MessageBusInterface $mysqlBus
     * @param MessageFactory $mysqlMsgFactory
     * @param CompanyHydrator $companyHydrator
     * @param VOFactory $voFactory
     * @param GuidGeneratorInterface $guidGenerator
     */
    public function __construct(
        DataResourcePool $dataResourcePool,
        MessageBusInterface $mysqlBus,
        MessageFactory $mysqlMsgFactory,
        CompanyHydrator $companyHydrator,
        VOFactory $voFactory,
        GuidGeneratorInterface $guidGenerator
    )
    {
        parent::__construct($dataResourcePool);

        $this->mysqlBus        = $mysqlBus;
        $this->mysqlMsgFactory = $mysqlMsgFactory;
        $this->companyHydrator = $companyHydrator;
        $this->voFactory       = $voFactory;
        $this->guidGenerator = $guidGenerator;
    }

    /**
     * Create a new company and assign it to a user.
     *
     * @param GuidInterface $ownerGuid
     * @param Company       $company
     * @param Address       $address
     * @param PhoneNumber   $phoneNumber
     * @param EmailAddress  $emailAddress
     *
     * @return bool
     * @throws \Exception
     */
    public function createCompany(
        GuidInterface $ownerGuid,
        Company $company,
        Address $address = null,
        PhoneNumber $phoneNumber = null,
        EmailAddress $emailAddress = null
    )
    {
        $this->mysqlBus->dispatch($this->mysqlMsgFactory->newStartTransaction());

        try {
            $companyGuid = $this->guidGenerator->generate();
            $company->setGuid($companyGuid);

            $saveCompanyCmd = $this->mysqlMsgFactory->newSaveCompany($ownerGuid, $company);
            $this->mysqlBus->dispatch($saveCompanyCmd);

            if ( ! is_null($address)) {
                $this->saveAddress($companyGuid, $address);
            }

            if ( ! is_null($phoneNumber)) {
                $this->savePhoneNumber($companyGuid, $phoneNumber);
            }

            if ( ! is_null($emailAddress)) {
                $this->saveEmailAddress($companyGuid, $emailAddress);
            }

            $this->mysqlBus->dispatch($this->mysqlMsgFactory->newCommitTransaction());

            return true;

        } catch (\Exception $exception) {
            $this->mysqlBus->dispatch($this->mysqlMsgFactory->newRollbackTransaction());

            throw $exception;
        }
    }

    /**
     * @deprecated - this happens when the company is created
     *
     * Assign an owner to the company.
     *
     * @param int $userId
     * @param int $companyId
     *
     * @return bool
     * @throws ServiceOperationFailedException
     * @throws \LogicException
     */
    public function assignOwner(int $userId, int $companyId)
    {
        // check that company exists
        $this->tableGateway->setTable('Company');
        $companyRows = $this->tableGateway->findById($companyId);

        if (count($companyRows) < 1) {
            throw new \InvalidArgumentException(sprintf('Unable to find company with id %s', $companyId));
        }

        // check that company has an owner
        $this->tableGateway->setTable('v_company_owner');
        $ownerRows = $this->tableGateway->findBy('id', $companyId);

        if (count($ownerRows) > 0) {
            throw new \InvalidArgumentException(sprintf('Company %s already has an owner assigned.', $companyId));
        }

        // no owner found, assign user as company owner
        $this->tableGateway->setTable('L_UserRole');
        $role = $this->tableGateway->findBy('name', AttrType::SECURITY_ROLE_COMPANY);
        if (count($role) !== 1) {
            throw new ServiceOperationFailedException(sprintf('Unable to locate security role %s.',
                AttrType::SECURITY_ROLE_COMPANY));
        }

        $this->tableGateway->setTable('R_UserCompanyRole');

        $this->tableGateway->create(array(
            'userId'    => $userId,
            'companyId' => $companyId,
            'roleId'    => $role[0]['id'],
        ));

        return true;

    }

    /**
     * Update a company's name.
     *
     * @param GuidInterface $companyGuid
     * @param string        $name
     */
    public function saveCompanyName(GuidInterface $companyGuid, string $name)
    {
        $this->mysqlBus->dispatch($this->mysqlMsgFactory->newUpdateCompanyName($companyGuid, $name));

        return;
    }

    /**
     * @param GuidInterface $companyGuid
     * @param PhoneNumber   $phoneNumber
     *
     * @throws \Exception
     */
    public function savePhoneNumber(GuidInterface $companyGuid, PhoneNumber $phoneNumber)
    {
        $phoneNumber = $this->voFactory->createPhoneNumber($phoneNumber->getPhoneNumber(), PhoneNumberType::{strtoupper($phoneNumber->getType())}());
        $this->mysqlBus->dispatch($this->mysqlMsgFactory->newUpdateCompanyPhoneNumber($companyGuid, $phoneNumber));
        return;
    }

    /**
     * @param GuidInterface $companyGuid
     * @param EmailAddress  $emailAddress
     */
    public function saveEmailAddress(GuidInterface $companyGuid, EmailAddress $emailAddress)
    {
        $this->mysqlBus->dispatch($this->mysqlMsgFactory->newUpdateCompanyEmailAddress($companyGuid, $emailAddress));
        return;
    }


    /**
     * @param GuidInterface $companyGuid
     * @param Address       $address
     */
    public function saveAddress(GuidInterface $companyGuid, Address $address)
    {
        $this->mysqlBus->dispatch($this->mysqlMsgFactory->newUpdateCompanyAddress($companyGuid, $address));
        return;
    }

    /**
     * Save a company's founding date.
     *
     * @param int $companyId
     * @param int $dateFounded
     *
     * @return int
     * @throws ServiceOperationFailedException
     */
    public function saveDateFounded(int $companyId, int $dateFounded)
    {
        $this->tableGateway->setTable('Company');
        $coRows = $this->tableGateway->findById($companyId);

        if (count($coRows) < 1) {
            throw new ServiceOperationFailedException(sprintf('Could not find company with id: %s', $companyId));
        }

        $coRows[0]['dateFounded'] = $dateFounded;

        return $this->tableGateway->update($coRows[0]);
    }

    /**
     * Save a company's website.
     *
     * @param int    $companyId
     * @param string $website
     *
     * @return int
     * @throws ServiceOperationFailedException
     */
    public function saveWebsite(int $companyId, string $website)
    {
        $this->tableGateway->setTable('Company');
        $coRows = $this->tableGateway->findById($companyId);

        if (count($coRows) < 1) {
            throw new ServiceOperationFailedException(sprintf('Could not find company with id: %s', $companyId));
        }

        $coRows[0]['website'] = $website;

        return $this->tableGateway->update($coRows[0]);
    }

    /**
     * Delete a company's website.
     *
     * @param int $companyId
     *
     * @return int
     * @throws ServiceOperationFailedException
     */
    public function deleteWebsite(int $companyId)
    {
        $this->tableGateway->setTable('Company');
        $coRows = $this->tableGateway->findById($companyId);

        if (count($coRows) < 1) {
            throw new ServiceOperationFailedException(sprintf('Could not find company with id: %s', $companyId));
        }

        $coRows[0]['website'] = '';

        return $this->tableGateway->update($coRows[0]);

    }

    /**
     * Save a company's description.
     *
     * @param int    $companyId
     * @param string $description
     *
     * @return int
     * @throws ServiceOperationFailedException
     */
    public function saveDescription(int $companyId, string $description)
    {
        $this->tableGateway->setTable('Company');
        $coRows = $this->tableGateway->findById($companyId);

        if (count($coRows) < 1) {
            throw new ServiceOperationFailedException(sprintf('Could not find company with id: %s', $companyId));
        }

        $coRows[0]['about'] = $description;

        return $this->tableGateway->update($coRows[0]);
    }

    /**
     * Save a company's status.
     *
     * @param int    $companyId
     * @param string $status
     *
     * @return int
     * @throws ServiceOperationFailedException
     */
    public function saveStatus(int $companyId, string $status)
    {
        $this->tableGateway->setTable('L_CompanyStatus');
        $statusRows = $this->tableGateway->findBy('name', $status);

        if (count($statusRows) !== 1) {
            throw new ServiceOperationFailedException(
                sprintf('Unexpected result count when querying for company status: %s, num rows: %s',
                    $status, count($statusRows)));
        }

        $this->tableGateway->setTable('Company');
        $coRows = $this->tableGateway->findById($companyId);

        if (count($coRows) < 1) {
            throw new ServiceOperationFailedException(sprintf('Could not find company with id: %s', $companyId));
        }

        $coRows[0]['statusId'] = $statusRows[0]['id'];

        return $this->tableGateway->update($coRows[0]);
    }

    /**
     * Find a company by id.
     *
     * @param int $companyId
     *
     * @return mixed
     * @throws ServiceOperationFailedException
     */
    public function loadCompany(int $companyId)
    {
        $this->tableGateway->setTable('Company');
        $companyRow = $this->tableGateway->findById($companyId);

        if (count($companyRow) < 1) {
            throw new ServiceOperationFailedException(sprintf('Could not find company with id:  %s', $companyId));
        }

        $company = $this->serializer->denormalize($companyRow[0], Company::class, 'array');

        return $company;
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return mixed
     */
    public function findByUserGuid(GuidInterface $userGuid)
    {
        $companyQry    = $this->mysqlMsgFactory->newFindCompanyByUserGuid($userGuid->value());
        $companyRecord = $this->mysqlBus->dispatch($companyQry);

        $company = $this->serializer->denormalize(
            array(
                'guid'        => $this->serializer->denormalize(array('value' => $companyRecord['companyGuid']),
                    Guid::class, 'array'),
                'name'        => $companyRecord['name'],
                'dateFounded' => (isset($companyRecord['dateFounded']) && ! empty($companyRecord['dateFounded'])) ? $companyRecord['dateFounded'] : '',
                'ownerGuid'   => $this->serializer->denormalize(array('value' => $companyRecord['ownerGuid']),
                    Guid::class, 'array'),
            ), Company::class, 'array');

        // load company email addresses
        $emailQry  = $this->mysqlMsgFactory->newFindBy('v_company_email_address', 'companyId', $companyRecord['id']);
        $emailRecs = $this->mysqlBus->dispatch($emailQry);

        $emailList = new Collection();
        foreach ($emailRecs as $record) {
            $emailAddress = $this->voFactory->createEmailAddress($record['emailAddress'],
                EmailAddressType::{strtoupper($record['type'])}());

            $emailList->add($emailAddress);
        }


        // load company addresses
        $addressQry  = $this->mysqlMsgFactory->newFindBy('v_company_address', 'companyId', $companyRecord['id']);
        $addressRecs = $this->mysqlBus->dispatch($addressQry);

        $addressList = new Collection();

        foreach ($addressRecs as $record) {
            $address = $this->serializer->denormalize([
                'streetAddress' => $record['streetAddress'],
                'city'          => $record['city'],
                'state'         => $record['state'],
                'postalCode'    => $record['postalCode'],
                'country'       => $record['country'],
                'type'          => array('value' => $record['type']),
            ], Address::class, 'array');

            $addressList->add($address);
        }

        // load company phone numbers
        $phoneQry  = $this->mysqlMsgFactory->newFindBy('v_company_phone_number', 'companyId', $companyRecord['id']);
        $phoneRecs = $this->mysqlBus->dispatch($phoneQry);

        $phoneList = new Collection();
        foreach ($phoneRecs as $record) {
            $phoneNumber = $this->voFactory->createPhoneNumber($record['phoneNumber'],
                PhoneNumberType::{strtoupper($record['type'])}());

            $phoneList->add($phoneNumber);
        }

        $contactProfile = new ContactProfile($emailList, $addressList, $phoneList);

        $company->setContactProfile($contactProfile);

        return $company;
    }
}
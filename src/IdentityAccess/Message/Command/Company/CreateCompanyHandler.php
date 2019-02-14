<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/16/18
 * Time: 9:16 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\Company;


use PapaLocal\Core\Factory\VOFactory;
use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\AddressType;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Entity\Company;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use PapaLocal\IdentityAccess\Data\Repository\CompanyRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class CreateCompanyHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class CreateCompanyHandler
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var GuidGeneratorInterface
     */
    private $guidFactory;

    /**
     * @var VOFactory
     */
    private $voFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * CreateCompanyHandler constructor.
     *
     * @param MessageFactory         $messageFactory
     * @param MessageBusInterface    $mysqlBus
     * @param GuidGeneratorInterface $guidFactory
     * @param VOFactory              $voFactory
     * @param SerializerInterface    $serializer
     * @param CompanyRepository      $companyRepository
     */
    public function __construct(MessageFactory $messageFactory, MessageBusInterface $mysqlBus, GuidGeneratorInterface $guidFactory, VOFactory $voFactory, SerializerInterface $serializer, CompanyRepository $companyRepository)
    {
        $this->messageFactory = $messageFactory;
        $this->mysqlBus = $mysqlBus;
        $this->guidFactory = $guidFactory;
        $this->voFactory = $voFactory;
        $this->serializer = $serializer;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @inheritdoc
     * @param CreateCompany $command
     *
     * @throws \Exception
     */
    public function __invoke(CreateCompany $command)
    {
        // assemble objects
        $guid = $this->guidFactory->generate();
        
        $company = $this->serializer->denormalize(array(
            'guid' => $guid,
            'name' => $command->getName()
        ), Company::class, 'array');

        $address = $this->serializer->denormalize($command->getAddress(), Address::class, 'array');
        $address->setType(AddressType::PHYSICAL());

        $emailAddress = $this->voFactory->createEmailAddress($command->getEmailAddress(), EmailAddressType::BUSINESS());

        $phoneNumber = $this->voFactory->createPhoneNumber($command->getPhoneNumber(), PhoneNumberType::BUSINESS());

        // save the company
        $this->companyRepository->createCompany($command->getOwnerUserGuid(), $company, $address,
            $phoneNumber, $emailAddress);
    }
}
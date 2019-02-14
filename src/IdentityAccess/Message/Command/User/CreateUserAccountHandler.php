<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/22/18
 * Time: 11:19 AM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


use PapaLocal\Core\Factory\VOFactory;
use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\AddressType;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Entity\Person;
use PapaLocal\IdentityAccess\Entity\UserAccount;
use PapaLocal\IdentityAccess\Service\UserService;
use PapaLocal\IdentityAccess\ValueObject\SecurityRole;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class CreateUserAccountHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class CreateUserAccountHandler
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var GuidGeneratorInterface
     */
    private $guidGenerator;

    /**
     * @var VOFactory
     */
    private $voFactory;

    /**
     * CreateUserAccountHandler constructor.
     *
     * @param UserService            $userService
     * @param SerializerInterface    $serializer
     * @param GuidGeneratorInterface $guidGenerator
     * @param VOFactory              $voFactory
     */
    public function __construct(
        UserService $userService,
        SerializerInterface $serializer,
        GuidGeneratorInterface $guidGenerator,
        VOFactory $voFactory
    )
    {
        $this->userService   = $userService;
        $this->serializer    = $serializer;
        $this->guidGenerator = $guidGenerator;
        $this->voFactory     = $voFactory;
    }

    /**
     * @param CreateUserAccount $command
     */
    public function __invoke(CreateUserAccount $command)
    {
        // create user object
        $user = $this->serializer->denormalize(array(
            'guid' => $this->guidGenerator->generate(),
            'username' => $command->getUsername(),
            'password' => $command->getPassword(), // unencoded
            'notificationSavePoint' => 0,
            'isActive' => true
        ), User::class, 'array');

        // create person object
        $person = $this->serializer->denormalize(array(
            'guid' => array('value' => $this->guidGenerator->generate()->value()),
            'firstName' => $command->getFirstName(),
            'lastName' => $command->getLastName(),
        ), Person::class, 'array');

        $user->setPerson($person);
        $user->setRoles(array(
            SecurityRole::ROLE_USER()
        ));

        $userPhone = $this->voFactory->createPhoneNumber($command->getPhoneNumber(), PhoneNumberType::MAIN());

        // create company object
        $company = null;

        if (! empty($command->getCompanyName())) {
            $company = $this->serializer->denormalize(array(
                'guid' => $this->guidGenerator->generate(),
                'name' => $command->getCompanyName(),
                'emailAddress' => $this->voFactory->createEmailAddress($command->getCompanyEmailAddress(), EmailAddressType::BUSINESS()),
                'phoneNumber' => $this->voFactory->createPhoneNumber($command->getCompanyPhoneNumber(), PhoneNumberType::BUSINESS()),
                'address' => $this->serializer->denormalize([
                    'streetAddress' => $command->getCompanyAddress()['streetAddress'],
                    'city' => $command->getCompanyAddress()['city'],
                    'state' => $command->getCompanyAddress()['state'],
                    'postalCode' => $command->getCompanyAddress()['postalCode'],
                    'country' => $command->getCompanyAddress()['country'],
                    'type' => ['value' => AddressType::PHYSICAL()->getValue()],
                ], Address::class, 'array')
            ), Company::class, 'array');
        }

        // create user account object
        $userAccount = $this->serializer->denormalize(array(
                'user' => $user,
                'person' => $person,
                'company' => $company,
                'phoneNumber' => $userPhone
            ), UserAccount::class, 'array');

        // invoke service
        $this->userService->createUserAccount($userAccount);

        return;
    }

}
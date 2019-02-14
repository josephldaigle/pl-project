<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/21/18
 */


namespace PapaLocal\IdentityAccess\Entity\Factory;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Entity\Person;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class UserFactory.
 *
 * @package PapaLocal\IdentityAccess\Entity\Factory
 */
class UserFactory
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * UserFactory constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param RecordInterface $userRecord
     *
     * @return User
     */
    public function createFromRecord(RecordInterface $userRecord): User
    {
        $user = $this->serializer->denormalize(array(
            'id' => $userRecord['userId'],
            'guid' => new Guid($userRecord['userGuid']),
            'username' => $userRecord['username'],
            'password' => $userRecord['password'],
            'timeCreated' => $userRecord['userTimeCreated'],
            'notificationSavePoint' => $userRecord['notificationSavePoint'],
            'isActive' => (bool) $userRecord['isActive']
        ), User::class, 'array');

        //set person and username on user object
        $person = $this->serializer->denormalize(array(
            'id' => $userRecord['personId'],
            'guid' => array('value' => $userRecord['personGuid']),
            'firstName' => $userRecord['firstName'],
            'lastName' => $userRecord['lastName'],
            'about' => (isset($userRecord['about']) && ! is_null($userRecord['about'])) ? $userRecord['about'] : ''
        ), Person::class, 'array');

        $user->setPerson($person);

        return $user;
    }
}
<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 7/15/18
 * Time: 7:10 PM
 */

namespace PapaLocal\Data\Repository\Person;


use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\Person;
use PapaLocal\Entity\PhoneNumber;
use PapaLocal\Entity\Registry\PersonRegistry;
use PapaLocal\ValueObject\ContactProfile;


/**
 * Class PersonRepository
 *
 * @package PapaLocal\Data\Repository\Person
 */
class PersonRepository extends AbstractRepository
{
    /**
     * @return mixed
     */
	public function loadAll()
	{
		$qb = $this->tableGateway->connection->createQueryBuilder();

		$emailRows = $qb->select('p.id', 'p.firstName', 'p.lastName', 'vpea.emailId', 'vpea.emailAddress', 'vpea.type')
		                ->from('Person', 'p')
		                ->leftJoin('p', 'v_person_email_address', 'vpea', 'p.id = vpea.personId')
		                ->execute()
		                ->fetchAll();

		$qb = $this->tableGateway->connection->createQueryBuilder();

		$phoneRows = $qb->select('*')
		                ->from('v_person_phone')
		                ->execute()
		                ->fetchAll();

        $collection = $this->serializer->denormalize(array(), Collection::class, 'array');

		$personList = $collection;

		// add email addresses to object
		foreach ($emailRows as $row) {
			$emailAddress = $this->serializer->denormalize(array(
				'id'           => $row['emailId'],
				'emailAddress' => $row['emailAddress'],
				'type'         => $row['type']
			), EmailAddress::class, 'array');

			if (is_null($personList->findBy('id', $row['id']))) {
				// person is not in list
				$person         = $this->serializer->denormalize($row, Person::class, 'array');
				$contactProfile = $this->serializer->denormalize(array(
					'emailList'       => $collection,
					'phoneNumberList' => $collection,
					'addressList'     => $collection
				), ContactProfile::class, 'array');

				$contactProfile->addEmailAddress($emailAddress, $emailAddress->getType());
				$person->setContactProfile($contactProfile);
				$personList->add($person);
			} else {
				// person found in list
				$person = $personList->findBy('id', $row['id']);
				$person->getContactProfile()->addEmailAddress($emailAddress, $emailAddress->getType());
			}
		}


		// add phone numbers to object
		foreach ($phoneRows as $row) {
			$phoneNumber = $this->serializer->denormalize(array(
				'id'          => $row['phoneId'],
				'phoneNumber' => $row['phoneNumber'],
				'type'        => $row['type']
			), PhoneNumber::class, 'array');

			if (is_null($personList->findBy('id', $row['personId']))) {
				// person is not in list
				$person         = $this->serializer->denormalize(array(
					'id'        => $row['personId'],
					'firstName' => $row['firstName'],
					'lastName'  => $row['lastName']
				), Person::class, 'array');
				$contactProfile = $this->serializer->denormalize(array(
					'emailList'       => $collection,
					'phoneNumberList' => $collection,
					'addressList'     => $collection
				), ContactProfile::class, 'array');
				$contactProfile->addPhoneNumber($phoneNumber, $phoneNumber->getType());
				$person->setContactProfile($contactProfile);
				$personList->add($person);
			} else {
				// person found in list
				$person = $personList->findBy('id', $row['personId']);
				$person->getContactProfile()->addPhoneNumber($phoneNumber, $phoneNumber->getType());
			}
		}

		$registry = $this->serializer->denormalize(array('personList' => $personList), PersonRegistry::class, 'array');
		return $registry;
	}

	/**
	 * Check whether a person exists in the database.
	 *
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $emailAddress
	 *
	 * @return bool
	 */
	public function personExists(string $firstName, string $lastName, string $emailAddress)
	{
		$this->tableGateway->setTable('v_person_email_address');
		$rows = $this->tableGateway->findByColumns(array(
			'firstName' => $firstName,
			'lastName' => $lastName,
			'emailAddress' => $emailAddress
		));

		return (count($rows) > 0);

	}
}
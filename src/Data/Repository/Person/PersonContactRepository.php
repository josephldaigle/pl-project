<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/3/18
 * Time: 7:33 PM
 */


namespace PapaLocal\Data\Repository\Person;


use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Test\Unit\Core\Enum\EmailAddressType;


/**
 * Class PersonContactRepository
 *
 * @package PapaLocal\Data\Repository\Person
 */
class PersonContactRepository extends AbstractRepository
{
	/**
	 * @param int    $personId
	 * @param string $emailAddress
	 *
	 * @return bool
	 */
	public function hasEmailAddress(int $personId, string $emailAddress)
	{
		$this->tableGateway->setTable('v_person_email_address');
		$rows = $this->tableGateway->findByColumns(array('personId' => $personId, 'emailAddress' => $emailAddress));

		return count($rows) > 0;
	}

	public function fetchEmailAddressesFor(int $personId)
	{
		$this->tableGateway->setTable('v_person_email_address');
		$rows = $this->tableGateway->findBy('personId', $personId);

		$list = $this->serializer->denormalize(array(), Collection::class, 'array');

		foreach ($rows as $row) {
			$emailAddress = $this->serializer->denormalize(array(
				'id' => $row['emailId'],
				'emailAddress' => $row['emailAddress'],
				'type' => $row['emailType']
				), EmailAddress::class, 'array');

			$list->add($emailAddress);
		}

		return $list;
	}
}
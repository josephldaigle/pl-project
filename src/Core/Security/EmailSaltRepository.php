<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/9/18
 */

namespace PapaLocal\Core\Security;


use PapaLocal\Core\Security\Entity\EmailSalt as EmailSaltEntity;
use PapaLocal\Core\Security\ValueObject\EmailSalt;
use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Exception\Data\NotFoundException;
use PapaLocal\Entity\Exception\QueryException;


/**
 * Class EmailSaltRepository
 *
 * Provide data access for email salts, used to identify inbound email urls.
 *
 * @package PapaLocal\Core\Security
 */
class EmailSaltRepository extends AbstractRepository
{
    /**
     * Persist a new email salt record.
     *
     * @param EmailSalt $emailSalt
     * @throws QueryException
     */
    public function save(EmailSalt $emailSalt)
    {
        try {
            $this->tableGateway->setTable('EmailKey');
            $this->tableGateway->create(array(
                'guid' => $emailSalt->getId()->value(),
                'emailAddress' => $emailSalt->getEmailAddress()->getEmailAddress(),
                'hash' => $emailSalt->getHash(),
                'purpose' => $emailSalt->getPurpose()->getValue(),
                'expirationPolicy' => serialize($emailSalt->getExpirationPolicy())
            ));

            return;

        } catch (\Exception $exception) {
            throw new QueryException(sprintf('Unable to create email key: %s', $exception->getMessage()), null, $exception);
        }
    }

    /**
     * Delete an email salt record.
     *
     * @param GuidInterface $guid
     */
    public function deleteSalt(GuidInterface $guid)
    {
        $this->tableGateway->setTable('EmailKey');
        $rows = $this->tableGateway->findByGuid($guid->value());
        $this->tableGateway->delete($rows[0]['id']);
        return;
    }

    /**
     * @param GuidInterface $guid
     *
     * @return EmailSaltEntity
     * @throws NotFoundException
     */
    public function findByGuid(GuidInterface $guid): \PapaLocal\Core\Security\Entity\EmailSalt
    {
        $this->tableGateway->setTable('EmailKey');
        $emailKeyRows = $this->tableGateway->findByGuid($guid->value());

        if (count($emailKeyRows) < 1) {
            throw new NotFoundException(sprintf('Unable to find EmailKey with guid %s', $guid->value()));
        }

        $emailSalt = $this->serializer->denormalize(array(
            'id' => array('value' => $emailKeyRows[0]['guid']),
            'emailAddress' => array('emailAddress' => $emailKeyRows[0]['emailAddress'], 'type' => EmailAddressType::PERSONAL()),
            'hash' => $emailKeyRows[0]['hash'],
            'purpose' => array('value' => $emailKeyRows[0]['purpose']),
            'timeCreate' => $emailKeyRows[0]['timeCreated'],
            'expirationPolicy' => unserialize($emailKeyRows[0]['expirationPolicy'])
            ), \PapaLocal\Core\Security\Entity\EmailSalt::class, 'array');

        return $emailSalt;
    }

    /**
     * TODO: Handle multiple results
     * @param EmailAddress $emailAddress
     *
     * @return EmailSaltEntity
     * @throws NotFoundException
     */
    public function findByEmailAddress(EmailAddress $emailAddress): \PapaLocal\Core\Security\Entity\EmailSalt
    {
        $this->tableGateway->setTable('EmailKey');
        $rows = $this->tableGateway->findBy('emailAddress', $emailAddress->getEmailAddress());

        if (count($rows) < 1) {
            throw new NotFoundException(sprintf('Unable to find EmailKey with email address %s', $emailAddress->getEmailAddress()));
        }

        $emailSalt = $this->serializer->denormalize(array(
            'id' => array('value' => $rows[0]['guid']),
            'emailAddress' => array('emailAddress' => $rows[0]['emailAddress'], 'type' => EmailAddressType::PERSONAL()),
            'hash' => $rows['hash'],
            'purpose' => array('value' => $rows[0]['purpose']),
            'timeCreate' => $rows[0]['timeCreated'],
            'expirationPolicy' => unserialize($rows[0]['expirationPolicy'])
        ), \PapaLocal\Core\Security\Entity\EmailSalt::class, 'array');

        return $emailSalt;
    }

    /**
     * TODO: Handle multiple results
     *
     * @param array $params
     *
     * @return EmailSaltEntity
     * @throws NotFoundException
     */
    public function findBy(array $params): \PapaLocal\Core\Security\Entity\EmailSalt
    {
        $this->tableGateway->setTable('EmailKey');
        $rows = $this->tableGateway->findByColumns($params);

        if (count($rows) < 1) {
            throw new NotFoundException('Unable to find EmailKey with provided parameters');
        }

        $emailSalt = $this->serializer->denormalize(array(
            'id' => $this->serializer->denormalize(array('value' => $rows[0]['guid']), Guid::class, 'array'),
            'emailAddress' => array('emailAddress' => $rows[0]['emailAddress'], 'type' => EmailAddressType::PERSONAL()),
            'hash' => $rows[0]['hash'],
            'purpose' => $this->serializer->denormalize(array('value' => $rows[0]['purpose']), EmailSaltPurpose::class, 'array'),
            'timeCreated' => $rows[0]['timeCreated'],
            'expirationPolicy' => unserialize($rows[0]['expirationPolicy'])
        ), \PapaLocal\Core\Security\Entity\EmailSalt::class, 'array');

        return $emailSalt;
    }

    /**
     * Check whether or not an EmailSalt exists in the database.
     *
     * @param EmailSalt $emailSalt
     * @return bool
     * @throws QueryException
     */
    public function saltExists(EmailSalt $emailSalt)
    {
//        try {
//            $this->tableGateway->setTable('v_person_email_key');
//            $rows = $this->tableGateway->findByColumns(array(
//                'personId' => $emailSalt->getPersonId(),
//                'emailId' => $emailSalt->getEmailId(),
//                'purpose' => $emailSalt->getPurpose()
//            ));
//
//            return (count($rows) > 0);
//
//        } catch (\Exception $exception) {
//            throw new QueryException(sprintf('An error occurred while searching for an EmailSalt: %s', $exception->getMessage()), null, $exception);
//        }
    }

    /**
	 * Find and load a salt by it's id.
	 *
	 * @param int $saltId
	 *
	 * @return null
	 */
	public function loadSaltById(int $saltId)
	{
//		$this->tableGateway->setTable('v_person_email_key');
//		$rows = $this->tableGateway->findById($saltId);
//
//		return (count($rows) < 1) ? null :
//			$this->serializer->denormalize($rows[0], EmailSaltEntity::class, 'array');
    }
}
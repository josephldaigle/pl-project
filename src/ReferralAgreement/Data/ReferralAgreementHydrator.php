<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/13/18
 * Time: 6:52 AM
 */


namespace PapaLocal\ReferralAgreement\Data;


use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\ReferralAgreement\ValueObject\Service;
use PapaLocal\Core\Data\AbstractHydrator;
use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\Entity\Entity;
use PapaLocal\Entity\EntityFactory;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\ValueObject\Location;
use PapaLocal\Entity\Person;
use PapaLocal\Entity\User;
use PapaLocal\Core\Exception\InvalidStateException;
use PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use PapaLocal\ReferralAgreement\ValueObject\StatusHistory;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class ReferralAgreementHydrator.
 *
 * @package PapaLocal\ReferralAgreement
 */
class ReferralAgreementHydrator extends AbstractHydrator
{
	/**
	 * @var
	 */
	private $agreement;

    /**
     * @var string
     */
	private $agreementId;

	/**
	 * @var bool
	 */
	private $setIdWasCalled = false;

    /**
     * @var array
     */
	private $methods = [];

	/**
	 * ReferralAgreementHydrator constructor.
	 *
	 * @param TableGateway        $tableGateway
	 * @param EntityFactory       $entityFactory
	 * @param SerializerInterface $serializer
	 */
	public function __construct(TableGateway $tableGateway,
		EntityFactory $entityFactory,
		SerializerInterface $serializer)
    {
        parent::__construct($tableGateway, $entityFactory, $serializer);
    }

	/**
     * @param string $guid
     *
     * @return ReferralAgreementHydrator
     */
	public function setId(string $guid): ReferralAgreementHydrator
	{
//		$this->agreement->setGuid(new Guid($guid));
		$this->agreementId = $guid;

		$this->setIdWasCalled = true;

		return $this;
	}

    /**
     * Load agreement basic detail.
     *
     * @return ReferralAgreement
     * @throws AgreementNotFoundException
     * @throws InvalidStateException
     */
	protected function loadHeader(): ReferralAgreement
	{
		$this->checkState();

		// query for agreements
		$this->tableGateway->setTable('ReferralAgreement');
		$rows = $this->tableGateway->findByGuid($this->agreementId);

		if (count($rows) < 1) {
			throw new AgreementNotFoundException(sprintf('Unable to find a referral agreement with guid: %s', $this->agreementId));
		}

		// configure agreement
        $this->agreement = $this->serializer->denormalize(array(
            'guid' => array('value' => $rows[0]['guid']),
            'companyId' => $rows[0]['companyId'],
            'name' => $rows[0]['name'],
            'description' => $rows[0]['description'],
            'quantity' => $rows[0]['quantity'],
            'bid' => $rows[0]['bid'],
            'strategy' => $rows[0]['strategy'],
            'owner' => $this->loadOwner($rows[0]['ownerId'])
        ), ReferralAgreement::class, 'array');

        $this->agreement->setTimeCreated($rows[0]['timeCreated']);
        $this->loadStatusHistory();

        foreach ($this->methods as $method) {
            call_user_func(__CLASS__ . '::' . $method);
        }

        $this->agreement->setNumberInvitees($this->countInvitees($this->agreementId));
        $this->agreement->setNumberParticipants($this->countParticipants($this->agreementId));

		return $this->agreement;
	}


    /**
     * Load agreement locations with type = 'include'.
     *
     * @return ReferralAgreementHydrator
     * @throws InvalidStateException
     */
	public function loadIncludedLocations(): ReferralAgreementHydrator
	{
        $this->methods[] = 'includedLocations';
        return $this;
	}

    /**
     * Load agreement locations with type = 'exclude'.
     *
     * @return ReferralAgreementHydrator
     * @throws InvalidStateException
     */
	public function loadExcludedLocations(): ReferralAgreementHydrator
	{
		$this->methods[] = 'excludedLocations';

		return $this;
	}

    /**
     * Load agreement services with type = 'include'.
     *
     * @return ReferralAgreementHydrator
     * @throws InvalidStateException
     */
	public function loadIncludedServices(): ReferralAgreementHydrator
    {
	    $this->methods[] = 'includedServices';

	    return $this;
    }

    /**
     * Load agreement services with type = 'exclude'.
     *
     * @return ReferralAgreementHydrator
     * @throws InvalidStateException
     */
    public function loadExcludedServices(): ReferralAgreementHydrator
    {
	    $this->methods[] = 'excludedServices';

	    return $this;
    }

    /**
     * Load the invitee list.
     *
     * @return ReferralAgreementHydrator
     * @throws InvalidStateException
     */
	public function loadInviteeList(): ReferralAgreementHydrator
	{
        $this->methods[] = 'inviteeList';
		return $this;
	}

    /**
     * Load the agreement owner (not a complete object).
     *
     * @param int $userId
     * @return mixed
     * @throws InvalidStateException
     */
    protected function loadOwner(int $userId)
    {
        $this->checkState();

        $this->tableGateway->setTable('v_user');
        $rows = $this->tableGateway->findBy('userId', $userId);

        if (count ($rows) > 0) {
            $user = $this->serializer->denormalize(array(
                'id' => $rows[0]['userId'],
                'guid' => $this->serializer->denormalize(array('value' => $rows[0]['userGuid']), Guid::class, 'array'),
                'username' => $rows[0]['username'],
                'timeZone' => $rows[0]['timeZone'],
            ), User::class, 'array');

            $person = $this->serializer->denormalize($rows[0], Person::class, 'array');

        }

        return array('user' => $user, 'person' => $person);
    }

    /**
     * Load the status history.
     *
     * @return ReferralAgreementHydrator
     */
    public function loadStatusHistory(): ReferralAgreementHydrator
    {
        $this->methods[] = 'statusHistory';

        return $this;
    }

    /**
     * @return Entity
     * @throws AgreementNotFoundException
     * @throws InvalidStateException
     */
	public function hydrate(): Entity
	{
		$this->loadHeader();
		$this->setIdWasCalled = false;

		return $this->agreement;
	}

    protected function includedLocations()
    {
        $this->checkState();

        $includedLocations = $this->serializer->denormalize(array(), Collection::class, 'array');

        $this->tableGateway->setTable('ReferralAgreementLocation');
        $rows = $this->tableGateway->findByColumns(array(
            'guid' => $this->agreement->getGuid()->value(),
            'type' => 'include'
        ));

        foreach ($rows as $row) {
            $location = $this->serializer->denormalize(array(
                'agreementId' => array('value' => $row['agreementId']),
                'location' => $row['location'],
                'type' => array('value' => $row['type']
                )), Location::class, 'array');
            $includedLocations->add($location);
        }

        $this->agreement->setIncludedLocations($includedLocations);

        return $this;
    }

    protected function excludedLocations()
    {
        $this->checkState();

        $excludedLocations = $this->serializer->denormalize(array(), Collection::class, 'array');

        $this->tableGateway->setTable('ReferralAgreementLocation');
        $rows = $this->tableGateway->findByColumns(array('guid' => $this->agreement->getGuid()->value(), 'type' => 'exclude'));

        foreach ($rows as $row) {
            $location = $this->serializer->denormalize(array(
                'agreementId' => array('value' => $row['agreementId']),
                'location' => $row['location'],
                'type' => array('value' => $row['type']
                )), Location::class, 'array');

            $excludedLocations->add($location);
        }

        $this->agreement->setExcludedLocations($excludedLocations);
    }

    protected function includedServices()
    {
        $this->checkState();

        $includedServices = $this->serializer->denormalize(array(), Collection::class, 'array');

        $this->tableGateway->setTable('v_referral_agreement_service');
        $rows = $this->tableGateway->findByColumns(array('guid' => $this->agreement->getGuid()->value(), 'type' => 'include'));

        foreach ($rows as $row) {
            $service = $this->serializer->denormalize(array(
                'agreementId' => array('value' => $row['agreementId']),
                'service' => $row['service'],
                'type' => array('value' => $row['type'])
            ), Service::class, 'array');

            $includedServices->add($service);
        }

        $this->agreement->setIncludedServices($includedServices);
    }

    protected function excludedServices()
    {
        $this->checkState();

        $excludedServices = $this->serializer->denormalize(array(), Collection::class, 'array');

        $this->tableGateway->setTable('v_referral_agreement_service');
        $rows = $this->tableGateway->findByColumns(array('guid' => $this->agreement->getGuid()->value(), 'type' => 'exclude'));

        foreach ($rows as $row) {
            $service = $this->serializer->denormalize(array(
                'agreementId' => array('value' => $row['agreementId']),
                'service' => $row['service'],
                'type' => array('value' => $row['type'])
            ), Service::class, 'array');
            $excludedServices->add($service);
        }

        $this->agreement->setExcludedServices($excludedServices);
    }

    protected function inviteeList()
    {
        // hydrate invitee list
        $this->checkState();

        $inviteeList = $this->serializer->denormalize(array(), Collection::class, 'array');

        $this->tableGateway->setTable('v_referral_agreement_invitee');
        $rows = $this->tableGateway->findBy('agreementGuid', $this->agreement->getGuid()->value());

        foreach ($rows as $row) {

            $emailAddress = $this->serializer->denormalize(array(
                'emailAddress' => $row['emailAddress'],
                'type' => array('value' => EmailAddressType::PERSONAL()->getValue())
            ), \PapaLocal\Core\ValueObject\EmailAddress::class, 'array');

            $agreementId = $this->serializer->denormalize(array('value' => $row['agreementGuid']), Guid::class, 'array');

            $invitee = new ReferralAgreementInvitee($agreementId, $row['firstName'], $row['lastName'], $row['message'], $emailAddress);

            if (!is_null($row['phoneNumber'])) {
                $phoneNumber = $this->serializer->denormalize(array(
                    'phoneNumber' => $row['phoneNumber'],
                    'type' => array('value' => PhoneNumberType::PERSONAL()->getValue())
                ), PhoneNumber::class, 'array');
                $invitee->setPhoneNumber($phoneNumber);
            }

            if (! empty($row['userGuid'])) {
                $userId = $this->serializer->denormalize(array('value' => $row['userGuid']), Guid::class, 'array');
                $invitee->setUserId($userId);
            }

            $invitee->setIsDeclined($row['declined']);
            $invitee->setIsParticipant($row['isParticipant']);

            if (! is_null($row['timeSent'])) {
                $invitee->setTimeNotified($row['timeSent']);

                if ($row['declined']) {
                    $invitee->setCurrentPlace('Declined');
                } elseif ($row['isParticipant']) {
                    $invitee->setCurrentPlace('Accepted');
                } else {
                    $invitee->setCurrentPlace('Invited');
                }
            } else {
                $invitee->setCurrentPlace('Created');
            }

            $inviteeList->add($invitee);
        }

        $this->agreement->setInvitees($inviteeList);
    }

    protected function statusHistory()
    {
        $this->checkState();

        $this->tableGateway->setTable('v_referral_agreement_status_history');
        $rows = $this->tableGateway->findByGuid($this->agreement->getGuid()->value());

        $history = $this->serializer->denormalize(array(), Collection::class, 'array');

        foreach ($rows as $row) {
            $status = $this->serializer->denormalize(array(
                'agreementId' => array('value' => $row['guid']),
                'status' => array('value' => ($row['status'])),
                'reason' => array('value' => ($row['reason'])),
                'updater' => array('value' => $row['updaterUserGuid']),
                'timeUpdated' => $row['timeUpdated']
            ), AgreementStatus::class, 'array');

            $history->add($status);
        }

        // TODO: use factory or serializer
        $statusHistory = new StatusHistory($history);

        if (count($statusHistory->getHistory()) > 0) {
            $currentStatus = $statusHistory->getCurrentStatus();

            if ($currentStatus->getStatus() == Status::INACTIVE()) {
                if ($currentStatus->getReason() == StatusChangeReason::CREATED()) {
                    $this->agreement->setCurrentPlace(StatusChangeReason::CREATED()->getValue());
                } else {
                    $this->agreement->setCurrentPlace(Status::INACTIVE()->getValue());
                }
            } else {
                $this->agreement->setCurrentPlace(Status::ACTIVE());
            }
        }


        $this->agreement->setStatusHistory($statusHistory);
    }

    /**
     * Prevents client from misusing this fluent interface.
     *
     * @throws InvalidStateException
     */
	protected function checkState()
    {
        if (! $this->setIdWasCalled) {
            throw new InvalidStateException('A call to setId must be made first.');
        }

        return true;
    }

    /**
     * Returns the number of invitations that exist for the agreement.
     *
     * @param string $agreementId
     * @return int
     */
    protected function countInvitees(string $agreementId): int
    {
        $this->tableGateway->setTable('v_referral_agreement_invitee');
        $rows = $this->tableGateway->findBy('agreementGuid', $agreementId);

        return count($rows);
    }

    /**
     * @param string $agreementId
     * @return int
     */
    protected function countParticipants(string $agreementId): int
    {
        $this->tableGateway->setTable('v_referral_agreement_invitee');
        $rows = $this->tableGateway->findByColumns(array(
            'agreementGuid' => $agreementId,
            'isParticipant' => 1));

        return count($rows);
    }

	/**
	 * {@inheritdoc}
	 */
	public function setEntity(Entity $entity)
	{
		throw new \BadFunctionCallException(sprintf('Not implemented in %s', self::class));
	}
}
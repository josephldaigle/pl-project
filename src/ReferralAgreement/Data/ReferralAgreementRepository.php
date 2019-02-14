<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/28/18
 * Time: 7:15 PM
 */


namespace PapaLocal\ReferralAgreement\Data;


use PapaLocal\Referral\Message\MessageFactory as ReferralMessageFactory;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Entity\Factory\ReferralAgreementFactory;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException;
use PapaLocal\ReferralAgreement\ValueObject\Strategy;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * ReferralAgreementRepository.
 *
 * @package PapaLocal\ReferralAgreement\Data
 */
class ReferralAgreementRepository
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
     * @var ReferralAgreementFactory
     */
    private $agreementFactory;

    /**
     * @var GuidGeneratorInterface
     */
    private $guidFactory;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var ReferralMessageFactory
     */
    private $referralMsgFactory;

    /**
     * ReferralAgreementRepository constructor.
     *
     * @param MessageBusInterface      $mysqlBus
     * @param MessageFactory           $mysqlMsgFactory
     * @param ReferralAgreementFactory $agreementFactory
     * @param GuidGeneratorInterface   $guidFactory
     * @param MessageBusInterface      $appBus
     * @param ReferralMessageFactory   $referralMsgFactory
     */
    public function __construct(
        MessageBusInterface $mysqlBus,
        MessageFactory $mysqlMsgFactory,
        ReferralAgreementFactory $agreementFactory,
        GuidGeneratorInterface $guidFactory,
        MessageBusInterface $appBus,
        ReferralMessageFactory $referralMsgFactory
    )
    {
        $this->mysqlBus           = $mysqlBus;
        $this->mysqlMsgFactory    = $mysqlMsgFactory;
        $this->agreementFactory   = $agreementFactory;
        $this->guidFactory        = $guidFactory;
        $this->appBus             = $appBus;
        $this->referralMsgFactory = $referralMsgFactory;
    }

    /**
     * @param GuidInterface $agreementGuid
     *
     * @return ReferralAgreement
     * @throws AgreementNotFoundException
     */
    public function findByGuid(GuidInterface $agreementGuid)
    {
        // load agreement header
        $query      = $this->mysqlMsgFactory->newFindByGuid('v_referral_agreement', $agreementGuid);
        $agmtHeader = $this->mysqlBus->dispatch($query);

        if ($agmtHeader->isEmpty()) {
            throw new AgreementNotFoundException(sprintf('Unable to locate agreement with guid: %s',
                $agreementGuid->value()));
        }

        // load locations
        $locationQry     = $this->mysqlMsgFactory->newFindBy('v_referral_agreement_location', 'agreementId',
            $agmtHeader['id']);
        $locationRecords = $this->mysqlBus->dispatch($locationQry);

        // load services
        $serviceQry     = $this->mysqlMsgFactory->newFindBy('v_referral_agreement_service', 'agreementId',
            $agmtHeader['id']);
        $serviceRecords = $this->mysqlBus->dispatch($serviceQry);

        // load status history
        $statusQuery   = $this->mysqlMsgFactory->newFindBy('v_referral_agreement_status_history', 'agreementGuid',
            $agmtHeader['guid']);
        $statusRecords = $this->mysqlBus->dispatch($statusQuery);

        $agreement = $this->agreementFactory->createFromRecords($agmtHeader, $locationRecords, $serviceRecords,
            $statusRecords);

        return $agreement;
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return Collection
     * @throws AgreementNotFoundException
     */
    public function loadUserAgreements(GuidInterface $userGuid): Collection
    {
        // load agreement headers
        $query          = $this->mysqlMsgFactory->newFindBy('v_referral_agreement', 'ownerGuid', $userGuid->value());
        $agmtHeadersSet = $this->mysqlBus->dispatch($query);

        // create agreement list
        $agreementList = $this->agreementFactory->getListBuilder();

        foreach ($agmtHeadersSet as $header) {
            $agmtGuid  = $this->guidFactory->createFromString($header['guid']);
            $agreement = $this->findByGuid($agmtGuid);
            $agreementList->add($agreement);
        }

        return $agreementList->build();
    }

    /**
     * @param GuidInterface $participantGuid
     *
     * @return \PapaLocal\Core\ValueObject\Collection\ListBuilder|Collection
     * @throws AgreementNotFoundException
     */
    public function loadParticipantAgreements(GuidInterface $participantGuid)
    {
        // load agreement headers
        $query          = $this->mysqlMsgFactory->newFindByCols('v_referral_agreement_invitee', array(
            'userGuid'      => $participantGuid->value(),
            'isParticipant' => 1,
        ));
        $agmtHeadersSet = $this->mysqlBus->dispatch($query);

        // create agreement list
        $agreementList = $this->agreementFactory->getListBuilder();

        foreach ($agmtHeadersSet as $header) {
            $agmtGuid  = $this->guidFactory->createFromString($header['agreementGuid']);
            $agreement = $this->findByGuid($agmtGuid);
            $agreementList->add($agreement);
        }

        $agreementList = $agreementList->build();

        return $agreementList;
    }

    /**
     * @param GuidInterface $inviteeUserGuid
     *
     * @return Collection
     * @throws AgreementNotFoundException
     */
    public function loadInviteeAgreements(GuidInterface $inviteeUserGuid)
    {
        // load agreement headers
        $query            = $this->mysqlMsgFactory->newFindByCols('v_referral_agreement_invitee', array(
                'userGuid'      => $inviteeUserGuid->value(),
                'sent'          => 1,
                'isParticipant' => 0,
                'declined'      => 0
            )
        );
        $invitationRecSet = $this->mysqlBus->dispatch($query);

        // create agreement list
        $agreementList = $this->agreementFactory->getListBuilder();

        foreach ($invitationRecSet as $invitationRec) {
            $agmtGuid  = $this->guidFactory->createFromString($invitationRec['agreementGuid']);
            $agreement = $this->findByGuid($agmtGuid);

            // only load active agreements for which the invitee is invited
            if ($agreement->getCurrentPlace() == 'Active') {
                $agreementList->add($agreement);
            }
        }

        return $agreementList->build();
    }

    /**
     * @param GuidInterface $agreementGuid
     *
     * @return mixed
     */
    public function getCurrentPeriodReferralCount(GuidInterface $agreementGuid)
    {
        $agreement = $this->findByGuid($agreementGuid);

        if ($agreement->getStrategy() === Strategy::WEEKLY()) {
            //weekly strategy
            $interval = new \DateInterval('P7D');
        } else {
            // monthly strategy
            $interval = new \DateInterval('P1M');
        }

        $today = \DateTime::createFromFormat('Y:m:d H:i:s', date('Y:m:d', time()) . ' 23:59:59');
        $firstDayOfPeriod = (\DateTime::createFromFormat('Y:m:d H:i:s', date('Y:m:d H:i:s', strtotime('now'))))->sub($interval);

        $referralQuery = $this->referralMsgFactory->newFindByAgreementGuid($agreementGuid);

        $referrals = $this->appBus->dispatch($referralQuery);

        // filter out referrals not in current period
        foreach ($referrals as $key => $referral) {
            $createdDate = \DateTime::createFromFormat('Y:m:d H:i:s', date('Y:m:d H:i:s', strtotime($referral->getTimeCreated())));

            if (! ($createdDate >= $firstDayOfPeriod && $createdDate <= $today)) {
                $referrals->remove($key);
            }
        }

        return $referrals->count();
    }

//    /**
//     * Load an invitee for a referral agreement.
//     *
//     * @param string $agreementId
//     * @param string $emailAddress
//     *
//     * @return null
//     */
//    public function loadInvitee(string $agreementId, string $emailAddress)
//    {
//        $this->tableGateway->setTable('v_referral_agreement_invitee');
//        $rows = $this->tableGateway->findByColumns(array(
//            'guid'         => $agreementId,
//            'emailAddress' => $emailAddress,
//        ));
//
//        if (count($rows) > 0) {
//
//            $person = $this->serializer->denormalize(array(
//                'id'        => $rows[0]['personId'],
//                'firstName' => $rows[0]['firstName'],
//                'lastName'  => $rows[0]['lastName'],
//            ), Person::class, 'array');
//
//            $emailAddress = $this->serializer->denormalize(array(
//                'id'           => $rows[0]['emailId'],
//                'emailAddress' => $rows[0]['emailAddress'],
//            ), EmailAddress::class, 'array');
//
//            $invitee = $this->serializer->denormalize($rows[0], ReferralAgreementInvitee::class, 'array');
//            $invitee->setPerson($person);
//            $invitee->setEmailAddress($emailAddress);
//
//            return $invitee;
//        }
//
//        return null;
//    }
//
//    /**
//     * Update a Referral Agreement's name.
//     *
//     * @param string $guid
//     * @param string $name
//     */
//    public function updateName(string $guid, string $name)
//    {
//        // dispatch message to mysql bus
//        $command = $this->mysqlMsgFactory->newUpdateAgreementName($guid, $name);
//        $this->mysqlBus->dispatch($command);
//
//        return;
//
//    }
//
//    /**
//     * Update a referral agreements description.
//     *
//     * @param ReferralAgreement $agreement
//     *
//     * @return int
//     * @throws NotFoundException
//     */
//    public function updateDescription(ReferralAgreement $agreement)
//    {
//        $this->tableGateway->setTable('ReferralAgreement');
//        $rows = $this->tableGateway->findBy('agreementId', $agreement->getId());
//
//        if (count($rows) < 1) {
//            throw new NotFoundException(sprintf('Unable to find an agreement with id: %s', $agreement->getId()));
//        }
//
//        $rows[0]['description'] = $agreement->getDescription();
//
//        return $this->tableGateway->update($rows[0]);
//    }
//
//    /**
//     * Update a referral agreements quantity.
//     *
//     * @param ReferralAgreement $agreement
//     *
//     * @return int
//     * @throws NotFoundException
//     */
//    public function updateQuantity(ReferralAgreement $agreement)
//    {
//        $this->tableGateway->setTable('ReferralAgreement');
//        $rows = $this->tableGateway->findBy('agreementId', $agreement->getId());
//
//        if (count($rows) < 1) {
//            throw new NotFoundException(sprintf('Unable to find an agreement with id: %s', $agreement->getId()));
//        }
//
//        $rows[0]['quantity'] = $agreement->getQuantity();
//
//        return $this->tableGateway->update($rows[0]);
//    }
//
//    /**
//     * Update a referral agreements strategy.
//     *
//     * @param ReferralAgreement $agreement
//     *
//     * @return int
//     * @throws NotFoundException
//     */
//    public function updateStrategy(ReferralAgreement $agreement)
//    {
//        // fetch strategy id
//        $this->tableGateway->setTable('L_ReferralAgreementStrategy');
//        $strategyId = $this->tableGateway->findBy('strategy', $agreement->getStrategy());
//
//        if (count($strategyId) < 1) {
//            throw new NotFoundException(sprintf('Unable to find an id for strategy [%s]', $agreement->getStrategy()));
//        }
//
//        // save strategy
//        $this->tableGateway->setTable('ReferralAgreement');
//        $rows = $this->tableGateway->findBy('agreementId', $agreement->getId());
//
//        if (count($rows) < 1) {
//            throw new NotFoundException(sprintf('Unable to find an agreement with id: %s', $agreement->getId()));
//        }
//
//        $rows[0]['strategyId'] = $strategyId[0]['id'];
//
//        return $this->tableGateway->update($rows[0]);
//    }
//
//    /**
//     * Update a referral agreements bid.
//     *
//     * @param ReferralAgreement $agreement
//     *
//     * @return int
//     * @throws NotFoundException
//     */
//    public function updateBid(ReferralAgreement $agreement)
//    {
//        $this->tableGateway->setTable('ReferralAgreement');
//        $rows = $this->tableGateway->findBy('agreementId', $agreement->getId());
//
//        if (count($rows) < 1) {
//            throw new NotFoundException(sprintf('Unable to find an agreement with id: %s', $agreement->getId()));
//        }
//
//        $rows[0]['bid'] = $agreement->getBid();
//
//        return $this->tableGateway->update($rows[0]);
//    }
//
//    /**
//     * Save an array of locations to the database.
//     *
//     * @param int    $agreementId
//     * @param array  $locations
//     * @param string $type
//     */
//    public function createLocations(int $agreementId, array $locations, string $type)
//    {
//        if (is_null($locations) || empty($locations)) {
//            return;
//        }
//
//        // save each location
//        $this->tableGateway->setTable('R_ReferralAgreementLocation');
//
//        foreach ($locations as $location) {
//            $this->tableGateway->create(array(
//                'agreementId' => $agreementId,
//                'location'    => $location->getName(),
//                'type'        => $type,
//            ));
//        }
//    }
//
//    /**
//     * @param int         $agreementId
//     * @param array       $services
//     * @param ServiceType $type
//     */
//    public function createServices(int $agreementId, array $services, ServiceType $type)
//    {
//        if (is_null($services) || empty($services)) {
//            return;
//        }
//
//        // save each location
//        $this->tableGateway->setTable('R_ReferralAgreementLocation');
//
//        foreach ($services as $service) {
//            $this->tableGateway->create(array(
//                'agreementId' => $agreementId,
//                'service'     => $service->getName(),
//                'type'        => $type,
//            ));
//        }
//    }
//
//    /**
//     * Updates a referral agreements locations.
//     *
//     * @param ReferralAgreement $agreement
//     *
//     * @return bool
//     */
//    public function updateIncludedLocations(ReferralAgreement $agreement)
//    {
//        // fetch locations
//        $this->tableGateway->setTable('v_referral_agreement_location');
//        $rows = $this->tableGateway->findByColumns(array('guid' => $agreement->getGuid(), 'type' => 'include'));
//
//        if (count($rows) < 1 && $agreement->getIncludedLocations()->count() > 0) {
//            // add new agreements only
//            foreach ($agreement->getIncludedLocations()->all() as $location) {
//                $this->tableGateway->create(array(
//                    'agreementId' => $agreement->getId(),
//                    'location'    => $location->getLocation(),
//                    'type'        => 'include',
//                ));
//            }
//        }
//
//        if (count($rows) > 0 && $agreement->getIncludedLocations()->count() < 1) {
//            // remove agreements only
//            foreach ($rows as $row) {
//                $this->tableGateway->delete($row['id']);
//            }
//        }
//
//        if (count($rows) > 0 && $agreement->getIncludedLocations()->count() > 0) {
//            // reconcile the list
//            $userProvidedLocations = $agreement->getIncludedLocations();
//
//            foreach ($rows as $row) {
//                if (is_null($userProvidedLocations->findBy('location', $row['location']))) {
//                    // delete the row (not in user's list)
//                    $this->tableGateway->delete($row['id']);
//                }
//            }
//
//            $storedLocations = $rows;
//            array_walk($storedLocations, function (&$item) {
//                $item = $item['location'];
//            });
//            foreach ($userProvidedLocations->all() as $location) {
//                if ( ! in_array($location->getLocation(), $storedLocations)) {
//                    // create the location
//                    $this->tableGateway->create(array(
//                        'agreementId' => $agreement->getId(),
//                        'location'    => $location->getLocation(),
//                        'type'        => 'include',
//                    ));
//                }
//            }
//        }
//
//        return true;
//    }
//
//    /**
//     * Updates a referral agreements locations.
//     *
//     * @param ReferralAgreement $agreement
//     *
//     * @return bool
//     */
//    public function updateExcludedLocations(ReferralAgreement $agreement)
//    {
//        // fetch locations
//        $this->tableGateway->setTable('R_ReferralAgreementLocation');
//        $rows = $this->tableGateway->findByColumns(array('agreementId' => $agreement->getId(), 'type' => 'exclude'));
//
//        if (count($rows) < 1 && $agreement->getExcludedLocations()->count() > 0) {
//            // add new agreements only
//            foreach ($agreement->getExcludedLocations()->all() as $location) {
//                $this->tableGateway->create(array(
//                    'agreementId' => $agreement->getId(),
//                    'location'    => $location->getLocation(),
//                    'type'        => 'exclude',
//                ));
//            }
//        }
//
//        if (count($rows) > 0 && $agreement->getExcludedLocations()->count() < 1) {
//            // remove agreements only
//            foreach ($rows as $row) {
//                $this->tableGateway->delete($row['id']);
//            }
//        }
//
//        if (count($rows) > 0 && $agreement->getExcludedLocations()->count() > 0) {
//            // reconcile the list
//            $userProvidedLocations = $agreement->getExcludedLocations();
//
//            foreach ($rows as $row) {
//                if (is_null($userProvidedLocations->findBy('location', $row['location']))) {
//                    // delete the row (not in user's list)
//                    $this->tableGateway->delete($row['id']);
//                }
//            }
//
//            $storedLocations = $rows;
//            array_walk($storedLocations, function (&$item) {
//                $item = $item['location'];
//            });
//            foreach ($userProvidedLocations->all() as $location) {
//                if ( ! in_array($location->getLocation(), $storedLocations)) {
//                    // create the location
//                    $this->tableGateway->create(array(
//                        'agreementId' => $agreement->getId(),
//                        'location'    => $location->getLocation(),
//                        'type'        => 'exclude',
//                    ));
//                }
//            }
//        }
//
//        return true;
//    }

    /**
     * @param Guid       $agreementId
     * @param Collection $services
     */
    public function updateServices(Guid $agreementId, Collection $services)
    {
        $updateServicesCmd = $this->mysqlMsgFactory->newUpdateServices($agreementId, $services);
        $this->mysqlBus->dispatch($updateServicesCmd);

        return;
    }

//    /**
//     * Updates a referral agreements included services.
//     *
//     * @param ReferralAgreement $agreement
//     *
//     * @return bool
//     */
//    public function updateIncludedServices(ReferralAgreement $agreement)
//    {
//        // fetch locations
//        $this->tableGateway->setTable('R_ReferralAgreementService');
//        $rows = $this->tableGateway->findByColumns(array('agreementId' => $agreement->getId(), 'type' => 'include'));
//
//        if (count($rows) < 1 && $agreement->getIncludedServices()->count() > 0) {
//            // add new agreements only
//            foreach ($agreement->getIncludedServices()->all() as $service) {
//                $this->tableGateway->create(array(
//                    'agreementId' => $agreement->getId(),
//                    'service'     => $service->getService(),
//                    'type'        => 'include',
//                ));
//            }
//        }
//
//        if (count($rows) > 0 && $agreement->getIncludedServices()->count() < 1) {
//            // remove agreements only
//            foreach ($rows as $row) {
//                $this->tableGateway->delete($row['id']);
//            }
//        }
//
//        if (count($rows) > 0 && $agreement->getIncludedServices()->count() > 0) {
//            // reconcile the list
//            $userProvidedServices = $agreement->getIncludedServices();
//
//            foreach ($rows as $row) {
//                if (is_null($userProvidedServices->findBy('service', $row['service']))) {
//                    // delete the row (not in user's list)
//                    $this->tableGateway->delete($row['id']);
//                }
//            }
//
//            $storedServices = $rows;
//            array_walk($storedServices, function (&$item) {
//                $item = $item['service'];
//            });
//            foreach ($userProvidedServices->all() as $service) {
//                if ( ! in_array($service->getService(), $storedServices)) {
//                    // create the location
//                    $this->tableGateway->create(array(
//                        'agreementId' => $agreement->getId(),
//                        'service'     => $service->getService(),
//                        'type'        => 'include',
//                    ));
//                }
//            }
//        }
//
//        return true;
//    }
//
//    /**
//     * Updates a referral agreements excluded services.
//     *
//     * @param ReferralAgreement $agreement
//     *
//     * @return bool
//     */
//    public function updateExcludedServices(ReferralAgreement $agreement)
//    {
//        // fetch locations
//        $this->tableGateway->setTable('R_ReferralAgreementService');
//        $rows = $this->tableGateway->findByColumns(array('agreementId' => $agreement->getId(), 'type' => 'exclude'));
//
//        if (count($rows) < 1 && $agreement->getExcludedServices()->count() > 0) {
//            // add new agreements only
//            foreach ($agreement->getExcludedServices()->all() as $service) {
//                $this->tableGateway->create(array(
//                    'agreementId' => $agreement->getId(),
//                    'service'     => $service->getService(),
//                    'type'        => 'exclude',
//                ));
//            }
//        }
//
//        if (count($rows) > 0 && $agreement->getExcludedServices()->count() < 1) {
//            // remove agreements only
//            foreach ($rows as $row) {
//                $this->tableGateway->delete($row['id']);
//            }
//        }
//
//        if (count($rows) > 0 && $agreement->getExcludedServices()->count() > 0) {
//            // reconcile the list
//            $userProvidedServices = $agreement->getExcludedServices();
//
//            foreach ($rows as $row) {
//                if (is_null($userProvidedServices->findBy('service', $row['service']))) {
//                    // delete the row (not in user's list)
//                    $this->tableGateway->delete($row['id']);
//                }
//            }
//
//            $storedServices = $rows;
//            array_walk($storedServices, function (&$item) {
//                $item = $item['service'];
//            });
//            foreach ($userProvidedServices->all() as $service) {
//                if ( ! in_array($service->getService(), $storedServices)) {
//                    // create the location
//                    $this->tableGateway->create(array(
//                        'agreementId' => $agreement->getId(),
//                        'service'     => $service->getService(),
//                        'type'        => 'exclude',
//                    ));
//                }
//            }
//        }
//
//        return true;
//    }


}
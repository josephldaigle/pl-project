<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/23/18
 */


namespace PapaLocal\ReferralAgreement\Entity\Factory;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\Core\ValueObject\Collection\ListBuilder;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Location;
use PapaLocal\ReferralAgreement\ValueObject\Service;
use PapaLocal\ReferralAgreement\ValueObject\StatusHistory;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class ReferralAgreementFactory.
 *
 * @package PapaLocal\ReferralAgreement\Entity\Factory
 */
class ReferralAgreementFactory
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ReferralAgreementFactory constructor.
     *
     * @param SerializerInterface      $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param RecordInterface    $header
     * @param RecordSetInterface $locations
     * @param RecordSetInterface $services
     * @param RecordSetInterface $statusHistory
     *
     * @return ReferralAgreement
     */
    public function createFromRecords(RecordInterface $header, RecordSetInterface $locations, RecordSetInterface $services, RecordSetInterface $statusHistory): ReferralAgreement
    {
        // set up agreement header
        $referralAgreement = $this->serializer->denormalize(array(
            'guid' => array('value' => $header['guid']),
            'companyGuid' => array('value' => $header['companyGuid']),
            'name' => $header['name'],
            'description' => $header['description'],
            'quantity' => $header['quantity'],
            'bid' => $header['bid'],
            'strategy' => $header['strategy'],
            'ownerGuid' => array('value' => $header['ownerGuid'])
            ), ReferralAgreement::class, 'array');
        $referralAgreement->setTimeCreated($header['timeCreated']);

        // create location list
        $locationList = $this->serializer->denormalize(array(), IncludeExcludeList::class, 'array');

        iterator_apply($locations,
            function(\Iterator $iterator, IncludeExcludeList $locationList, SerializerInterface $serializer) {
                $location = $serializer->denormalize(array(
                    'guid' => array('value' => $iterator->current()['agreementId']),
                    'location' => $iterator->current()['location'],
                    'type' => array('value' => $iterator->current()['type'])
                ), Location::class, 'array');

                $locationList->add($location);

                return true;

            }, array($locations, &$locationList, $this->serializer));

        $referralAgreement->setIncludedLocations($locationList->getIncludes());
        $referralAgreement->setExcludedLocations($locationList->getExcludes());

        // create service list
        $serviceList = $this->serializer->denormalize(array(), IncludeExcludeList::class, 'array');

        iterator_apply($services,
            function(\Iterator $iterator, IncludeExcludeList $serviceList, SerializerInterface $serializer) {
                $service = $serializer->denormalize(array(
                    'guid' => array('value' => $iterator->current()['agreementId']),
                    'service' => $iterator->current()['service'],
                    'type' => array('value' => $iterator->current()['type'])
                ), Service::class, 'array');

                $serviceList->add($service);

                return true;

            }, array($services, &$serviceList, $this->serializer));

        $referralAgreement->setIncludedServices($serviceList->getIncludes());
        $referralAgreement->setExcludedServices($serviceList->getExcludes());

        // create status history
        $historyList = $this->serializer->denormalize(array(), Collection::class, 'array');

        iterator_apply($statusHistory,
            function(\Iterator $iterator, Collection $historyList, SerializerInterface $serializer) {

                $record = $serializer->denormalize(array(
                    'agreementId' => array('value' => $iterator->current()['agreementGuid']),
                    'status' => array('value' => $iterator->current()['status']),
                    'timeUpdated' => $iterator->current()['timeUpdated'],
                    'updater' => array('value' => $iterator->current()['updaterUserGuid']),
                    'reason' => array('value' => $iterator->current()['reason'])
                ), AgreementStatus::class, 'array');

                $historyList->add($record);
                
                return true;

            }, array($statusHistory, &$historyList, $this->serializer));

        $statusHistory = new StatusHistory($historyList);
        $referralAgreement->setStatusHistory($statusHistory);

        return $referralAgreement;
    }

    /**
     * @return ListBuilder
     */
    public function getListBuilder(): ListBuilder
    {
        return new ListBuilder($this->serializer);
    }
}
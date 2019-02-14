<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/20/18
 * Time: 9:14 PM
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\ReferralAgreementService;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use PapaLocal\ReferralAgreement\ValueObject\StatusHistory;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class CreateReferralAgreementHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class CreateReferralAgreementHandler
{
    /**
     * @var ReferralAgreementService
     */
    private $referralAgreementService;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * CreateReferralAgreementHandler constructor.
     *
     * @param ReferralAgreementService $referralAgreementService
     * @param SerializerInterface      $serializer
     */
    public function __construct(
        ReferralAgreementService $referralAgreementService,
        SerializerInterface $serializer
    )
    {
        $this->referralAgreementService = $referralAgreementService;
        $this->serializer = $serializer;
    }

    /**
     * @param CreateReferralAgreement $command
     *
     * @throws \PapaLocal\ReferralAgreement\Exception\AgreementExistsException
     */
    public function __invoke(CreateReferralAgreement $command)
    {
        // create the ReferralAgreementEntity
        $form = $command->getCreateAgreementForm();

        // create a ReferralAgreement instance
        $referralAgreement = $this->serializer->denormalize(array(
            'guid' => array('value' => $command->getAgreementGuid()->value()),
            'companyGuid' => array('value' => $command->getCompanyGuid()->value()),
            'name' => $form->getName(),
            'description' => $form->getDescription(),
            'quantity' => $form->getQuantity(),
            'strategy' => $form->getStrategy(),
            'bid' => $form->getBid()
        ), ReferralAgreement::class, 'array');

        // gather locations & services
        $includedLocations = $this->serializer->denormalize(array(), IncludeExcludeList::class, 'array');
        $referralAgreement->setIncludedLocations($includedLocations->addAll($form->getIncludedLocations()));

        $excludedLocations = $this->serializer->denormalize(array(), IncludeExcludeList::class, 'array');
        $referralAgreement->setExcludedLocations($excludedLocations->addAll($form->getExcludedLocations()));

        $includedServices = $this->serializer->denormalize(array(), IncludeExcludeList::class, 'array');
        $referralAgreement->setIncludedServices($includedServices->addAll($form->getIncludedServices()));

        $excludedServices = $this->serializer->denormalize(array(), IncludeExcludeList::class, 'array');
        $referralAgreement->setExcludedServices($excludedServices->addAll($form->getExcludedServices()));

        $referralAgreement->setOwnerGuid($command->getUserId());

        // add a status history row
        $status = $this->serializer->denormalize(array(
            'agreementId' => array('value' => $command->getAgreementGuid()->value()),
            'status' =>  array('value' => Status::INACTIVE()->getValue()),
            'reason' => array('value' => StatusChangeReason::CREATED()->getValue()),
            'updater' => array('value' => $command->getUserId()->value())
        ), AgreementStatus::class, 'array');

        // create the initial status obj
        $statusHistory = $this->serializer->denormalize(array(
            'statusHistory' => array('items' => array($status))
        ), StatusHistory::class, 'array');

        $referralAgreement->setStatusHistory($statusHistory);

        // invoke service
        $this->referralAgreementService->createReferralAgreement($referralAgreement, $command->getUserId());
    }
}
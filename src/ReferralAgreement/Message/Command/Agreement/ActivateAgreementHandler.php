<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/31/19
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\ReferralAgreementService;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class ActivateAgreementHandler.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class ActivateAgreementHandler
{
    /**
     * @var GuidGeneratorInterface
     */
    private $guidFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ReferralAgreementService
     */
    private $agreementService;

    /**
     * ActivateAgreementHandler constructor.
     *
     * @param GuidGeneratorInterface   $guidFactory
     * @param SerializerInterface      $serializer
     * @param ReferralAgreementService $agreementService
     */
    public function __construct(GuidGeneratorInterface $guidFactory, SerializerInterface $serializer, ReferralAgreementService $agreementService)
    {
        $this->guidFactory = $guidFactory;
        $this->serializer = $serializer;
        $this->agreementService = $agreementService;
    }

    /**
     * @inheritdoc
     *
     * @param ActivateAgreement $command
     *
     * @throws \PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException
     */
    public function __invoke(ActivateAgreement $command)
    {
        // create status object
        $agreementStatus = $this->serializer->denormalize(array(
            'agreementId' => array('value' => $command->getAgreementGuid()),
            'status' => array('value' => Status::ACTIVE()->getValue()),
            'reason' => array('value' => $command->getReason()),
            'updater' => array('value' => $command->getRequestorGuid()),
            'timeUpdated' => date('Y-m-d H:i:s', time())
        ), AgreementStatus::class, 'array');

        // create guid obj
        $agreementGuid = $this->guidFactory->createFromString($command->getAgreementGuid());

        // invoke service
        $this->agreementService->activateAgreement($agreementGuid, $agreementStatus);
        return;
    }
}
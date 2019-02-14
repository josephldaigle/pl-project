<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/1/18
 * Time: 11:41 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException;
use PapaLocal\ReferralAgreement\ReferralAgreementService;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class PauseAgreementHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class PauseAgreementHandler
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ReferralAgreementService
     */
    private $agreementService;

    /**
     * PauseAgreementHandler constructor.
     *
     * @param SerializerInterface      $serializer
     * @param ReferralAgreementService $agreementService
     */
    public function __construct(SerializerInterface $serializer, ReferralAgreementService $agreementService)
    {
        $this->serializer       = $serializer;
        $this->agreementService = $agreementService;
    }

    /**
     * @param PauseAgreement $command
     *
     * @throws AgreementNotFoundException
     */
    public function __invoke(PauseAgreement $command)
    {
        // create status object
        $agreementStatus = $this->serializer->denormalize(array(
            'agreementId' => array('value' => $command->getAgreementGuid()->value()),
            'status' => array('value' => Status::INACTIVE()->getValue()),
            'reason' => array('value' => $command->getChangeReason()->getValue()),
            'updater' => array('value' => $command->getRequestorGuid()->value()),
            'timeUpdated' => date('Y-m-d H:i:s', time())
        ), AgreementStatus::class, 'array');

        // invoke service
        $this->agreementService->pauseAgreement($command->getAgreementGuid(), $agreementStatus);

        return;
    }

}
<?php
/**
 * Created by PhpStorm.
 * Date: 9/21/18
 * Time: 9:43 AM
 */

namespace PapaLocal\Referral;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Referral\Data\ReferralRepository;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\Entity\Referral;
use Symfony\Component\Workflow\Registry;


/**
 * Class ReferralService
 * @package PapaLocal\Referral
 */
class ReferralService
{
    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * @var GuidGeneratorInterface
     */
    private $guidGenerator;


    /**
     * ReferralService constructor.
     * @param Registry $workflowRegistry
     * @param ReferralRepository $referralRepository
     * @param GuidGeneratorInterface $guidGenerator
     */
    public function __construct(Registry $workflowRegistry,
                                ReferralRepository $referralRepository,
                                GuidGeneratorInterface $guidGenerator)
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->referralRepository = $referralRepository;
        $this->guidGenerator = $guidGenerator;
    }

    /**
     * @param Referral $referral
     */
    public function createReferral(Referral $referral)
    {
        $referralGuid = $this->guidGenerator->generate();
        $referral->setGuid($referralGuid);

        $workflow = $this->workflowRegistry->get($referral);
        $workflow->apply($referral, 'create');

        if ($referral->getRecipient() instanceof AgreementRecipient) {
            $workflow->apply($referral, 'acquire');
        }

        return;
    }

    /**
     * @param Referral $referral
     */
    public function rateReferral(Referral $referral)
    {
        $workflow = $this->workflowRegistry->get($referral);

        if ($referral->getRating()->getScore() < 3) {
            if ($referral->getRecipient() instanceof AgreementRecipient) {
                $workflow->apply($referral, 'dispute');
            }
        } else {
            $workflow->apply($referral, 'accept');
        }

        return;
    }

    /**
     * @param Referral $referral
     */
    public function resolveDispute(Referral $referral)
    {
        $workflow = $this->workflowRegistry->get($referral);
        $workflow->apply($referral, 'admin_review');

        return;
    }

    /**
     * @param Guid $guid
     * @return Referral
     * @throws \Exception
     */
    public function findByGuid(Guid $guid)
    {
        try {
            return $this->referralRepository->fetchByGuid($guid);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
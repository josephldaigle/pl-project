<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/28/17
 */

namespace PapaLocal\Data\Command\User\Billing;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Billing\BillingProfile;

/**
 * Class LoadBillingProfile.
 *
 * Loads a user's billing profile detail, including available payment methods.
 */
class LoadBillingProfile extends QueryCommand
{

    /**
     * @var int
     */
    private $userId;

    /**
     * LoadBillingProfile constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @inheritdoc
     */
    protected function runQuery()
    {
        try {
            //attempt to fetch profile
            $this->tableGateway->setTable('BillingProfile');
            $profileRows = $this->tableGateway->findBy('userId', $this->userId);

            if (count($profileRows) < 1) {
                return false;
            }

            // create billing profile
            $billingProfile = $this->serializer->denormalize($profileRows[0], BillingProfile::class, 'array');

            // return billing profile
            return $billingProfile;

        } catch (\Exception $exception) {
            throw $this->filterException($exception);
        }
    }

    /**
     * @inheritdoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }


}
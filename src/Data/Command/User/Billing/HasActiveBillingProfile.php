<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/3/18
 * Time: 9:44 PM
 */

namespace PapaLocal\Data\Command\User\Billing;


use PapaLocal\Data\Command\QueryCommand;

/**
 * HasActiveBillingProfile.
 *
 * Checks whether a user has an active billing profile.
 */
class HasActiveBillingProfile extends QueryCommand
{
    /**
     * @var int
     */
    private $userId;

    /**
     * HasBillingProfile constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        try {
            // query for billing profile
            $this->tableGateway->setTable('BillingProfile');
            $result = $this->tableGateway->findBy('userId', $this->userId);

            if ( (count($result) > 0) && (intval($result[0]['isActive']) === 1)) {
                // billing profile exists
                return true;
            }

            // no billing profile exists or is not active
            return false;

        } catch (\Exception $exception) {

            throw $this->filterException($exception);
        }
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }
}
<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/3/18
 * Time: 9:30 PM
 */

namespace PapaLocal\Data\Command\User\Billing;

use PapaLocal\Data\Command\QueryCommand;
use PHPUnit\Runner\Exception;

/**
 * HasBillingProfile.
 *
 * Checks if a user has a billing profile in the database.
 */
class HasBillingProfile extends QueryCommand
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

            if (count($result) > 0) {
                // billing profile exists
                return true;
            }

            // no billing profile exists, return false
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
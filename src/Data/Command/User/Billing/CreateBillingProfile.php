<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/27/17
 * Time: 8:09 PM
 */

namespace PapaLocal\Data\Command\User\Billing;

use PapaLocal\Data\Command\QueryCommand;

/**
 * CreateBillingProfile.
 *
 * Stores a user's billing profile.
 */
class CreateBillingProfile extends QueryCommand
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $customerId;

    /**
     * CreateBillingProfile constructor.
     *
     * @param int $userId
     * @param int $customerId
     */
    public function __construct(int $userId, int $customerId)
    {
        $this->userId = $userId;
        $this->customerId = $customerId;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {

        try {

            $this->tableGateway->setTable('BillingProfile');

            return $this->tableGateway->create(array(
                'userId' => $this->userId,
                'customerId' => $this->customerId,
                'isActive' => 1
            ));

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
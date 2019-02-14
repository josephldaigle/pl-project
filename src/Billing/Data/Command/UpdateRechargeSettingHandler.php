<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/29/18
 */


namespace PapaLocal\Billing\Data\Command;


use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateRechargeSettingHandler.
 *
 * @package PapaLocal\Billing\Data\Command
 */
class UpdateRechargeSettingHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateRechargeSettingHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateRechargeSetting $command)
    {
        $this->tableGateway->setTable('v_user_billing_profile');
        $userBillProRecs = $this->tableGateway->findBy('userGuid', $command->getUserGuid());

        if ($userBillProRecs->count() > 0) {
            $this->tableGateway->setTable('BillingProfile');
            $custBillProRecs = $this->tableGateway->findBy('customerId', $userBillProRecs->current()['customerId']);

            if ($custBillProRecs->count() > 0) {
                $billProRec = $custBillProRecs->current();
                $billProRec['minBalance'] = $command->getMinBalance();
                $billProRec['maxBalance'] = $command->getMaxBalance();

                $this->tableGateway->update($billProRec->properties());
            }
        }

        return;
    }


}
<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 5/11/18
 */


namespace PapaLocal\Billing\ValueObject;


use PapaLocal\Core\ValueObject\Guid;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class RechargeSetting.
 *
 * Models the  user's settings for automatic account recharges.
 *
 * @package PapaLocal\Billing
 */
class RechargeSetting
{
    /**
     * @var Guid
     */
    private $userGuid;

    /**
     * @var float
     */
    private $minBalance;

    /**
     * @var float
     */
    private $maxBalance;

    /**
     * RechargeSetting constructor.
     *
     * @param Guid  $userGuid
     * @param float $minBalance
     * @param float $maxBalance
     */
    public function __construct(Guid $userGuid, float $minBalance, float $maxBalance)
    {
        $this->userGuid   = $userGuid;
        $this->minBalance = $minBalance;
        $this->maxBalance = $maxBalance;
    }

    /**
     * @return Guid
     */
    public function getUserGuid(): Guid
    {
        return $this->userGuid;
    }

    /**
     * @return mixed
     */
    public function getMinBalance()
    {
        return $this->minBalance;
    }

    /**
     * @return mixed
     */
    public function getMaxBalance()
    {
        return $this->maxBalance;
    }
}
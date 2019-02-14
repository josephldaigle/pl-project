<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/29/18
 */

namespace PapaLocal\Billing\Message\Command;


/**
 * Class UpdateRechargeSetting.
 *
 * @package PapaLocal\Billing\Message\Command
 */
class UpdateRechargeSetting
{
    /**
     * @var string
     */
    private $userGuid;

    /**
     * @var string
     */
    private $minBalance;

    /**
     * @var string
     */
    private $maxBalance;

    /**
     * UpdateRechargeSetting constructor.
     *
     * @param string $userGuid
     * @param string $minBalance
     * @param string $maxBalance
     */
    public function __construct(string $userGuid, string $minBalance, string $maxBalance)
    {
        $this->userGuid = $userGuid;
        $this->minBalance = $minBalance;
        $this->maxBalance = $maxBalance;
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->userGuid;
    }

    /**
     * @return string
     */
    public function getMinBalance(): string
    {
        return $this->minBalance;
    }

    /**
     * @return string
     */
    public function getMaxBalance(): string
    {
        return $this->maxBalance;
    }

}
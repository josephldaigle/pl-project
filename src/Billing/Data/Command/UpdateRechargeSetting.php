<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/29/18
 */

namespace PapaLocal\Billing\Data\Command;


use PapaLocal\Billing\ValueObject\RechargeSetting;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateRechargeSetting.
 *
 * @package PapaLocal\Billing\Data\Command
 */
class UpdateRechargeSetting
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var RechargeSetting
     */
    private $rechargeSetting;

    /**
     * UpdateRechargeSetting constructor.
     *
     * @param GuidInterface   $userGuid
     * @param RechargeSetting $rechargeSetting
     */
    public function __construct(GuidInterface $userGuid, RechargeSetting $rechargeSetting)
    {
        $this->userGuid = $userGuid;
        $this->rechargeSetting = $rechargeSetting;
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->userGuid->value();
    }

    /**
     * @return string
     */
    public function getMinBalance(): string
    {
        return $this->rechargeSetting->getMinBalance();
    }

    /**
     * @return string
     */
    public function getMaxBalance(): string
    {
        return $this->rechargeSetting->getMaxBalance();
    }
}
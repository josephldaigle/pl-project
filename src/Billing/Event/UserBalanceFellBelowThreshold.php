<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/15/18
 * Time: 4:05 PM
 */

namespace PapaLocal\Billing\Event;


use PapaLocal\Core\ValueObject\GuidInterface;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class UserBalanceFellBelowThreshold
 *
 * @package PapaLocal\Billing\Event
 */
class UserBalanceFellBelowThreshold extends Event
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * UserBalanceFellBelowThreshold constructor.
     *
     * @param GuidInterface $userGuid
     */
    public function __construct(GuidInterface $userGuid)
    {
        $this->userGuid = $userGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getAccountOwnerUserGuid(): GuidInterface
    {
        return $this->userGuid;
    }
}
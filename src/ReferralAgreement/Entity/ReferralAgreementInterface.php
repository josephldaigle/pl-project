<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/20/18
 * Time: 5:20 PM
 */

namespace PapaLocal\ReferralAgreement\Entity;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\ValueObject\StatusHistory;


/**
 * Interface ReferralAgreementInterface
 *
 * @package PapaLocal\ReferralAgreement\Entity
 */
interface ReferralAgreementInterface
{
    /**
     * @return Guid
     */
    public function getGuid(): Guid;

    /**
     * @return Guid
     */
    public function getCompanyGuid(): Guid;

    /**
     * @return Guid
     */
    public function getOwnerGuid(): Guid;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return int
     */
    public function getQuantity(): int;

    /**
     * @return float
     */
    public function getBid(): float;

    /**
     * @return string
     */
    public function getCurrentPlace(): string;

    /**
     * @return StatusHistory
     */
    public function getStatusHistory();

    /**
     * @return Collection contains instances of Location
     */
    public function getIncludedLocations(): Collection;

    /**
     * @return Collection
     */
    public function getExcludedLocations(): Collection;

    /**
     * @return Collection
     */
    public function getIncludedServices(): Collection;

    /**
     * @return Collection
     */
    public function getExcludedServices(): Collection;

    /**
     * @return int
     */
    public function getNumberParticipants(): int;

    /**
     * @return int
     */
    public function getNumberInvitees(): int;
}
<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/19/18
 * Time: 1:24 PM
 */


namespace PapaLocal\ReferralAgreement\ValueObject;


use PapaLocal\Core\ValueObject\Guid;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * Class Location
 *
 * @package PapaLocal\ReferralAgreement\ValueObject
 */
class Location
{
	/**
	 * @var Guid
	 */
	private $agreementId;

	/**
	 * @var string
	 */
	private $location;

	/**
	 * @var LocationType
	 */
	private $type;

    /**
     * Location constructor.
     *
     * @param string       $location
     * @param LocationType $type
     * @param Guid         $agreementId
     */
    public function __construct(string $location, LocationType $type, Guid $agreementId = null)
    {
        $this->setLocation($location);
        $this->setType($type);

        if (! is_null($agreementId)) {
            $this->setAgreementId($agreementId);
        }
    }

    /**
     * @param Guid $agreementId
     */
    protected function setAgreementId(Guid $agreementId)
    {
        $this->agreementId = $agreementId;
    }

    /**
     * @param string $location
     */
    protected function setLocation(string $location)
    {
        $this->location = $location;
    }

    /**
     * @param LocationType $type
     */
    protected function setType(LocationType $type)
    {
        $this->type = $type;
    }

    /**
     * @return Guid
     */
    public function getAgreementId(): Guid
    {
        return $this->agreementId;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return LocationType
     */
    public function getType(): LocationType
    {
        return $this->type;
    }
}
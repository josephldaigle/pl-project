<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/14/18
 */


namespace PapaLocal\ReferralAgreement\Feed;


use PapaLocal\Entity\FeedItemInterface;


/**
 * Class AgreementFeedView.
 *
 * @package PapaLocal\ReferralAgreement\Feed
 */
class AgreementFeedView implements FeedItemInterface
{
    /**
     * @var string
     */
    private $guid;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $timeCreated;

    /**
     * @var string
     */
    private $timeUpdated;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $cardBody;

    /**
     * AgreementFeedView constructor.
     *
     * @param string $guid
     * @param string $title
     * @param string $timeCreated
     * @param string $timeUpdated
     */
    public function __construct(string $guid,
                                string $title,
                                string $timeCreated,
                                string $timeUpdated)
    {
        $this->guid = $guid;
        $this->title = $title;
        $this->timeCreated = $timeCreated;
        $this->timeUpdated = $timeUpdated;
    }

    public function getId(): int
    {
        return 0;
    }

    public function getGuid()
    {
        return $this->guid;
    }

    public function getTitle(): string
    {
        return $this->getTitle();
    }

    public function getTimeCreated(): string
    {
        return $this->getTimeCreated();
    }

    public function getTimeUpdated(): string
    {
        return $this->getTimeCreated();
    }

    public function getFeedType(): string
    {
        return 'referralAgreement';
    }

    public function getCardBody(): string
    {
        return 'empty for now';
    }

}
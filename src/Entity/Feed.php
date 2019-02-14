<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 11/22/17
 * Time: 1:56 PM
 */

namespace PapaLocal\Entity;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class Feed
 *
 * @package PapaLocal\Entity
 *
 * Model a Feed item.
 */
class Feed extends Entity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $details;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $display;

    /**
     * @var string
     */
    private $timeCreated;

    /**
     * @var string
     */
    private $timeUpdated;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Feed
     */
    public function setId(int $id): Feed
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Feed
     */
    public function setTitle(string $title): Feed
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDetails(): string
    {
        return $this->details;
    }

    /**
     * @param string $details
     * @return Feed
     */
    public function setDetails(string $details): Feed
    {
        $this->details = $details;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Feed
     */
    public function setType(string $type): Feed
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplay(): string
    {
        return $this->display;
    }

    /**
     * @param string $display
     * @return Feed
     */
    public function setDisplay(string $display): Feed
    {
        $this->display = $display;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeCreated(): string
    {
        return $this->timeCreated;
    }

    /**
     * @param string $timeCreated
     * @return Feed
     */
    public function setTimeCreated(string $timeCreated): Feed
    {
        $this->timeCreated = $timeCreated;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeUpdated(): string
    {
        return $this->timeUpdated;
    }

    /**
     * @param string $timeUpdated
     * @return Feed
     */
    public function setTimeUpdated(string $timeUpdated): Feed
    {
        $this->timeUpdated = $timeUpdated;
        return $this;
    }

}
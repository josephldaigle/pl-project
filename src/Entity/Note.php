<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 11/22/17
 * Time: 1:56 PM
 */

namespace PapaLocal\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class Note extends Entity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $author;

    /**
     * @var string
     */
    private $note;

    /**
     * @var string
     */
    private $timeCreated;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     * @return Note
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     * @return $this
     */
    public function setNote(string $note)
    {
        $this->note = $note;

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
     * @return $this
     */
    public function setTimeCreated(string $timeCreated)
    {
        $this->timeCreated = $timeCreated;

        return $this;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isAuthor(int $userId)
    {
       $found = $this->author->findBy('userId', $userId);

        return (is_null($found)) ? false : true;
    }

}
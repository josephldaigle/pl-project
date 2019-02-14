<?php

/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 11/22/17
 * Time: 1:45 PM
 */

namespace Test\Unit\Entity;

use PHPUnit\Framework\TestCase;
use PapaLocal\Entity\Note;

class NoteTest extends TestCase
{
    public function testCanInstantiate()
    {
        //set up fixtures
        $note = new Note();

        //make assertions
        $this->assertInstanceOf(Note::class, $note);
    }

    public function testCanGetSetId()
    {
        //set up fixtures
        $id = 5;
        $note = (new Note())->setId($id);

        //make assertions
        $this->assertTrue(is_int($note->getId()));
        $this->assertEquals($id, $note->getId());
    }

    public function testCanGetSetNote()
    {
        //set up fixtures
        $message = 'This is a message';
        $note = (new Note())->setNote($message);

        //make assertions
        $this->assertTrue(is_string($note->getNote()));
        $this->assertEquals($message, $note->getNote());
    }

    public function testCanGetSetTimeCreated()
    {
        //set up fixtures
        $timeCreated = '2017-10-23 13:56:38';
        $note = (new Note())->setTimeCreated($timeCreated);

        //make assertions
        $this->assertTrue(is_string($timeCreated));
        $this->assertEquals($timeCreated, $note->getTimeCreated());
    }

    public function testEqualsReturnsTrueWhenNoteIsProperlyGenerated()
    {
        //set up fixtures
        $message = 'This is a note';
        $note = $this->getMockBuilder(Note::class)
            ->setMethodsExcept(['equals', 'setNote', 'getNote'])
            ->getMock();

        $note->setNote($message);
        $result = $note->equals($note);

        //make assertions
        $this->assertTrue($result);
    }

    public function testIsFeedItemReturnsFalse()
    {
        //set up fixtures
        $note = $this->getMockBuilder(Note::class)
            ->setMethodsExcept(['isFeedItem'])
            ->getMock();

        //make assertions
        $this->assertFalse($note->isFeedItem());
    }


}
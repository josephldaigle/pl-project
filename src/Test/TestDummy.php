<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/5/17
 * Time: 12:26 PM
 */

namespace PapaLocal\Test;

use PapaLocal\Entity\Entity;

/**
 * TestDummy.
 *
 * Empty 'shell' entity for testing.
 */
class TestDummy extends Entity
{
    private $member;

    public function __construct($member = null)
    {
        $this->member = $member;
    }

    public function setMember($member)
    {
        $this->member = $member;
        return $this;
    }

    public function getMember()
    {
        return $this->member;
    }
}
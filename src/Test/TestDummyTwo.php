<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 9:39 PM
 */

namespace PapaLocal\Test;

use PapaLocal\Entity\Entity;

/**
 * TestDummyTwo.
 */
class TestDummyTwo extends Entity
{
    private $member;

    private $memberTwo;

    public function setMember($member)
    {
        $this->member = $member;
        return $this;
    }

    public function getMember()
    {
        return $this->member;
    }

    public function setMemberTwo($memberTwo)
    {
        $this->memberTwo = $memberTwo;
        return $this;
    }

    public function getMemberTwo()
    {
        return $this->memberTwo;
    }

}
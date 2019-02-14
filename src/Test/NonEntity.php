<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/29/17
 */

namespace PapaLocal\Test;

/**
 * Class NonEntity.
 *
 * @package PapaLocal\Test
 */
class NonEntity
{
    private $member;

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
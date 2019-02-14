<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/7/18
 * Time: 9:11 AM
 */

namespace PapaLocal\Test;

use PapaLocal\Entity\Entity;
use Symfony\Component\Serializer\Annotation as Serializer;

class SerializerDummy extends Entity
{
    private $memberOne;

    private $memberTwo;

    private $memberThree;

    private $memberFour;

    /**
     * @return mixed
     */
    public function getMemberOne()
    {
        return $this->memberOne;
    }

    /**
     * @param mixed $memberOne
     *
     * @return SerializerDummy
     */
    public function setMemberOne($memberOne)
    {
        $this->memberOne = $memberOne;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMemberTwo()
    {
        return $this->memberTwo;
    }

    /**
     * @param mixed $memberTwo
     *
     * @return SerializerDummy
     */
    public function setMemberTwo($memberTwo)
    {
        $this->memberTwo = $memberTwo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMemberThree()
    {
        return $this->memberThree;
    }

    /**
     * @param mixed $memberThree
     *
     * @return SerializerDummy
     */
    public function setMemberThree($memberThree)
    {
        $this->memberThree = $memberThree;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMemberFour()
    {
        return $this->memberFour;
    }

    /**
     * @param mixed $memberFour
     *
     * @return SerializerDummy
     */
    public function setMemberFour($memberFour)
    {
        $this->memberFour = $memberFour;
        return $this;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 12/2/18
 * Time: 10:21 AM
 */

namespace PapaLocal\Feed\Plugin;


use PapaLocal\Entity\Collection\Collection;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class DateComparator
 * @package PapaLocal\Feed\Plugin
 */
class DateComparator
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * DateComparator constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string $timeCreated
     * @param string $startDate
     * @param string $endDate
     * @return int|string
     */
    public function between(string $timeCreated, string $startDate, string $endDate)
    {
        $timeCreated = $this->serializer->denormalize($timeCreated, \DateTimeInterface::class);
        $startDate = $this->serializer->denormalize($startDate, \DateTimeInterface::class);
        $endDate = $this->serializer->denormalize($endDate . '23:59:59', \DateTimeInterface::class);

       if($timeCreated >= $startDate && $timeCreated <= $endDate) {
           return true;
       }

       return false;
    }
}
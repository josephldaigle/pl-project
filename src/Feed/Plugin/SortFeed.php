<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 11/30/18
 * Time: 8:55 AM
 */

namespace PapaLocal\Feed\Plugin;


use PapaLocal\Entity\Collection\Collection;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class SortFeed
 * @package PapaLocal\Feed\Plugin
 */
class SortFeed
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * SortFeed constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Collection $items
     * @param string $filter
     * @return Collection
     */
    public function sortBy(Collection $items, string $filter)
    {
//        $arrayList = $this->serializer->normalize($items);

        foreach ($items as $item) {
            $a[] = $item;
        }

        switch ($filter) {
            case 'title_asc':
                usort($a, array($this, 'titleAscending'));
                break;
            case 'updated_desc':
                usort($arrayList, array($this, 'updatedDescending'));
                break;
            case 'created_desc':
                usort($arrayList, array($this, 'createdDescending'));
                break;
            case 'created_asc':
                usort($arrayList, array($this, 'createdAscending'));
                break;
        }

        return $items;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    private function titleAscending($a, $b)
    {
        return strcmp($a["title"], $b["title"]);
    }

    /**
     * @param $a
     * @param $b
     * @return false|int
     */
    private function updatedDescending($a, $b)
    {
        return strtotime($a["timeCreated"]) - strtotime($b["timeCreated"]);
    }

    /**
     * @param $a
     * @param $b
     * @return false|int
     */
    private function createdDescending($a, $b)
    {
        return strtotime($a["timeCreated"]) - strtotime($b["timeCreated"]);
    }

    /**
     * @param $a
     * @param $b
     * @return false|int
     */
    private function createdAscending($a, $b)
    {
        return strtotime($b["timeCreated"]) - strtotime($a["timeCreated"]);
    }
}
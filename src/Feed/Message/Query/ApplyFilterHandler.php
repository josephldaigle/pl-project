<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/29/18
 * Time: 11:41 AM
 */

namespace PapaLocal\Feed\Message\Query;


use PapaLocal\Feed\Plugin\DateComparator;
use PapaLocal\Feed\Plugin\SortFeed;
use PapaLocal\Feed\ValueObject\SortBy;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class ApplyFilterHandler
 * @package PapaLocal\Feed\Message\Query
 */
class ApplyFilterHandler
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var DateComparator
     */
    private $dateComparator;

    /**
     * @var SortFeed
     */
    private $sortFeed;

    /**
     * ApplyFilterHandler constructor.
     * @param SerializerInterface $serializer
     * @param DateComparator $dateComparator
     * @param SortFeed $sortFeed
     */
    public function __construct(SerializerInterface $serializer, DateComparator $dateComparator, SortFeed $sortFeed)
    {
        $this->serializer = $serializer;
        $this->dateComparator = $dateComparator;
        $this->sortFeed = $sortFeed;
    }

    /**
     * @param ApplyFilter $query
     * @return mixed
     */
    function __invoke(ApplyFilter $query)
    {
        $feedList = $query->getFeedItems();
        $filter = $query->getFeedFilter();

        // Filter list
        foreach ($feedList as $key => $item){

            // Apply type filter
            if(! in_array($item->getFeedType(), $query->getFeedFilter()->getTypes())){
                $feedList->remove($key);
                continue;
            }

            // Apply date filter
            if(! $this->dateComparator->between($item->getTimeCreated(), $filter->getStartDate(), $filter->getEndDate())){
                $feedList->remove($key);
            }
        }

        // Sort list
        $feedList->sortBy([SortBy::class, constant(SortBy::class . '::' . $filter->getSortOrder())]);

        // Load top 15
        $feedList->reduceTo($query->getCount());

        return $feedList;
    }
}
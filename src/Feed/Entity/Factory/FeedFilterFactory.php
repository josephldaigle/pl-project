<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 11/29/18
 * Time: 9:46 PM
 */

namespace PapaLocal\Feed\Entity\Factory;


use PapaLocal\Feed\Form\FeedFilter;


/**
 * Class FeedFilterFactory
 *
 * @package PapaLocal\Feed\Entity\Factory
 */
class FeedFilterFactory
{
    /**
     * Create a feed filter with some default settings.
     * @return \PapaLocal\Feed\ValueObject\FeedFilter
     */
    public function createDefault()
    {
        return new \PapaLocal\Feed\ValueObject\FeedFilter(
            array('transaction', 'agreement', 'referral'),
            '01/01/2015',
            date("m/d/Y"),
            'NEWEST_FIRST'
        );
    }

    /**
     * Create a feed filter from a form object.
     *
     * @param FeedFilter $form
     *
     * @return \PapaLocal\Feed\ValueObject\FeedFilter
     */
    public function createFromForm(FeedFilter $form)
    {
        return new \PapaLocal\Feed\ValueObject\FeedFilter(
            $form->getTypes(),
            $form->getStartDate(),
            $form->getEndDate(),
            $form->getSortOrder()
        );
    }
}
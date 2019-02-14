<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/29/18
 * Time: 4:41 PM
 */

namespace PapaLocal\Feed\ParamConverter;


use PapaLocal\Feed\Form\FeedFilter;
use PapaLocal\Feed\Form\SelectFeedItemForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class FeedFilterParamConverter
 * @package PapaLocal\Feed\ParamConverter
 */
class FeedFilterParamConverter
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * FeedFilterParamConverter constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createFromRequest(Request $request)
    {
        $feedFilterForm = $this->serializer->denormalize(
        array(
            'guid' => $request->request->get('id'),
            'types' =>  $request->request->get('types'),
            'startDate' =>  $request->request->get('startDate'),
            'endDate' =>  $request->request->get('endDate'),
            'sortOrder' =>  $request->request->get('sortOrder'),
        ),
        FeedFilter::class, 'array');

        if ( (count($feedFilterForm->getTypes()) === 1) && ($request->request->has('id')) ) {
            $selectedItem = $this->serializer->denormalize(['guid' => $request->request->get('id'), 'type' => $feedFilterForm->getTypes()[0]], SelectFeedItemForm::class, 'array');

            $feedFilterForm->setSelectedItem($selectedItem);
        }

        return $feedFilterForm;
    }
}
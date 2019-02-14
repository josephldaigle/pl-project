<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/6/18
 * Time: 9:11 PM
 */

namespace PapaLocal\Core\ValueObject\Collection;


use PapaLocal\Entity\Collection\Collection;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class ListBuilder
 *
 * @package PapaLocal\Core\ValueObject\Collection
 */
class ListBuilder implements ListBuilderInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    private $items;

    /**
     * ListBuilder constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->items = [];
    }

    /**
     * {@inheritdoc}
     */
    public function add($object, string $key = ''): ListBuilderInterface
    {
        if (! empty($key)) {
            $this->items[$key] = $object;
        } else {
            $this->items[] = $object;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): Collection
    {
        $list = $this->serializer->denormalize(array('items' => $this->items), Collection::class, 'array');
        $this->items = [];

        return $list;
    }
}
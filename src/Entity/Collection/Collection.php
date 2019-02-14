<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/16/17
 */


namespace PapaLocal\Entity\Collection;


/**
 * Class Collection.
 */
class Collection implements CollectionInterface
{

    /**
     * @var array
     */
    protected $items = [];

    /**
     * Collection constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = array())
    {
        $this->addAll($items);
    }

    /**
     * Add an element to the beginning of the collection.
     *
     * @param $object
     */
    public function prepend($object)
    {
        array_unshift($this->items, $object);
    }

    /**
     * Add an element to end of the collection.
     *
     * @param      $object
     * @param null $key
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function add($object, $key = null)
    {
        if (is_null($object) || empty($object)) {
            throw new \InvalidArgumentException(sprintf('Param 1 passed to %s cannot be null or empty.',
                __METHOD__));
        }

        //if no key exists, add the object to the collection
        if ($key === null || is_numeric($key)) {
            $this->items[] = $object;
        } else {

            if (isset($this->items[$key])) {
                throw new \InvalidArgumentException(sprintf('Key %s is already assigned.', $key));

            } else {
                $this->items[$key] = $object;
            }
        }
    }

    /**
     * @param $items
     *
     * @return Collection
     */
    public function addAll($items): Collection
    {
        foreach ($items as $key => $val) {
            $this->add($val, $key);
        }

        return $this;
    }

    /**
     * Whether or not an item exists at index $key.
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        if (array_key_exists($key, $this->items)) {
            return true;
        }

        return false;
    }

    /**
     * Fetch an item from the collection.
     *
     * @param $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($key)
    {
        if (! array_key_exists($key, $this->items)) {
            throw new \InvalidArgumentException(sprintf('Key %s does not exist.', $key));
        }

        return $this->items[$key];
    }

	/**
	 * Fetch the first item in the collection.
	 *
	 * @return mixed|null
	 */
	public function first()
	{
		foreach($this->items as $item) {
			return $item;
		}

		return null;
    }

	/**
	 * Fetch the last item in the collection.
	 *
	 * @return mixed|null
	 */
	public function last()
	{
		$reversed = array_reverse($this->items, true);

		foreach($reversed as $item) {
			return $item;
		}

		return null;
    }

    /**
     * Find an item in the collection by it's $property containing $value.
     *
     * @param $property
     * @param $value
     * @return mixed returns null if the collection is empty or the item is not found
     * @throws \BadMethodCallException if the collection contains primitives
     */
    public function findBy($property, $value)
    {
        //search for the item
        foreach ($this->items as $item) {

            switch (gettype($item)) {
                case 'array':

                    if ( (array_key_exists($property, $item)) && ($value === $item[$property]) ) {
                        return $item;
                    }

                    break;

                case 'object':

                    //check if property exists
                    if (property_exists($item, $property)) {
                        //property exists, check it's value matches - use reflection to un-hide private members
                        $refObj = new \ReflectionObject($item);
                        $refProp = $refObj->getProperty($property);
                        $refProp->setAccessible(true);

                        //verify property value
                        if ($value == $refProp->getValue($item)) {
                            return $item;
                        }
                    }

                    break;

                default:        //can't call this function on collections of primitives (not array or object)
                    throw new \BadMethodCallException('Cannot call findBy on a collection that'
                    . ' contains items other than arrays or objects.');
            }
        }

        //no object found
        return null;
    }

    /**
     * Fetch all items in the collection as an array.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Remove an item from the collection.
     *
     * @param $key
     * @throws \InvalidArgumentException
     */
    public function remove($key)
    {
        if (! array_key_exists($key, $this->items)) {
            throw new \InvalidArgumentException(sprintf('Key supplied does not exist.', $key));
        }

        unset($this->items[$key]);
    }


    /**
     * Replace an element in the collection.
     *
     * @param $object
     * @param $key
     */
    public function replace($object, $key)
    {
        $this->remove($key);
        $this->add($object, $key);
    }

    /**
     * @return int number of elements in the collection
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return \Generator|\Traversable
     */
    public function getIterator()
    {
        foreach ($this->items as $key => $val) {
            yield $key => $val;
        }
    }

    /**
     * @param callable $comparator
     */
    public function sortBy(callable $comparator)
    {
        usort($this->items, $comparator);
    }

    /**
     * @param int $start
     * @param int $end
     */
    public function slice(int $start, int $end)
    {
        foreach ($this->items as $key => $val) {
            if ($key < $start || $key > $end) {
                $this->remove($key);
            }
        }
    }

    /**
     * @param int $number
     */
    public function reduceTo(int $number)
    {
        if (count($this->items) <= $number) {
            return;
        }

        foreach ($this->items as $key => $item){
            if($key > ($number - 1)){
                $this->remove($key);
            }
        }
    }
}
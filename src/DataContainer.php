<?php
namespace VolumNet\Parser;

use InvalidArgumentException;
use ArrayObject;

/**
 * Retriever for the site
 * @property array $data Stored data
 */
class DataContainer
{
    /**
     * Stored categories
     * @var ArrayObject<Category>
     */
    protected $categories;

    /**
     * Stored items
     * @var ArrayObject<Item>
     */
    protected $items;

    /**
     * Stored images
     * @var ArrayObject<Image>
     */
    protected $images;

    public function __get($var)
    {
        switch ($var) {
            case 'data':
                return $this->getData();
                break;
            case 'categories':
            case 'items':
            case 'images':
                return $this->$var;
                break;
            case 'unprocessedCategories':
            case 'unprocessedItems':
            case 'unprocessedImages':
                $key = str_replace('unprocessed', '', $var);
                $key = mb_strtolower($key);
                return array_filter(
                    $this->$key,
                    function ($x) {
                        return !$x->completed;
                    }
                );
                break;
            case 'processedCategories':
            case 'processedItems':
            case 'processedImages':
                $key = str_replace('processed', '', $var);
                $key = mb_strtolower($key);
                return array_filter(
                    $this->$key,
                    function ($x) {
                        return !$x->completed;
                    }
                );
                break;
        }
    }


    public function __set($var, $val)
    {
        switch ($var) {
            case 'data':
                return $this->setData($val);
                break;
        }
    }

    /**
     * Constructor
     * @param string $filename Filename to open
     */
    public function __construct($filename = null)
    {
        $this->categories = new ArrayObject();
        $this->items = new ArrayObject();
        $this->images = new ArrayObject();
        if ($filename) {
            $this->loadFromFile($filename);
        }
    }


    /**
     * Getting data
     * @return array
     */
    public function getData()
    {
        $data = [];
        foreach ($this->categories as $key => $cat) {
            $data['categories'][$key] = (array)$cat;
        }
        foreach ($this->items as $key => $item) {
            $data['items'][$key] = (array)$item;
        }
        foreach ($this->images as $key => $image) {
            $data['images'][$key] = (array)$image;
        }
    }


    /**
     * Setting data
     * @param array $data Data to set
     */
    public function setData(array $data)
    {
        if (isset($data['categories'])) {
            $this->categories = new ArrayObject();
            foreach ((array)$data['categories'] as $key => $category) {
                $cat = new Category();
                foreach ((array)$category as $key2 => $val2) {
                    $cat->$key2 = $val2;
                }
                $this->categories[$key] = $cat;
            }
        }
        if (isset($data['items'])) {
            $this->items = new ArrayObject();
            foreach ((array)$data['items'] as $key => $item) {
                $item = new Item();
                foreach ((array)$item as $key2 => $val2) {
                    $item->$key2 = $val2;
                }
                $this->items[$key] = $item;
            }
        }
        if (isset($data['images'])) {
            $this->images = new ArrayObject();
            foreach ((array)$data['images'] as $key => $image) {
                $image = new Image();
                foreach ((array)$image as $key2 => $val2) {
                    $image->$key2 = $val2;
                }
                $this->images[$key] = $image;
            }
        }
    }


    /**
     * Saving to file
     * @param string $filename Filename to save
     */
    public function saveToFile($filename)
    {
        if (is_file(dirname($filename))) {
            throw new InvalidArgumentException(dirname($filename) . ' already exists');
        }
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
        $json = json_encode($this->getData());
        file_put_contents($filename, $json);
    }


    /**
     * Loading from file
     * @param string $filename Filename to load
     */
    public function loadFromFile($filename)
    {
        if (!is_file($filename)) {
            throw new InvalidArgumentException($filename . ' doesn\'t exist');
        }
        $text = file_get_contents($filename);
        $json = json_decode($text, true);
        $this->setData($json);
    }
}
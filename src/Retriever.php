<?php
namespace VolumNet\Parser;

use InvalidArgumentException;

/**
 * Retriever for the site
 * @property array $data Stored data
 */
class Retriever
{
    /**
     * Instance of UrlRetriever
     * @var UrlRetriever
     */
    protected $urlRetriever;

    /**
     * Instance of Parser
     * @var Parser
     */
    protected $parser;

    /**
     * Stored categories
     * @var array<Category>
     */
    protected $categories = [];

    /**
     * Stored items
     * @var array<Item>
     */
    protected $items = [];

    /**
     * Stored images
     * @var array<Image>
     */
    protected $images = [];

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
     * @param UrlRetriever $urlRetriever URL retriever instance
     * @param Parser $parser Parser instance
     */
    public function __construct(UrlRetriever $urlRetriever, Parser $parser)
    {
        $this->urlRetriever = $urlRetriever;
        $this->parser = $parser;
    }


    /**
     * Getting data
     * @return array
     */
    public function getData()
    {
        return (object)[
            'categories' => $this->categories,
            'items' => $this->items,
            'images' => $this->images,
        ];
    }


    /**
     * Setting data
     * @param array $data Data to set
     */
    public function setData(array $data)
    {
        if (isset($data->categories)) {
            $this->categories = (array)$data->categories;
        }
        if (isset($data->items)) {
            $this->items = (array)$data->items;
        }
        if (isset($data->images)) {
            $this->images = (array)$data->images;
        }
    }


    /**
     * Retrieve menu from the category
     * @param string $url URL of the category
     * @param array|null $container Children container to put links
     * @return array parsed subcategories
     */
    public function retrieveMenu($url, array &$container = null)
    {
        $pq = $this->urlRetriever->getPhpQuery($url);
        $cats = $this->parser->parseCategories($pq);
        foreach ($cats as $row) {
            if (!isset($this->categories[$row->url])) {
                $this->categories[$row->url] = $row;
            }
            if ($container) {
                $container[] = $row->url;
            }
        }
        return $cats;
    }


    /**
     * Retrieve a set of items
     * @param string $url URL of the current page in the category
     * @param array|null $container Items container to put links
     * @return string|false URL of the next page in the category, or false if this is the last page
     */
    public function retrieveList($url, array &$container = null)
    {
        $pq = $this->urlRetriever->getPhpQuery($url);
        $items = $this->parser->parseList($pq);
        foreach ($items as $item) {
            if (!isset($this->items[$item->url])) {
                $this->items[$item->url] = $item;
            }
            if ($container) {
                $container[] = $row->url;
            }
        }
        $nextUrl = $this->parser->nextPage($pq);
        return $nextUrl;
    }


    /**
     * Retrieve opened item
     * @param string $url URL of the opened item page
     * @param Item|null $item Item to change
     * @return array Item data
     */
    public function retrieveItem($url, Item $item = null)
    {
        $pq = $this->urlRetriever->getPhpQuery($url);
        $item = $this->parser->parseOpenedPage($pq, $item);
        $this->items[$url] = $item;
        if ($item->categories) {
            foreach ($item->categories as $catUrl) {
                if (!isset($this->categories[$catUrl])) {
                    $this->categories[$catUrl] = new Category();
                }
                $this->categories[$catUrl]->items[] = $url;
                $this->categories[$catUrl]->items = array_unique(array_values($this->categories[$catUrl]->items));
            }
        }
        $this->items[$url] = array_merge($this->items[$url], $item, ['completed' => true]);
        return $item;
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

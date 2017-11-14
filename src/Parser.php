<?php
namespace VolumNet\Parser;

use \phpQueryObject;

abstract class Parser
{
    /**
     * Regular expression for the domain URL
     */
    const URL_RX = '/^http:\\/\\//domain.com/umi';

    /**
     * Canonical domain root URL
     */
    const CANONICAL_URL = 'http://domain.com';

    /**
     * Parsing categories menu
     * @param phpQueryObject $pq an instance of page
     * @return array<Category>
     */
    abstract public function parseCategories(phpQueryObject $pq);


    /**
     * Checking if there is the next page
     * @param phpQueryObject $pq an instance of page
     * @return string|false URL of the next page if present, false otherwise
     */
    abstract public function nextPage(phpQueryObject $pq);


    /**
     * Parsing list
     * @param phpQueryObject $pq an instance of page
     * @return array<Item>
     */
    abstract public function parseList(phpQueryObject $pq);


    /**
     * Parsing article
     * @param phpQueryObject $pq an instance of page
     * @param Item $item Item to update
     * @return Item
     */
    abstract public function parseOpenedPage(phpQueryObject $pq, Item $item = null);


    /**
     * Canonizes URL - adds leading domain name with the schema
     * @param string $url arbitrary URL
     * @return string Canonical URL
     */
    public function canonizeURL($url)
    {
        $url = trim($url);
        $url = preg_replace(static::URL_RX, static::CANONICAL_URL, $url);
        return $url;
    }


    /**
     * Canonizes texts - trims and removes repeating spaces
     * @param string $text arbitrary text
     * @return string Canonical text
     */
    public function canonizeText($text)
    {
        $text = trim($text);
        $text = preg_replace('/\\s+/umi', ' ', $text);
        return $text;
    }
}

<?php
namespace VolumNet\Parser;

use \phpQueryObject;
use \phpQuery;

class UrlRetriever
{
    /**
     * Get page as text
     * @param string $url URL to retrieve
     * @return string
     */
    public function getText($url)
    {
        $result = file_get_contents($url);
        return $result;
    }


    /**
     * Get page as JSON object
     * @param string $url URL to retrieve
     * @param boolean $forceArray force output as array
     * @return \stdObject|array|false
     */
    public function getJson($url, $forceArray = false)
    {
        $text = $this->getText($url);
        $result = false;
        if ($text) {
            $result = json_decode($text, $forceArray);
        }
        return $result;
    }


    /**
     * Get page as phpQuery
     * @param string $url
     * @return phpQueryObject|false
     */
    public function getPhpQuery($url)
    {
        $text = $this->getText($url);
        $result = false;
        if ($text) {
            $result = phpQuery::newDocument($text);
        }
        return $result;
    }
}

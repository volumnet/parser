<?php
namespace VolumNet\Parser;

/**
 * Parsing image class
 */
class Image
{
    /**
     * URL of the image
     * @var string
     */
    public $url;

    /**
     * Name of the image
     * @var string
     */
    public $name;


    /**
     * Output file
     * @var string
     */
    public $filename;

    /**
     * Is item completed
     * @var boolean
     */
    public $completed = false;
}

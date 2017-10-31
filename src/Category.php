<?php
namespace VolumNet\Parser;

/**
 * Parsing category class
 */
class Category
{
    /**
     * URL of the category
     * @var string
     */
    public $url;

    /**
     * Children categories urls
     * @var array<string>
     */
    public $children = [];

    /**
     * Category items
     * @var array<Item>
     */
    public $items = [];

    /**
     * Name of the category
     * @var string
     */
    public $name;

    /**
     * Images array
     * @var array<Image>
     */
    public $images = [];

    /**
     * Is item completed
     * @var boolean
     */
    public $completed = false;
}

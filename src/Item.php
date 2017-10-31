<?php
namespace VolumNet\Parser;

/**
 * Parsing item class
 */
class Item
{
    /**
     * URL of the item
     * @var string
     */
    public $url;

    /**
     * Name of the item
     * @var string
     */
    public $name;

    /**
     * Is item completed
     * @var boolean
     */
    public $completed = false;

    /**
     * Categories array (optional)
     * @var array|null
     */
    public $categories;

    /**
     * Images array
     * @var array<Image>
     */
    public $images = [];
}

<?php
/**
 * @package yoannisj/craft-articulate
 */

namespace yoannisj\articulate;
use Craft;
use craft\base\Plugin;


/**
 * 
**/

class Articulate extends Plugin
{    // =Static
    // =========================================================================

    // =Properties
    // =========================================================================

    /**
     * Static reference to the plugin's singleton instance
     * 
     * @var \yoannisj\articulate\Articulate
     */

    public static $plugin;

    // Public Methods
    // =========================================================================
    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();

        // Add static reference to plugin's singleton instance
        self::$plugin = $this;

    }

}
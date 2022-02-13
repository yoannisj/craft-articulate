<?php
/**
 * @package yoannisj/craft-articulate
 */

namespace yoannisj\articulate;

use yii\base\Event;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;

use yoannisj\articulate\fields\ArticulateFormField;

/**
 * 
**/

class Articulate extends Plugin
{
    // =Static
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

        // Register plugin's custom field(s)
        Event::on(
            Fields::class, // The class that triggers the event
            Fields::EVENT_REGISTER_FIELD_TYPES, // the name of the event
            function(RegisterComponentTypesEvent $event) // the callback function
            {
                $event->types[] = ArticulateFormField::class;
            }
        );
    }

}
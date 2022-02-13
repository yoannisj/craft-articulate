<?php
/**
 * 
 */

use Craft;
use craft\base\Field;
use craft\base\ElementInterface;

/**
 * 
 */

class ArticulateFormField extends Field
{
    // =Static
    // =========================================================================

    /**
     * @inheritdoc
     */

    public static function displayName(): string
    {
        return Craft::t('articulate', "Articulate Form Field");
    }

    // =Properties
    // =========================================================================

    // =Public Methods
    // =========================================================================

    // =Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */

    protected function inputHtml( $value, ElementInterface $element = null )
    {
        return '<p>Hello Articulate Form Field input '.$this->handle.'!</p>';
    }
}
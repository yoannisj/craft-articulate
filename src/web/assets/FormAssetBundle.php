<?php
/**
 * @package yoannisj/craft-articulate
 */

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset as CraftCpAsset;

/**
 * 
**/

class FormAssetBundle extends AssetBundle
{
    // =Static
    // =========================================================================

    // =Properties
    // =========================================================================

    /**
     * 
     */

    public $depends = [
        CraftCpAsset::class,
    ];

    /**
     * @inheritdoc
     */

    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@yoannisj/articulate/web/assets/src";

        $this->css = [
            'css/form.css',
        ];

        $this->js = [
            'js/helpers/assertions.js',
            'js/form/adapters/base-field-adapter.js',
            'js/form/adapters/relation-field-adapter.js',
            'js/form/adapters/selectize-field-adapter.js',
            'js/form/form-rule.js',
            'js/form.js',
        ];

    }
}
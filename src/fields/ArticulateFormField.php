<?php
/**
 * @package yoannisj\craft-articulate
 */

namespace yoannisj\articulate\fields;

use yii\base\InvalidConfigException;

use Craft;
use craft\base\Field;
use craft\base\ElementInterface;
use craft\web\View;
use craft\helpers\Html as HtmlHelper;

use yoannisj\articulate\Articulate;
use yoannisj\articulate\models\FormConfig;
use yoannisj\articulate\web\assets\FormAssetBundle;

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

    /**
     * @inheritdoc
     */

    public static function hasContentColumn(): bool
    {
        return false;
    }

    // =Properties
    // =========================================================================

    /**
     * @var FormConfig Memoized reference to field's FormConfig model
     */

    private $_formConfig;

    // =Public Methods
    // =========================================================================

    /**
     * Getter method for memoized and read-only `formRules` property
     * 
     * @return FormConfig
     */

    public function getFormConfig(): FormConfig
    {
        if (!isset($this->_formConfig))
        {
            $configPath = 'articulate/'.$this->handle;
            $config = Craft::$app->getConfig()->getConfigFromFile($configPath);

            $this->_formConfig = new FormConfig($config);
        }

        return $this->_formConfig;
    }

    // =Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */

    protected function inputHtml( $value, ElementInterface $element = null ): string
    {
        $config = $this->getFormConfig();
        $rules = $config->getRules();

        $ruleConditions = [];
        foreach ($rules as $rule)
        {
            $ruleField = $rule->getField();
            $ruleConditions[$ruleField->handle] = $rule->when->toArray();
        }

        // Craft::dd($ruleConditions);
        // Craft::dd($config->getFieldsMap());

        $craftView = Craft::$app->getView();
        $craftView->registerAssetBundle(FormAssetBundle::class, View::POS_END);

        $html = '';

        if (!empty($config->description)) {
            $html .= '<p>'.$config->description.'</p>';
        }

        return $html;
    }
}
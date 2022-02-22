<?php
/**
 * @package yoannisj/craft-articulate
 */

namespace yoannisj\articulate\models;

use yii\base\InvalidConfigException;

use Craft;
use craft\base\Model;

use yoannisj\articulate\models\FormRule;

/**
 * 
 */

class FormConfig extends Model
{
    // =Static
    // =========================================================================

    // =Properties
    // =========================================================================

    /**
     * @var string
     */

    public $description;

    /**
     * @var FormRule[]
     */

    private $_rules;

    /**
     * @var bool Whether `rules` property has been normalized or not
     */

    protected $isNormalizedRules = false;

    // =Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */

    public function init()
    {
        if (empty($this->_rules))
        {
            throw new InvalidConfigException(
                "Missing required Form Config property `rules`");
        }

        parent::init();
    }

    /**
     * @param array $rules
     */

    public function setRules( array $rules )
    {
        $this->_rules = $rules;
        $this->isNormalizedRules = false;
    }

    /**
     * 
     */

    public function getRules()
    {
        if ($this->isNormalizedRules) {
            return $this->_rules;
        }

        $rules = [];

        foreach ($this->_rules as $rule)
        {
            if (is_array($rule))
            {
                if (!array_key_exists('class', $rule)) {
                    $rule['class'] = FormRule::class;
                }

                $rule = Craft::createObject($rule);
            }

            if (!($rule instanceof FormRule))
            {
                throw new InvalidConfigException(
                    "All items in FormConfig's `rules` must be instances of ".FormRule::class);
            }

            $rules[] = $rule;
        }

        $this->_rules = $rules;
        $this->isNormalizedRules = true;

        return $rules;
    }

    /**
     * Getter method for computed `fieldsConfig` property
     * 
     * @return array
     */

    public function getFieldsMap(): array
    {
        $fieldsMap = [];

        // collect list of field handles based on form rules
        foreach ($this->rules as $rule)
        {
            // make sure rule's own field is included
            $ruleMap = $rule->getFieldMap();
            $path = $ruleMap['path'];

            if (!array_key_exists($path, $fieldsMap)) {
                $fieldsMap[$path] = $ruleMap;
            }

            // make sure fields from rule's conditions are included
            $ruleConditionMap = $rule->getWhen()->getFieldsMap();

            foreach ($ruleConditionMap as $fieldPath => $fieldMap)
            {
                if (!array_key_exists($fieldPath, $fieldsMap)) {
                    $fieldsMap[$fieldPath] = $fieldMap;
                }
            }
        }

        return $fieldsMap;
    }

    /**
     * 
     */

    public function getFieldsConfig()
    {
        $map = $this->getFieldsMap();
        $config = [];

    }

    // =Protected Methods
    // =========================================================================

}
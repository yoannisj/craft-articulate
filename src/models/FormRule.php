<?php
/**
 * @package yoannisj/craft-articulate
 */

namespace yoannisj\articulate\models;

use yii\base\InvalidConfigException;

use Craft;
use craft\base\Model;
use craft\base\FieldInterface;

use yoannisj\articulate\Articulate;
use yoannisj\articulate\base\FormRuleInterface;
use yoannisj\articulate\models\FormCondition;
use yoannisj\articulate\helpers\FormRuleHelper;

/**
 * Represents a rule applied to a form to make it more dynamic and react
 * to other field values in the same form
 */

class FormRule extends Model implements FormRuleInterface
{
    // =Static Methods
    // =========================================================================

    // =Properties Methods
    // =========================================================================

    /**
     * @var string Private property to store rule's `effect` setting
     */

    private $_effect;

    /**
     * @var string Private property to store rule's `fieldPath` setting
     */

    private $_fieldPath;

    /**
     * @var array Private property to store rule's `fieldMap` setting
     */

    private $_fieldMap;

    /**
     * @var FieldInterface Private property to store rule's `field` setting
     */

    private $_field;

    /**
     * @var mixed Field value the rule's effect applies to (for option fields)
     */

    public $value;

    /**
     * @var mixed Field value to fallback to when hiding or disabling current value
     */

    public $fallback;

    /**
     * @var FormCondition
     */

    private $_when;

    /**
     * @var boolan
     */

    public $isNormalizedWhen;

    // =Public Methods
    // =========================================================================

    /**
     * @param string|null $effect
     */

    public function setEffect( string $effect = null )
    {
        $this->_effect = $effect;
    }

    /**
     * @return string|null
     */

    public function getEffect(): string
    {
        return $this->_effect;
    }

    /**
     * @param string|FieldInterface $field Field this rule applies to
     */

    public function setField( $field )
    {
        if (is_string($field))
        {
            $fieldMap = FormRuleHelper::parseFieldPath($field);

            $this->_fieldMap = $fieldMap;
            $this->_fieldPath = $fieldMap['path'];
            $this->_field = null;
        }

        else if ($field instanceof FieldInterface)
        {
            $fieldMap = [
                'path' => $field->handle,
                'handle' => $field->handle,
                'type' => get_class($field),
                'parentHandle' => null,
                'blockType' => null,
            ];

            $this->_fieldMap = $fieldMap;
            $this->_fieldPath = $fieldMap['path'];
            $this->_field = $field;
        }

        else {
            throw new InvalidConfigException(
                "Form Rule's `field` attribute must be a string or FieldInterface instance");
        }
    }

    /**
     * @return array
     */

    public function getFieldMap(): array
    {
        if (!isset($this->_fieldMap))
        {
            throw new InvalidConfigException(
                "Missing required Form Rule's `field` attribute");
        }

        return $this->_fieldMap;
    }

    /**
     * @return string
     */

    public function getFieldPath(): string
    {
        if (!isset($this->_fieldPath))
        {
            $fieldMap = $this->getFieldMap();
            $this->_fieldPath = $fieldMap['path'];
        }

        return $this->_fieldPath;
    }

    /**
     * @return FieldInterface
     */

    public function getField(): FieldInterface
    {
        if (!isset($this->_field))
        {
            $fieldMap = $this->getFieldMap();
            $fieldHandle = $fieldMap['handle'];

            $field = Craft::$app->getFields()
                ->getFieldByHandle($fieldHandle);

            if (!$field)
            {
                throw new InvalidConfigException(
                    "Could not find Form Rule field with handle '$fieldHandle'");
            }

            $this->_field = $field;
        }

        return $this->_field;
    }

    /**
     * @return string|array\null
     */

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string|null
     */

    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * @param array|FormCondition|null $when
     */

    public function setWhen( $when )
    {
        $this->_when = $when;
    }

    /**
     * @return FormCondition
     */

    public function getWhen(): FormCondition
    {
        if ($this->isNormalizedWhen) {
            return $this->_when;
        }

        $when = FormRuleHelper::parseCondition($this->_when);

        $this->isNormalizedWhen = true;
        $this->_when = $when;

        return $when;
    }

    // =Validation
    // -------------------------------------------------------------------------

    // =Exporting
    // ------------------------------------------------------------------------

    /**
     * @inheritdoc
     */

    public function fields()
    {
        $fields = parent::fields();

        $fields[] = 'fieldMap';
        $fields[] = 'when';

        return $fields;
    }

    /**
     * @inheritdoc
     */

    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'fieldPath';
        $fields[] = 'field';

        return $fields;
    }

    // =Protected Methods
    // =========================================================================

}
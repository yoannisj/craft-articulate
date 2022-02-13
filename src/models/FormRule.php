<?php
/**
 * @package yoannisj/craft-articulate
 */

namespace yoannisj\articulate\models;

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
     * @var string Private property to store rule's `field` setting
     */

    private $_field;

    /**
     * @var mixed Field value the rule's effect applies to (for option fields)
     */

    public $value;

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
        $this->_field = $field;
    }

    /**
     * @return FieldInterface
     */

    public function getField(): FieldInterface
    {
        if (is_string($this->_field))
        {
            $field = Craft::$app->getFields()
                ->getFieldByHandle($this->_field);

            if (!$field) {
                throw new InvalidConfigException("Could not find form rule field with handle '".$this->_field."'");
            }

            $this->_field = $field;
        }

        return $this->_field;
    }

    /**
     * @return mixed
     */

    public function getValue()
    {
        return $this->value;
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

    // =Protected Methods
    // =========================================================================

}
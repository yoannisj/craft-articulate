<?php
/**
 * @package yoannisj/craft-articulate
 */

namespace yoannisj\articulate\models;

use yii\base\InvalidConfigException;

use Craft;
use craft\base\Model;
use craft\base\FieldInterface;

/**
 * Represents an condition to verify when evaluating form rules
 */

class FormCondition extends Model
{
    // =Static
    // =========================================================================

    const OPERATOR_AND = 'and';
    const OPERATOR_OR = 'or';

    const TEST_EMPTY = 'empty';
    const TEST_EQUAL = 'equal';
    const TEST_NOT_EQUAL = 'notEqual';

    const SUPPORTED_TESTS = [
        'empty', 'equal', 'notEqual',
    ];

    // =Properties
    // =========================================================================

    /**
     * @var bool Whether condition is null (i.e. always resolves to true)
     */

    public $isNull;

    /**
     * @var string Conditional operator used to evaluate nested conditions 
     */

    public $operator;

    /**
     * @var FormCondition[] If conditions are empty, conditional will always
     *  resolve to `true`
     */

    public $subConditions;

    /**
     * @var string
     */

    public $subject;

    /**
     * @var string
     */

    public $test;

    /**
     * @var mixed
     */

    public $given;

    // =Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */

    public function init()
    {
        if (isset($this->operator)) {
            $this->isNull = $this->isNull ?? empty($this->subConditions);
        }

        if ($this->isNull)
        {
            $this->test = null;
            $this->given = null;
            $this->value = null;
        }

        else if (!$this->operator)
        {
            if (!isset($this->subject))
            {
                Craft::dd($this->toArray());

                throw new InvalidConfigException(
                    "Missing required FormCondition property `subject`");
            }

            if (!isset($this->test)) {
                throw new InvalidConfigException(
                    "Missing required FormCondition property `test`");
            }

            else if (!in_array($this->test, self::SUPPORTED_TESTS))
            {
                throw new InvalidConfigException(
                    "Unknown FormCondition test '".$test."'");
            }

            if ($this->test != self::TEST_EMPTY && !isset($this->given))
            {
                throw new InvalidConfigException(
                    "Missing FormCondition property `given`, which is"
                    ." required for `test` '".$this->test."'");
            }
        }

        parent::init();
    }

    /**
     * @return FieldInterface[]
     */

    public function getFieldsMap(): array
    {
        // @todo: support field paths for nested block fields

        $fields = [];

        if ($this->operator)
        {
            foreach ($this->subConditions as $subCondition)
            {
                $subFields = $subCondition->getFieldsMap();

                foreach ($subFields as $fieldHandle => $field)
                {
                    if (!array_key_exists($fieldHandle, $fields)) {
                        $fields[$fieldHandle] = $field;
                    }
                }
            }

            return $fields;
        }

        $fieldHandle = $this->subject;
        $field = Craft::$app->getFields()->getFieldByHandle($fieldHandle);

        $fields[$fieldHandle] = $field;

        return $fields;
    }

    // =Validation
    // -------------------------------------------------------------------------

}
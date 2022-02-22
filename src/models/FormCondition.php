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
use yoannisj\articulate\helpers\FormRuleHelper;

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
     * @var array
     */

    private $_subjectMap;

    /**
     * @var string
     */

    private $_subjectPath;

    /**
     * @var FieldInterface
     */

    private $_subject;

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
        }

        else if (!$this->operator)
        {
            if (!isset($this->subject))
            {
                throw new InvalidConfigException(
                    "Missing required FormCondition property `subject`");
            }

            if (!isset($this->test))
            {
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
     * @return array
     */

    public function getFieldsMap(): array
    {
        if ($this->isNull) return [];

        $fieldsMap = [];

        if ($this->operator)
        {
            foreach ($this->subConditions as $subCondition)
            {
                $conditionMap = $subCondition->getFieldsMap();

                foreach ($conditionMap as $fieldPath => $fieldMap)
                {
                    if (!array_key_exists($fieldPath, $fieldsMap)) {
                        $fieldsMap[$fieldPath] = $fieldMap;
                    }
                }
            }

            return $fieldsMap;
        }

        $fieldMap = $this->getSubjectMap();
        $fieldPath = $fieldMap['path'];

        $fieldsMap[$fieldPath] = $fieldMap;

        return $fieldsMap;
    }

    /**
     * 
     */

    public function setSubject( $subject )
    {
        if (is_string($subject))
        {
            $subjectMap = FormRuleHelper::parseFieldPath($subject);

            $this->_subjectMap = $subjectMap;
            $this->_subjectPath = $subjectMap['path'];
            $this->_subject = null;
        }

        else if ($subject instanceof FieldInterface)
        {
            $subjectMap = [
                'path' => $subject->handle,
                'handle' => $subject->handle,
                'type' => get_class($subject),
                'parentHandle' => null,
                'blockType' => null,
            ];

            $this->_subjectMap = $subjectMap;
            $this->_subjectPath = $subjectMap['path'];
            $this->_subject = $subject;
        }

        else {
            throw new InvalidConfigException(
                "Form Rule's `subject` attribute must be a string or FieldInterface instance");
        }
    }

    /**
     * @return array|null
     */

    public function getSubjectMap()
    {
        if ($this->isNull) return null;

        if (!isset($this->_subjectMap))
        {
            throw new InvalidConfigException(
                "Missing required Form Rule's `subject` attribute");
        }

        return $this->_subjectMap;
    }

    /**
     * 
     */

    public function getSubjectPath(): string
    {
        if (!isset($this->_subjectPath))
        {
            $subjectMap = $this->getSubjectMap();
            $this->_subjectPath = $subjectMap['path'];
        }

        return $this->_subjectPath;
    }

    /**
     * @return FieldInterface|null
     */

    public function getSubject()
    {
        if ($this->isNull) return null;

        if (!isset($this->_subject))
        {
            $subjectMap = $this->getSubjectMap();
            $subjectHandle = $subjectMap['handle'];

            $subject = Craft::$app->getFields()
                ->getFieldByHandle($subjectHandle);

            if (!$subject)
            {
                throw new InvalidConfigException(
                    "Could not find Form Rule field with handle '$subjectHandle'");
            }

            $this->_subject = $subject;
        }

        return $this->_subject;
    }

    // =Validation
    // -------------------------------------------------------------------------

    // =Exporting
    // -------------------------------------------------------------------------

    /**
     * @inheritdoc
     */

    public function fields()
    {
        $fields = parent::fields();

        $fields[] = 'subjectMap';

        return $fields;
    }

    /**
     * @inheritdoc
     */

    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'subjectPath';
        $fields[] = 'subject';

        return $fields;
    }

    // =Protected Methods
    // =========================================================================

}
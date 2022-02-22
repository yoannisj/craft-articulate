<?php
/**
 * @package yoannisj/craft-articulate
 */

namespace yoannisj\articulate\helpers;

use yii\base\InvalidArgumentException;

use Craft;
use craft\helpers\ArrayHelper;

use yoannisj\articulate\models\FormCondition;

/**
 * 
 */

class FormRuleHelper
{
    /**
     * @param string $path
     * 
     * @return array
     */

    public static function parseFieldPath( string $path ): array
    {
        $fieldPath = $path;
        $handle = $fieldPath;
        $parentHandle = null;
        $blockType = null;

        /* @todo: use regex matching to deconstruct field path */
        if (strpos($handle, '.'))
        {
            $parts = explode('.', $handle);
            $handle = $parts[1];
            $parentHandle = $parts[0];
        }

        if (strpos($handle, ':'))
        {
            $parts = explode(':', $handle);
            $handle = $parts[1];
            $blockType = $parts[0];
        }
    
        $field = Craft::$app->getFields()
            ->getFieldByHandle($handle);

        return [
            'path' => $fieldPath,
            'handle' => $handle,
            'type' => $field ? get_class($field) : null,
            'parentHandle' => $parentHandle,
            'blockType' => $blockType,
        ];
    }

    /**
     * @return FormCondition
     */

    public static function parseCondition( $condition )
    {
        if ($condition instanceof FormCondition) {
            return $condition;
        }

        if (!$condition || empty($condition))
        {
            return new FormCondition([
                'isNull' => true,
            ]);
        }

        if (!is_array($condition))
        {
            throw new InvalidArgumentException(
                "Form conditional expression must be an array");
        }

        // support defining FormConditional as an associative array of properties
        if (ArrayHelper::isAssociative($condition)) {
            return new FormCondition($condition);
        }

        $operator = null;
        $subConditions = null;

        $count = count($condition);
        $firstToken = $condition[0];

        if (is_array($firstToken))
        {
            if ($count == 1) {
                return static::parseCondition($firstToken);
            }

            else
            {
                // add default operator at start of condition
                $firstToken = FormCondition::OPERATOR_AND;
                $count = array_unshift($condition, $firstToken);
            }
        }

        if ($firstToken == FormCondition::OPERATOR_AND
            || $firstToken == FormCondition::OPERATOR_OR)
        {
            if ($count < 3) {
                throw new InvalidArgumentException(
                    "Conditional operators require at least 2 more conditions");
            }

            $operator = $firstToken;
            $subConditions = [];

            for ($i = 1; $i < $count; $i++) {
                $subConditions[] = static::parseCondition($condition[$i]);
            }

            return new FormCondition([
                'operator' => $operator,
                'subConditions' => $subConditions,
            ]);
        }

        else if (!is_string($firstToken))
        {
            throw new InvalidArgumentException(
                "First token in condition must be a string");
        }

        if ($count < 2) {
            throw new InvalidArgumentException(
                "Condition can not include less than 2 tokens");
        }

        if ($count > 3) {
            throw new InvalidArgumentException(
                "Condition can not include more than 3 tokens");
        }

        return new FormCondition([
            'subject' => $firstToken,
            'test' => $condition[1],
            'given' => ($condition[2] ?? null),
        ]);
    }

}
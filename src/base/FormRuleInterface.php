<?php
/**
 * @package yoannisj/craft-articulate
 */

namespace yoannisj\articulate\base;

use craft\base\FieldInterface;
use yoannisj\articulate\models\FormCondition;

/**
 * Interface implemented by form rule models
 */

interface FormRuleInterface
{
    // =Public Methods
    // =========================================================================

    /**
     * @return string
     */

    public function getEffect(): string;

    /**
     * @return array
     */

    public function getFieldMap(): array;

    /**
     * @return string
     */

    public function getFieldPath(): string;

    /**
     * @return FieldInterface
     */

    public function getField(): FieldInterface;

    /**
     * @return string|array|null
     */

    public function getValue();

    /**
     * @return mixed
     */

    public function getFallback();

    /**
     * @return FormCondition;
     */

    public function getWhen(): FormCondition;
}
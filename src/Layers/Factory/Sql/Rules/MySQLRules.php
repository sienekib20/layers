<?php

namespace Sienekib\Layers\Factory\Sql\Rules;

use Spatie\Ignition\Ignition;

/**
 * Class MySQLRules
 * @package Sienekib\Layers\Rules
 *
 * This class is responsible for validating the data before SQL operations.
 */
class MySQLRules
{
    public function validateInsertFields(array $fields): void
    {
        foreach ($fields as $field => $value) {
            if (empty($field)) {
                try {

                    throw new \Exception("Field name cannot be empty.");
                } catch (\Exception $e) {
                    Ignition::make()->renderException($e);
                }
            }

            if (is_null($value)) {
                try {

                    throw new \Exception("Field '{$field}' cannot be null.");
                } catch (\Exception $e) {
                    Ignition::make()->renderException($e);
                }
            }
        }
    }

    public function validateUpdateFields(array $fields): void
    {
        foreach ($fields as $field => $value) {
            if (empty($field)) {
                try {

                    throw new \Exception("Field name cannot be empty.");
                } catch (\Exception $e) {
                    Ignition::make()->renderException($e);
                }
            }
        }
    }

    public function validateCondition(string $condition): void
    {
        if (empty($condition)) {
            try {

                throw new \Exception("Condition cannot be empty.");
            } catch (\Exception $e) {
                Ignition::make()->renderException($e);
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\DirectiveResolvers;

use PoP\ComponentModel\Feedback\Tokens;

/**
 * Apply a transformation to the string
 */
abstract class AbstractTransformFieldStringValueDirectiveResolver extends AbstractTransformFieldValueDirectiveResolver
{
    protected function validateTypeIsString(mixed $value, string | int $id, string $field, string $fieldOutputKey, array &$objectErrors, array &$objectWarnings)
    {
        if (!is_string($value)) {
            $objectWarnings[(string)$id][] = [
                Tokens::PATH => [$this->directive],
                Tokens::MESSAGE => sprintf(
                    $this->getTranslationAPI()->__('Directive \'%s\' from field \'%s\' cannot be applied on object with ID \'%s\' because it is not a string', 'practical-directives'),
                    $this->getDirectiveName(),
                    $fieldOutputKey,
                    $id
                ),
            ];
            return false;
        }
        return true;
    }
}

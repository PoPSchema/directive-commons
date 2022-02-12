<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\DirectiveResolvers;

use PoP\ComponentModel\Component as ComponentModelComponent;
use PoP\ComponentModel\ComponentConfiguration as ComponentModelComponentConfiguration;
use PoP\ComponentModel\Feedback\Tokens;
use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;
use PoP\Root\App;

/**
 * Apply a transformation to the string
 */
abstract class AbstractTransformFieldStringValueDirectiveResolver extends AbstractTransformFieldValueDirectiveResolver
{
    final protected function transformValue(
        mixed $value,
        string | int $id,
        string $field,
        string $fieldOutputKey,
        RelationalTypeResolverInterface $relationalTypeResolver,
        array &$succeedingPipelineIDsDataFields,
        array &$variables,
        array &$messages,
        array &$objectErrors,
        array &$objectWarnings,
        array &$objectDeprecations,
        array &$schemaErrors,
        array &$schemaWarnings,
        array &$schemaDeprecations
    ): mixed {
        // null => Nothing to do
        if ($value === null) {
            return null;
        }

        /**
         * Validate it is a string
         */
        if (!is_string($value)) {
            return $this->handleNonStringValue(
                $value,
                $id,
                $field,
                $fieldOutputKey,
                $succeedingPipelineIDsDataFields,
                $objectErrors,
                $objectWarnings
            );
        }

        /** @var string $value */
        return $this->transformStringValue(
            $value,
            $id,
            $field,
            $fieldOutputKey,
            $relationalTypeResolver,
            $variables,
            $messages,
            $objectErrors,
            $objectWarnings,
            $objectDeprecations,
            $schemaErrors,
            $schemaWarnings,
            $schemaDeprecations,
        );
    }

    abstract protected function transformStringValue(string $value, string | int $id, string $field, string $fieldOutputKey, RelationalTypeResolverInterface $relationalTypeResolver, array &$variables, array &$messages, array &$objectErrors, array &$objectWarnings, array &$objectDeprecations, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations): string;

    protected function handleNonStringValue(
        mixed $value,
        string | int $id,
        string $field,
        string $fieldOutputKey,
        array &$succeedingPipelineIDsDataFields,
        array &$objectErrors,
        array &$objectWarnings
    ): mixed {
        /** @var ComponentModelComponentConfiguration */
        $componentConfiguration = App::getComponent(ComponentModelComponent::class)->getConfiguration();
        $removeFieldIfDirectiveFailed = $componentConfiguration->removeFieldIfDirectiveFailed();
        if ($removeFieldIfDirectiveFailed) {
            $idsDataFieldsToRemove = [];
            $idsDataFieldsToRemove[(string)$id]['direct'][] = $field;
            $this->removeIDsDataFields(
                $idsDataFieldsToRemove,
                $succeedingPipelineIDsDataFields
            );
        }

        $errorMessage = sprintf(
            $this->__('Directive \'%s\' from field \'%s\' cannot be applied on object with ID \'%s\' because it is not a string', 'practical-directives'),
            $this->getDirectiveName(),
            $fieldOutputKey,
            $id
        );
        $setFailingFieldResponseAsNull = $componentConfiguration->setFailingFieldResponseAsNull();
        if ($setFailingFieldResponseAsNull) {
            $objectErrors[(string)$id][] = [
                Tokens::PATH => [$this->directive],
                Tokens::MESSAGE => $errorMessage,
            ];
            return null;
        }
        $objectWarnings[(string)$id][] = [
            Tokens::PATH => [$this->directive],
            Tokens::MESSAGE => $errorMessage,
        ];
        return $value;
    }
}

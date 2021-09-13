<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\DirectiveResolvers;

use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;
use PoP\ComponentModel\DirectiveResolvers\AbstractDirectiveResolver;

/**
 * Apply a transformation to the string
 */
abstract class AbstractTransformFieldValueDirectiveResolver extends AbstractDirectiveResolver
{
    public function resolveDirective(
        RelationalTypeResolverInterface $relationalTypeResolver,
        array &$idsDataFields,
        array &$succeedingPipelineIDsDataFields,
        array &$succeedingPipelineDirectiveResolverInstances,
        array &$objectIDItems,
        array &$unionDBKeyIDs,
        array &$dbItems,
        array &$previousDBItems,
        array &$variables,
        array &$messages,
        array &$objectErrors,
        array &$objectWarnings,
        array &$objectDeprecations,
        array &$objectNotices,
        array &$objectTraces,
        array &$schemaErrors,
        array &$schemaWarnings,
        array &$schemaDeprecations,
        array &$schemaNotices,
        array &$schemaTraces
    ): void {
        foreach ($idsDataFields as $id => $dataFields) {
            foreach ($dataFields['direct'] as $field) {
                $fieldOutputKey = $this->fieldQueryInterpreter->getUniqueFieldOutputKey($relationalTypeResolver, $field);
                $dbItems[(string)$id][$fieldOutputKey] = $this->transformValue(
                    $dbItems[(string)$id][$fieldOutputKey],
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
                    $schemaDeprecations
                );
            }
        }
    }

    abstract protected function transformValue($value, $id, string $field, string $fieldOutputKey, RelationalTypeResolverInterface $relationalTypeResolver, array &$variables, array &$messages, array &$objectErrors, array &$objectWarnings, array &$objectDeprecations, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations);
}

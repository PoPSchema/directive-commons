<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\DirectiveResolvers;

use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;
use PoP\ComponentModel\DirectiveResolvers\AbstractSchemaDirectiveResolver;

/**
 * Apply a transformation to the string
 */
abstract class AbstractTransformFieldValueDirectiveResolver extends AbstractSchemaDirectiveResolver
{
    public function resolveDirective(
        RelationalTypeResolverInterface $relationalTypeResolver,
        array &$idsDataFields,
        array &$succeedingPipelineIDsDataFields,
        array &$succeedingPipelineDirectiveResolverInstances,
        array &$resultIDItems,
        array &$unionDBKeyIDs,
        array &$dbItems,
        array &$previousDBItems,
        array &$variables,
        array &$messages,
        array &$dbErrors,
        array &$dbWarnings,
        array &$dbDeprecations,
        array &$dbNotices,
        array &$dbTraces,
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
                    $dbErrors,
                    $dbWarnings,
                    $dbDeprecations,
                    $schemaErrors,
                    $schemaWarnings,
                    $schemaDeprecations
                );
            }
        }
    }

    abstract protected function transformValue($value, $id, string $field, string $fieldOutputKey, RelationalTypeResolverInterface $relationalTypeResolver, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$dbDeprecations, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations);
}

<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\DirectiveResolvers;

use PoP\ComponentModel\DirectiveResolvers\AbstractDirectiveResolver;
use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;

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
            $object = $objectIDItems[$id];
            foreach ($dataFields['direct'] as $field) {
                $fieldOutputKey = $this->getFieldQueryInterpreter()->getUniqueFieldOutputKey($relationalTypeResolver, $field, $object);
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

    abstract protected function transformValue(mixed $value, string | int $id, string $field, string $fieldOutputKey, RelationalTypeResolverInterface $relationalTypeResolver, array &$variables, array &$messages, array &$objectErrors, array &$objectWarnings, array &$objectDeprecations, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations);
}

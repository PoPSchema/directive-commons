<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\DirectiveResolvers;

use PoP\ComponentModel\DirectiveResolvers\AbstractDirectiveResolver;
use PoP\ComponentModel\Engine\EngineIterationFieldSet;
use PoP\ComponentModel\Feedback\EngineIterationFeedbackStore;
use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;
use PoP\GraphQLParser\Spec\Parser\Ast\FieldInterface;

/**
 * Apply a transformation to the string
 */
abstract class AbstractTransformFieldValueDirectiveResolver extends AbstractDirectiveResolver
{
    /**
     * @param array<string|int,EngineIterationFieldSet> $idsDataFields
     * @param array<array<string|int,EngineIterationFieldSet>> $succeedingPipelineIDsDataFields
     */
    public function resolveDirective(
        RelationalTypeResolverInterface $relationalTypeResolver,
        array $idsDataFields,
        array $succeedingPipelineDirectiveResolverInstances,
        array $objectIDItems,
        array $unionDBKeyIDs,
        array $previousDBItems,
        array &$succeedingPipelineIDsDataFields,
        array &$dbItems,
        array &$variables,
        array &$messages,
        EngineIterationFeedbackStore $engineIterationFeedbackStore,
    ): void {
        foreach ($idsDataFields as $id => $dataFields) {
            foreach ($dataFields->direct as $field) {
                $fieldOutputKey = $field->getOutputKey();
                $dbItems[(string)$id][$fieldOutputKey] = $this->transformValue(
                    $dbItems[(string)$id][$fieldOutputKey],
                    $id,
                    $field,
                    $fieldOutputKey,
                    $relationalTypeResolver,
                    $succeedingPipelineIDsDataFields,
                    $variables,
                    $messages,
                    $engineIterationFeedbackStore,
                );
            }
        }
    }

    abstract protected function transformValue(
        mixed $value,
        string | int $id,
        FieldInterface $field,
        string $fieldOutputKey,
        RelationalTypeResolverInterface $relationalTypeResolver,
        array &$succeedingPipelineIDsDataFields,
        array &$variables,
        array &$messages,
        EngineIterationFeedbackStore $engineIterationFeedbackStore,
    ): mixed;
}

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
     * @param array<string|int,EngineIterationFieldSet> $idFieldSet
     * @param array<array<string|int,EngineIterationFieldSet>> $succeedingPipelineIDFieldSet
     */
    public function resolveDirective(
        RelationalTypeResolverInterface $relationalTypeResolver,
        array $idFieldSet,
        array $succeedingPipelineDirectiveResolverInstances,
        array $objectIDItems,
        array $unionDBKeyIDs,
        array $previousDBItems,
        array &$succeedingPipelineIDFieldSet,
        array &$dbItems,
        array &$variables,
        array &$messages,
        EngineIterationFeedbackStore $engineIterationFeedbackStore,
    ): void {
        foreach ($idFieldSet as $id => $fieldSet) {
            foreach ($fieldSet->fields as $field) {
                $fieldOutputKey = $field->getOutputKey();
                $dbItems[$id][$fieldOutputKey] = $this->transformValue(
                    $dbItems[$id][$fieldOutputKey],
                    $id,
                    $field,
                    $fieldOutputKey,
                    $relationalTypeResolver,
                    $succeedingPipelineIDFieldSet,
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
        array &$succeedingPipelineIDFieldSet,
        array &$variables,
        array &$messages,
        EngineIterationFeedbackStore $engineIterationFeedbackStore,
    ): mixed;
}

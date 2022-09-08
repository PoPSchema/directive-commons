<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\DirectiveResolvers;

use PoPSchema\DirectiveCommons\FeedbackItemProviders\FeedbackItemProvider;
use PoP\ComponentModel\Engine\EngineIterationFieldSet;
use PoP\ComponentModel\Feedback\EngineIterationFeedbackStore;
use PoP\ComponentModel\Feedback\ObjectResolutionFeedback;
use PoP\ComponentModel\TypeResolvers\ConcreteTypeResolverInterface;
use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;
use PoP\ComponentModel\TypeResolvers\ScalarType\AnyBuiltInScalarScalarTypeResolver;
use PoP\ComponentModel\TypeResolvers\ScalarType\IDScalarTypeResolver;
use PoP\ComponentModel\TypeResolvers\ScalarType\StringScalarTypeResolver;
use PoP\GraphQLParser\Spec\Parser\Ast\FieldInterface;
use PoP\Root\Feedback\FeedbackItemResolution;
use SplObjectStorage;

/**
 * Apply a transformation to the string
 */
abstract class AbstractTransformFieldStringValueDirectiveResolver extends AbstractTransformFieldValueDirectiveResolver
{
    /**
     * @param array<array<string|int,EngineIterationFieldSet>> $succeedingPipelineIDFieldSet
     * @param array<string|int,SplObjectStorage<FieldInterface,mixed>> $resolvedIDFieldValues
     */
    final protected function transformValue(
        mixed $value,
        string|int $id,
        FieldInterface $field,
        RelationalTypeResolverInterface $relationalTypeResolver,
        array &$succeedingPipelineIDFieldSet,
        array &$resolvedIDFieldValues,
        EngineIterationFeedbackStore $engineIterationFeedbackStore,
    ): mixed {
        // null => Nothing to do
        if ($value === null) {
            return null;
        }

        /**
         * Validate it is a string
         */
        if (!is_string($value)) {
            $this->handleNonStringValue(
                $value,
                $id,
                $field,
                $relationalTypeResolver,
                $succeedingPipelineIDFieldSet,
                $resolvedIDFieldValues,
                $engineIterationFeedbackStore,
            );
            return null;
        }

        /** @var string $value */
        return $this->transformStringValue(
            $value,
            $id,
            $field,
            $relationalTypeResolver,
        );
    }

    abstract protected function transformStringValue(string $value, string|int $id, FieldInterface $field, RelationalTypeResolverInterface $relationalTypeResolver): string;

    /**
     * @param array<array<string|int,EngineIterationFieldSet>> $succeedingPipelineIDFieldSet
     * @param array<string|int,SplObjectStorage<FieldInterface,mixed>> $resolvedIDFieldValues
     */
    protected function handleNonStringValue(
        mixed $value,
        string|int $id,
        FieldInterface $field,
        RelationalTypeResolverInterface $relationalTypeResolver,
        array &$succeedingPipelineIDFieldSet,
        array &$resolvedIDFieldValues,
        EngineIterationFeedbackStore $engineIterationFeedbackStore,
    ): void {
        /** @var array<string|int,EngineIterationFieldSet> */
        $idFieldSetToRemove = [
            $id => new EngineIterationFieldSet([$field]),
        ];

        $this->removeIDFieldSet(
            $succeedingPipelineIDFieldSet,
            $idFieldSetToRemove,
        );
        $this->setFailingFieldResponseAsNull(
            $resolvedIDFieldValues,
            $idFieldSetToRemove,
        );

        $engineIterationFeedbackStore->objectResolutionFeedbackStore->addError(
            new ObjectResolutionFeedback(
                new FeedbackItemResolution(
                    FeedbackItemProvider::class,
                    FeedbackItemProvider::E1,
                    [
                        $this->getDirectiveName(),
                        $field->getOutputKey(),
                        $id,
                    ]
                ),
                $this->directive,
                $relationalTypeResolver,
                $this->directive,
                $idFieldSetToRemove
            )
        );
    }

    /**
     * @return array<class-string<ConcreteTypeResolverInterface>>|null
     */
    protected function getSupportedFieldTypeResolverClasses(): ?array
    {
        return [
            StringScalarTypeResolver::class,
            IDScalarTypeResolver::class,
            AnyBuiltInScalarScalarTypeResolver::class,
        ];
    }
}

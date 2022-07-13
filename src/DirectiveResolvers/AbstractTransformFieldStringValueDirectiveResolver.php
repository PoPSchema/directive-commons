<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\DirectiveResolvers;

use PoP\ComponentModel\Engine\EngineIterationFieldSet;
use PoP\ComponentModel\Module as ComponentModelModule;
use PoP\ComponentModel\ModuleConfiguration as ComponentModelModuleConfiguration;
use PoP\ComponentModel\Feedback\EngineIterationFeedbackStore;
use PoP\Root\Feedback\FeedbackItemResolution;
use PoP\ComponentModel\Feedback\ObjectResolutionFeedback;
use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;
use PoP\GraphQLParser\Spec\Parser\Ast\FieldInterface;
use PoP\Root\App;
use PoPSchema\DirectiveCommons\FeedbackItemProviders\FeedbackItemProvider;

/**
 * Apply a transformation to the string
 */
abstract class AbstractTransformFieldStringValueDirectiveResolver extends AbstractTransformFieldValueDirectiveResolver
{
    final protected function transformValue(
        mixed $value,
        string|int $id,
        FieldInterface $field,
        RelationalTypeResolverInterface $relationalTypeResolver,
        array &$succeedingPipelineIDFieldSet,
        array &$variables,
        array &$messages,
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
            $variables,
            $messages,
        );
    }

    abstract protected function transformStringValue(string $value, string|int $id, FieldInterface $field, RelationalTypeResolverInterface $relationalTypeResolver, array &$variables, array &$messages): string;

    protected function handleNonStringValue(
        mixed $value,
        string|int $id,
        FieldInterface $field,
        RelationalTypeResolverInterface $relationalTypeResolver,
        array &$succeedingPipelineIDFieldSet,
        EngineIterationFeedbackStore $engineIterationFeedbackStore,
    ): void {
        /** @var array<string|int,EngineIterationFieldSet> */
        $idFieldSetToRemove = [
            $id => new EngineIterationFieldSet([$field]),
        ];

        /** @var ComponentModelModuleConfiguration */
        $moduleConfiguration = App::getModule(ComponentModelModule::class)->getConfiguration();
        $removeFieldIfDirectiveFailed = $moduleConfiguration->removeFieldIfDirectiveFailed();
        if ($removeFieldIfDirectiveFailed) {
            $this->removeIDFieldSet(
                $idFieldSetToRemove,
                $succeedingPipelineIDFieldSet
            );
        }

        $engineIterationFeedbackStore->objectFeedbackStore->addError(
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
}

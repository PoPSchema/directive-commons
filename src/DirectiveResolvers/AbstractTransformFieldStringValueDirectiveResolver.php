<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\DirectiveResolvers;

use PoP\ComponentModel\Module as ComponentModelModule;
use PoP\ComponentModel\ModuleConfiguration as ComponentModelModuleConfiguration;
use PoP\ComponentModel\Feedback\EngineIterationFeedbackStore;
use PoP\Root\Feedback\FeedbackItemResolution;
use PoP\ComponentModel\Feedback\ObjectFeedback;
use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;
use PoP\GraphQLParser\StaticHelpers\LocationHelper;
use PoP\Root\App;
use PoPSchema\DirectiveCommons\FeedbackItemProviders\FeedbackItemProvider;

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
                $fieldOutputKey,
                $relationalTypeResolver,
                $succeedingPipelineIDsDataFields,
                $engineIterationFeedbackStore,
            );
            return null;
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
        );
    }

    abstract protected function transformStringValue(string $value, string | int $id, string $field, string $fieldOutputKey, RelationalTypeResolverInterface $relationalTypeResolver, array &$variables, array &$messages): string;

    protected function handleNonStringValue(
        mixed $value,
        string | int $id,
        string $field,
        string $fieldOutputKey,
        RelationalTypeResolverInterface $relationalTypeResolver,
        array &$succeedingPipelineIDsDataFields,
        EngineIterationFeedbackStore $engineIterationFeedbackStore,
    ): void {
        /** @var ComponentModelModuleConfiguration */
        $moduleConfiguration = App::getModule(ComponentModelModule::class)->getConfiguration();
        $removeFieldIfDirectiveFailed = $moduleConfiguration->removeFieldIfDirectiveFailed();
        if ($removeFieldIfDirectiveFailed) {
            $idsDataFieldsToRemove = [];
            $idsDataFieldsToRemove[(string)$id]['direct'][] = $field;
            $this->removeIDsDataFields(
                $idsDataFieldsToRemove,
                $succeedingPipelineIDsDataFields
            );
        }

        $engineIterationFeedbackStore->objectFeedbackStore->addError(
            new ObjectFeedback(
                new FeedbackItemResolution(
                    FeedbackItemProvider::class,
                    FeedbackItemProvider::E1,
                    [
                        $this->getDirectiveName(),
                        $fieldOutputKey,
                        $id,
                    ]
                ),
                LocationHelper::getNonSpecificLocation(),
                $relationalTypeResolver,
                $field,
                $id,
                $this->directive,
            )
        );
    }
}

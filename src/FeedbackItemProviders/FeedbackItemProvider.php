<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\FeedbackItemProviders;

use PoP\Root\FeedbackItemProviders\AbstractFeedbackItemProvider;
use PoP\ComponentModel\Feedback\FeedbackCategories;

class FeedbackItemProvider extends AbstractFeedbackItemProvider
{
    public final const E1 = 'e1';
    public final const W1 = 'w1';
    public final const W2 = 'w2';

    /**
     * @return string[]
     */
    public function getCodes(): array
    {
        return [
            self::E1,
            self::W1,
            self::W2,
        ];
    }

    public function getMessagePlaceholder(string $code): string
    {
        return match ($code) {
            self::E1 => $this->__('Directive \'%s\' from field \'%s\' cannot be applied on object with ID \'%s\' because it is not a string', 'directives-commons'),
            self::W1 => $this->__('Dynamic variable with name \'%s\' had already been set, had its value overridden', 'export-directive'),
            self::W2 => $this->__('Dynamic variable with name \'%s\' had already been set for object with ID \'%s\', had its value overridden', 'export-directive'),
            default => parent::getMessagePlaceholder($code),
        };
    }

    public function getCategory(string $code): string
    {
        return match ($code) {
            self::E1
                => FeedbackCategories::ERROR,
            self::W1,
            self::W2
                => FeedbackCategories::WARNING,
            default
                => parent::getCategory($code),
        };
    }
}

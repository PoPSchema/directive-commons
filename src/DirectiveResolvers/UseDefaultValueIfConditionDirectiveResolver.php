<?php

declare(strict_types=1);

namespace PoP\BasicDirectives\DirectiveResolvers;

use PoP\ComponentModel\DirectiveResolvers\GlobalDirectiveResolverTrait;
use PoP\BasicDirectives\DirectiveResolvers\AbstractUseDefaultValueIfConditionDirectiveResolver;

class UseDefaultValueIfConditionDirectiveResolver extends AbstractUseDefaultValueIfConditionDirectiveResolver
{
    use GlobalDirectiveResolverTrait;

    const DIRECTIVE_NAME = 'default';
    public static function getDirectiveName(): string
    {
        return self::DIRECTIVE_NAME;
    }
}

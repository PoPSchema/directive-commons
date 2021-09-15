<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\TypeResolvers\EnumType;

use PoP\ComponentModel\Enums\AbstractEnumTypeResolver;
use PoPSchema\DirectiveCommons\Enums\DefaultConditionEnum;

class DefaultConditionEnumTypeResolver extends AbstractEnumTypeResolver
{
    public function getTypeName(): string
    {
        return 'DefaultCondition';
    }
    /**
     * @return string[]
     */
    public function getEnumValues(): array
    {
        return [
            DefaultConditionEnum::IS_NULL,
            DefaultConditionEnum::IS_EMPTY,
        ];
    }
}

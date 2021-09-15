<?php

declare(strict_types=1);

namespace PoPSchema\DirectiveCommons\Enums;

use PoP\ComponentModel\Enums\AbstractEnumTypeResolver;

class DefaultConditionEnum extends AbstractEnumTypeResolver
{
    public const IS_NULL = 'IS_NULL';
    public const IS_EMPTY = 'IS_EMPTY';

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
            self::IS_NULL,
            self::IS_EMPTY,
        ];
    }
}

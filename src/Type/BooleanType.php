<?php
namespace Jtl\OpenApiComponentGenerator\Type;

class BooleanType extends AbstractType
{
    public function getOpenApiType(): string
    {
        return self::BOOLEAN;
    }

    public function getPhpType(): string
    {
        return 'bool';
    }
}

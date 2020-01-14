<?php
namespace Jtl\OpenApiComponentGenerator\Type;

class NumberType extends AbstractType
{
    public function getOpenApiType(): string
    {
        return self::NUMBER;
    }

    public function getPhpType(): string
    {
        return 'float';
    }
}
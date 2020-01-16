<?php
namespace Jtl\OpenApiComponentsGenerator\Type;

class NumberType extends AbstractFormatType
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

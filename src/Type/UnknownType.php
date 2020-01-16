<?php
namespace Jtl\OpenApiComponentGenerator\Type;

class UnknownType extends AbstractType
{
    public function getOpenApiType(): string
    {
        return '';
    }

    public function getPhpType(): string
    {
        return '';
    }
}

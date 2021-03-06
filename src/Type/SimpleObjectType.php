<?php
namespace Jtl\OpenApiComponentsGenerator\Type;

class SimpleObjectType extends AbstractType
{
    /**
     * @return string
     */
    public function getOpenApiType(): string
    {
        return self::OBJECT;
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return 'array';
    }
}

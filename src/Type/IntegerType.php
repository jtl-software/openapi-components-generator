<?php
namespace Jtl\OpenApiComponentGenerator\Type;

class IntegerType extends AbstractType
{
    /**
     * @return string
     */
    public function getOpenApiType(): string
    {
        return self::INTEGER;
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return 'int';
    }
}
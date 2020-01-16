<?php
namespace Jtl\OpenApiComponentGenerator\Type;

class IntegerType extends AbstractFormatType
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
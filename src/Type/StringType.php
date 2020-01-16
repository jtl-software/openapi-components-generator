<?php
namespace Jtl\OpenApiComponentsGenerator\Type;

class StringType extends AbstractFormatType
{
    /**
     * @return string
     */
    public function getOpenApiType(): string
    {
        return self::STRING;
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return self::STRING;
    }
}

<?php
namespace Jtl\OpenApiComponentGenerator\Type;

class StringType extends AbstractType
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
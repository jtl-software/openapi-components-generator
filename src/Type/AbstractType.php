<?php
namespace Jtl\OpenApiComponentGenerator\Type;

abstract class AbstractType
{
    public const
        STRING = 'string',
        NUMBER = 'number',
        INTEGER = 'integer',
        BOOLEAN = 'boolean',
        ARRAY = 'array',
        OBJECT = 'object',
        MULTI_OBJECT = 'multi_object'
    ;

    /**
     * @var string[]
     */
    protected static $basicDataTypes = [
        self::STRING,
        self::NUMBER,
        self::INTEGER,
        self::BOOLEAN,
    ];

    /**
     * @return string
     */
    public abstract function getOpenApiType(): string;

    /**
     * @return string
     */
    public abstract function getPhpType(): string;

    /**
     * @return string[]
     */
    public static function getBasicDataTypes(): array
    {
        return self::$basicDataTypes;
    }
}
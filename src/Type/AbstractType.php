<?php
namespace Jtl\OpenApiComponentsGenerator\Type;

abstract class AbstractType
{
    public const
        STRING = 'string',
        NUMBER = 'number',
        INTEGER = 'integer',
        BOOLEAN = 'boolean',
        ARRAY = 'array',
        OBJECT = 'object',
        COMBINED = 'combined',
        UNKNOWN = 'unknown'
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
    abstract public function getOpenApiType(): string;

    /**
     * @return string
     */
    abstract public function getPhpType(): string;

    /**
     * @return boolean
     */
    public function hasPhpType(): bool
    {
        return !empty($this->getPhpType());
    }

    /**
     * @return string[]
     */
    public static function getBasicDataTypes(): array
    {
        return self::$basicDataTypes;
    }
}

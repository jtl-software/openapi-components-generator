<?php
namespace Jtl\OpenApiComponentsGenerator\Type;

abstract class AbstractFormatType extends AbstractType
{
    public const
        FORMAT_NONE = '',
        FORMAT_DATETIME = 'date-time',
        FORMAT_UUID = 'uuid'
    ;

    /**
     * @var string
     */
    protected $format = self::FORMAT_NONE;

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return AbstractFormatType
     */
    public function setFormat(string $format): AbstractFormatType
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasFormat(): bool
    {
        return $this->format == self::FORMAT_NONE;
    }
}

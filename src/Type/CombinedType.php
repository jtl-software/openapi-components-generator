<?php
namespace Jtl\OpenApiComponentGenerator\Type;

class CombinedType extends AbstractType
{
    const ONE_OF = 'oneOf';
    const ANY_OF = 'anyOf';
    const ALL_OF = 'allOf';

    /**
     * @var string[]
     */
    protected static $multiTypes = [
        self::ONE_OF,
        self::ANY_OF,
        self::ALL_OF,
    ];

    /**
     * @var string
     */
    protected $multiType;

    /**
     * @var AbstractType[]
     */
    protected $elements = [];

    /**
     * MultiType constructor.
     * @param string $multiType
     * @throws \Exception
     */
    public function __construct(string $multiType)
    {
        if (!self::isMultiType($multiType)) {
            throw new \Exception(sprintf('%s is not a multi type', $multiType));
        }
        $this->multiType = $multiType;
    }

    /**
     * @return string
     */
    public function getOpenApiType(): string
    {
        return $this->multiType;
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return 'object';
    }

    /**
     * @return string
     */
    public function getMultiType(): string
    {
        return $this->multiType;
    }

    /**
     * @param AbstractType $type
     * @return CombinedType
     */
    public function addElement(AbstractType $type): CombinedType
    {
        if (!in_array($type, $this->elements, true)) {
            $this->elements[] = $type;
        }
        return $this;
    }

    /**
     * @return AbstractType[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param AbstractType ...$elements
     * @return CombinedType
     */
    public function setElements(AbstractType ...$elements): CombinedType
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * @param string $type
     * @return boolean
     */
    public static function isMultiType(string $type): bool
    {
        return in_array($type, self::$multiTypes, true);
    }
}

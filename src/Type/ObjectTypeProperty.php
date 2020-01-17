<?php
namespace Jtl\OpenApiComponentsGenerator\Type;

class ObjectTypeProperty
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var AbstractType
     */
    protected $type;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var mixed[]
     */
    protected $enum = [];

    /**
     * @var boolean
     */
    protected $required = false;

    /**
     * @var boolean
     */
    protected $readOnly = false;

    /**
     * @var mixed[]
     */
    protected $rawData = [];

    /**
     * ObjectTypeProperty constructor.
     * @param string $name
     * @param AbstractType $type
     * @param bool $required
     * @param bool $readOnly
     */
    public function __construct(string $name, AbstractType $type, bool $required = false, bool $readOnly = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->readOnly = $readOnly;
    }

    /**
     * @return ObjectType
     */
    public function getParent(): ObjectType
    {
        return $this->parent;
    }

    /**
     * @param ObjectType $parent
     * @return ObjectTypeProperty
     */
    public function setParent(ObjectType $parent): ObjectTypeProperty
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function hasDescription(): bool
    {
        return strlen($this->description) > 0;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return ObjectTypeProperty
     */
    public function setDescription(string $description): ObjectTypeProperty
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return AbstractType
     */
    public function getType(): AbstractType
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return boolean
     */
    public function hasDefaultValue(): bool
    {
        return !is_null($this->defaultValue);
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return boolean
     */
    public function isEnum(): bool
    {
        return count($this->enum) > 0;
    }

    /**
     * @return mixed[]
     */
    public function getEnum(): array
    {
        return $this->enum;
    }

    /**
     * @param mixed[] $enum
     * @return ObjectTypeProperty
     */
    public function setEnum(array $enum): ObjectTypeProperty
    {
        $this->enum = $enum;
        return $this;
    }

    /**
     * @param mixed $defaultValue
     * @return ObjectTypeProperty
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param string $format
     * @return ObjectTypeProperty
     */
    public function setFormat(string $format): ObjectTypeProperty
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * @return mixed[]
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    /**
     * @param mixed[] $rawData
     * @return ObjectTypeProperty
     */
    public function setRawData(array $rawData): ObjectTypeProperty
    {
        $this->rawData = $rawData;
        return $this;
    }
}

<?php
namespace Jtl\OpenApiComponentGenerator\Type;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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

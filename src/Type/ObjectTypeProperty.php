<?php
namespace Jtl\OpenApiComponentGenerator\Type;

class ObjectTypeProperty
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var AbstractType
     */
    protected $type;

    /**
     * @var string
     */
    protected $format = '';

    /**
     * @var boolean
     */
    protected $required = false;

    /**
     * @var boolean
     */
    protected $readOnly = false;

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
}
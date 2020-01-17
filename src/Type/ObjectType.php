<?php
namespace Jtl\OpenApiComponentsGenerator\Type;

class ObjectType extends AbstractType
{
    /**
     * @var ObjectTypeProperty[]
     */
    protected $properties = [];

    /**
     * @var boolean
     */
    protected $abstract = false;

    /**
     * @return string
     */
    public function getOpenApiType(): string
    {
        return self::OBJECT;
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return '';
    }

    /**
     * @return ObjectTypeProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param ObjectTypeProperty ...$properties
     * @return NamedObjectType
     */
    public function setProperties(ObjectTypeProperty ...$properties): ObjectType
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * @param ObjectTypeProperty $property
     * @return NamedObjectType
     */
    public function addProperty(ObjectTypeProperty $property): ObjectType
    {
        if (!in_array($property, $this->properties, true)) {
            $this->properties[] = $property;
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    /**
     * @param bool $abstract
     * @return ObjectType
     */
    public function setAbstract(bool $abstract): ObjectType
    {
        $this->abstract = $abstract;
        return $this;
    }
}

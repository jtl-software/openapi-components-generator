<?php
namespace Jtl\OpenApiComponentGenerator\Type;

class ObjectType extends AbstractType
{
    /**
     * @var ObjectTypeProperty[]
     */
    protected $properties = [];

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
        if(!in_array($property, $this->properties, true)) {
            $this->properties[] = $property;
        }
        return $this;
    }
}
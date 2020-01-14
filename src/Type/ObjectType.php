<?php

namespace Jtl\OpenApiComponentGenerator\Type;

class ObjectType extends AbstractType
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @var ObjectTypeProperty[]
     */
    protected $properties = [];

    /**
     * ObjectType constructor.
     * @param string $name
     * @param string $namespace
     * @param ObjectTypeProperty ...$properties
     */
    public function __construct(string $name, string $namespace = '', ObjectTypeProperty ...$properties)
    {
        $this->name = $name;
        $this->namespace = $namespace;
    }

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
        $name = implode('', array_map('ucfirst', explode('_', $this->name)));
        if (!empty($this->namespace)) {
            return trim($this->namespace, '\\') . '\\' . $name;
        }
        return $name;
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
    public function getNamespace(): string
    {
        return $this->namespace;
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
     * @return ObjectType
     */
    public function setProperties(ObjectTypeProperty ...$properties): ObjectType
    {
        foreach ($properties as $property) {
            $this->setProperty($property);
        }
        return $this;
    }

    /**
     * @param ObjectTypeProperty $property
     * @return ObjectType
     */
    public function setProperty(ObjectTypeProperty $property): ObjectType
    {
        $this->properties[$property->getName()] = $property;
        return $this;
    }
}
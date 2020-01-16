<?php

namespace Jtl\OpenApiComponentsGenerator\Type;

class NamedObjectType extends ObjectType
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
     * ObjectType constructor.
     * @param string $name
     * @param string $namespace
     */
    public function __construct(string $name, string $namespace = '')
    {
        $this->name = $name;
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return implode('', array_map('ucfirst', explode('_', $this->name)));
    }

    /**
     * @return string
     */
    public function getFullQualifiedPhpType(): string
    {
        if ($this->hasNamespace()) {
            return sprintf('%s\\%s', trim($this->namespace, '\\'), $this->getPhpType());
        }
        return $this->getPhpType();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function hasNamespace(): bool
    {
        return strlen($this->namespace) > 0;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     * @return NamedObjectType
     */
    public function setNamespace(string $namespace): NamedObjectType
    {
        $this->namespace = $namespace;
        return $this;
    }
}

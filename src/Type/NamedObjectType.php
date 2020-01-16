<?php

namespace Jtl\OpenApiComponentGenerator\Type;

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
     * @param string $namespace
     * @return NamedObjectType
     */
    public function setNamespace(string $namespace): NamedObjectType
    {
        $this->namespace = $namespace;
        return $this;
    }
}

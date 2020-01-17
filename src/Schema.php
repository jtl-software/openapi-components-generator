<?php
namespace Jtl\OpenApiComponentsGenerator;

use Jtl\OpenApiComponentsGenerator\Type\AbstractType;

class Schema
{
    /**
     * @var string
     */
    protected $apiSchemaPath;

    /**
     * @var string
     */
    protected $openApiVersion;

    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @var AbstractType[]
     */
    protected $components = [];

    /**
     * Schema constructor.
     * @param string $apiSchemaPath
     * @param string $openApiVersion
     * @param AbstractType[] $components
     */
    public function __construct(string $apiSchemaPath, string $openApiVersion, array $components)
    {
        $this->apiSchemaPath = $apiSchemaPath;
        $this->openApiVersion = $openApiVersion;
        $this->setComponents($components);
    }

    /**
     * @return string
     */
    public function getApiSchemaPath(): string
    {
        return $this->apiSchemaPath;
    }

    /**
     * @return string
     */
    public function getOpenApiVersion(): string
    {
        return $this->openApiVersion;
    }

    /**
     * @return AbstractType[]
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * @param array $components
     * @return Schema
     */
    public function setComponents(array $components): Schema
    {
        foreach ($components as $name => $component) {
            $this->setComponent($name, $component);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param AbstractType $component
     * @return Schema
     */
    public function setComponent(string $name, AbstractType $component): Schema
    {
        $this->components[$name] = $component;
        return $this;
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
     * @return Schema
     */
    public function setNamespace(string $namespace): Schema
    {
        $this->namespace = $namespace;
        return $this;
    }
}

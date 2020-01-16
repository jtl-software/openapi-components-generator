<?php
namespace Jtl\OpenApiComponentGenerator;

use Jtl\OpenApiComponentGenerator\Type\AbstractType;

class Schema
{
    /**
     * @var string
     */
    protected $schemaUrl;

    /**
     * @var string
     */
    protected $openApiVersion;

    /**
     * @var AbstractType[]
     */
    protected $components = [];

    /**
     * Schema constructor.
     * @param string $schemaUrl
     * @param string $openApiVersion
     * @param AbstractType[] $components
     */
    public function __construct(string $schemaUrl, string $openApiVersion, array $components)
    {
        $this->schemaUrl = $schemaUrl;
        $this->openApiVersion = $openApiVersion;
        $this->setComponents($components);
    }

    /**
     * @return string
     */
    public function getSchemaUrl(): string
    {
        return $this->schemaUrl;
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
}
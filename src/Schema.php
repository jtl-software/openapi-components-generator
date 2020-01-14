<?php
namespace Jtl\OpenApiComponentGenerator;

use Jtl\OpenApiComponentGenerator\Type\ObjectType;

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
     * @var ObjectType[]
     */
    protected $components = [];

    /**
     * Schema constructor.
     * @param string $schemaUrl
     * @param string $openApiVersion
     * @param ObjectType ...$components
     */
    public function __construct(string $schemaUrl, string $openApiVersion, ObjectType ...$components)
    {
        $this->schemaUrl = $schemaUrl;
        $this->openApiVersion = $openApiVersion;
        $this->components = $components;
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
     * @return ObjectType[]
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * @param ObjectType ...$components
     * @return Schema
     */
    public function setComponents(ObjectType ...$components): Schema
    {
        $this->components = $components;
        return $this;
    }
}
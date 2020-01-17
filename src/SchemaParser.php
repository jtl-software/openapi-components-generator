<?php

namespace Jtl\OpenApiComponentsGenerator;

use Jtl\OpenApiComponentsGenerator\Type\AbstractFormatType;
use Jtl\OpenApiComponentsGenerator\Type\AbstractType;
use Jtl\OpenApiComponentsGenerator\Type\ObjectType;
use Jtl\OpenApiComponentsGenerator\Type\ArrayType;
use Jtl\OpenApiComponentsGenerator\Type\CombinedType;
use Jtl\OpenApiComponentsGenerator\Type\NamedObjectType;
use Jtl\OpenApiComponentsGenerator\Type\ObjectTypeProperty;
use Jtl\OpenApiComponentsGenerator\Type\SimpleObjectType;
use Jtl\OpenApiComponentsGenerator\Type\UnknownType;

class SchemaParser
{
    /**
     * @var UnknownType
     */
    protected $unknownType;

    /**
     * @var array AbstractType[]
     */
    protected $basicDataTypes = [];

    /**
     * @var NamedObjectType[]
     */
    protected $components = [];

    /**
     * @var string[]
     */
    protected $filterPatterns = [];

    /**
     * SchemaParser constructor.
     */
    public function __construct()
    {
        $this->unknownType = new UnknownType();
        foreach (AbstractType::getBasicDataTypes() as $dataType) {
            $typeClass = sprintf('Jtl\\OpenApiComponentsGenerator\\Type\\%sType', ucfirst($dataType));
            if (class_exists($typeClass)) {
                $this->basicDataTypes[$dataType] = new $typeClass();
            }
        }
    }

    /**
     * @param string $apiSchemaPath
     * @param string $namespace
     * @return Schema
     * @throws \Exception
     */
    public function read(string $apiSchemaPath, string $namespace = ''): Schema
    {
        $handle = fopen($apiSchemaPath, 'r');
        if (!$handle) {
            throw new \Exception(sprintf('%s not found', $apiSchemaPath));
        }
        $schemaData = json_decode(fread($handle, filesize($apiSchemaPath)), true);
        fclose($handle);

        $this->components = [];

        if (!isset($schemaData['openapi'])) {
            throw new \Exception('\'openapi\' property not found in schema');
        }

        if (!version_compare($schemaData['openapi'], '3.0', '>=')) {
            throw new \Exception(sprintf('Given OpenAPI version (%s) is not supported', $schemaData['openapi']));
        }

        if (!isset($schemaData['components']['schemas'])) {
            throw new \Exception('No components found');
        }

        foreach ($schemaData['components']['schemas'] as $componentName => $componentData) {
            $found = true;
            foreach ($this->filterPatterns as $pattern) {
                $found = false;
                if (preg_match($pattern, $componentName) === 1) {
                    $found = true;
                    break;
                }
            }

            if ($found === false) {
                continue;
            }

            $type = $this->determineType($componentData);

            switch ($type) {
                case AbstractType::OBJECT:
                    $this->components[$componentName] = new NamedObjectType($componentName, $namespace);
                    break;
                case AbstractType::COMBINED:
                    $this->components[$componentName] = new CombinedType($this->determineMultiType($componentData));
                    break;
            }
        }

        foreach ($this->components as $name => $component) {
            if ($component instanceof NamedObjectType) {
                $this->instantiateObjectType($component, $schemaData['components']['schemas'][$name]);
            } elseif ($component instanceof CombinedType) {
                $this->instantiateCombinedType($component, $schemaData['components']['schemas'][$name][$component->getMultiType()]);
            }
        }

        return (new Schema($apiSchemaPath, $schemaData['openapi'], $this->components));
    }

    /**
     * @param ObjectType $objectType
     * @param array $data
     * @return ObjectType
     * @throws \Exception
     */
    protected function instantiateObjectType(ObjectType $objectType, array $data): ObjectType
    {
        $requiredProperties = $data['required'] ?? [];
        $properties = $data['properties'] ?? [];
        foreach ($properties as $propertyName => $propertyData) {
            $objectType->addProperty($this->instantiateProperty($propertyName, $propertyData, in_array($propertyName, $requiredProperties, true)));
        }
        return $objectType;
    }

    /**
     * @param CombinedType $type
     * @param array $data
     * @return CombinedType
     * @throws \Exception
     */
    protected function instantiateCombinedType(CombinedType $type, array $data): CombinedType
    {
        foreach ($data as $i => $valueData) {
            if (isset($valueData['$ref'])) {
                $componentName = $this->getComponentNameFromRef($valueData['$ref']);
                if (isset($this->components[$componentName])) {
                    $type->addElement($this->components[$componentName]);
                }
            } elseif (isset($valueData['type'])) {
                $type->addElement($this->instantiateType($valueData));
            }
        }
        return $type;
    }

    /**
     * @param string $name
     * @param array $data
     * @param bool $required
     * @return ObjectTypeProperty
     * @throws \Exception
     */
    protected function instantiateProperty(string $name, array $data, bool $required = false): ObjectTypeProperty
    {
        $type = $this->instantiateType($data);
        $readOnly = isset($data['readOnly']) && $data['readOnly'] === true;
        $description = $data['description'] ?? '';
        $defaultValue = $data['default'] ?? null;
        $enum = $data['enum'] ?? [];

        return (new ObjectTypeProperty($name, $type, $required, $readOnly))
            ->setRawData($data)
            ->setDescription($description)
            ->setDefaultValue($defaultValue)
            ->setEnum($enum);
    }

    /**
     * @param array $data
     * @return AbstractType
     * @throws \Exception
     */
    protected function instantiateType(array $data): AbstractType
    {
        $typeName = $this->determineType($data);

        $type = null;
        switch ($typeName) {
            case AbstractType::ARRAY:
                $itemsType = null;
                if (isset($data['items']['$ref'])) {
                    $componentName = $this->getComponentNameFromRef($data['items']['$ref']);
                    if (isset($this->components[$componentName])) {
                        $itemsType = $this->components[$componentName];
                    }
                } elseif (isset($data['items']['type']) && isset($this->basicDataTypes[$data['items']['type']])) {
                    $itemsType = clone $this->basicDataTypes[$data['items']['type']];
                    if ($itemsType instanceof AbstractFormatType && isset($data['items']['format'])) {
                        $itemsType->setFormat($data['items']['format']);
                    }
                }
                $type = new ArrayType($itemsType);
                break;

            case AbstractType::OBJECT:
                $type = new SimpleObjectType();
                if (isset($data['$ref'])) {
                    $componentName = $this->getComponentNameFromRef($data['$ref']);
                    if (isset($this->components[$componentName])) {
                        $type = $this->components[$componentName];
                    }
                }

                if ($type instanceof SimpleObjectType && isset($data['properties'])) {
                    $type = $this->instantiateObjectType(new ObjectType(), $data);
                }
                break;

            case AbstractType::COMBINED:
                $multiType = $this->determineMultiType($data);
                $type = $this->instantiateCombinedType(new CombinedType($multiType), $data[$multiType]);
                break;

            case AbstractType::UNKNOWN:
                $type = $this->unknownType;
                if (isset($data['$ref'])) {
                    $componentName = $this->getComponentNameFromRef($data['$ref']);
                    if (isset($this->components[$componentName])) {
                        $type = $this->components[$componentName];
                    }
                }
                break;

            default:
                if (!isset($this->basicDataTypes[$typeName])) {
                    throw new \Exception(sprintf('%s is not a basic data type', $typeName));
                }

                $type = clone $this->basicDataTypes[$typeName];
                if ($type instanceof AbstractFormatType && isset($data['format'])) {
                    $type->setFormat($data['format']);
                }
                break;
        }

        return $type;
    }

    /**
     * @param mixed[] $data
     * @return string
     */
    protected function determineType(array $data): string
    {
        $type = $data['type'] ?? AbstractType::UNKNOWN;
        if (isset($data['$ref']) && !isset($data['type']) || isset($data['properties'])) {
            $type = AbstractType::OBJECT;
        } elseif ($type === AbstractType::UNKNOWN && (isset($data['allOf']) || isset($data['oneOf']) || isset($data['anyOf']))) {
            $type = AbstractType::COMBINED;
        }
        return $type;
    }

    /**
     * @param mixed[] $data
     * @return string|null
     */
    protected function determineMultiType(array $data): ?string
    {
        $mulitType = null;
        if (isset($data['allOf']) || isset($data['oneOf']) || isset($data['anyOf'])) {
            if (isset($data['allOf'])) {
                $mulitType = 'allOf';
            } elseif (isset($data['oneOf'])) {
                $mulitType = 'oneOf';
            } else {
                $mulitType = 'anyOf';
            }
        }
        return $mulitType;
    }

    /**
     * @param string $ref
     * @return string
     */
    protected function getComponentNameFromRef(string $ref): string
    {
        $lastSlashPos = strrpos($ref, '/');
        if ($lastSlashPos === false) {
            return $ref;
        }
        return substr($ref, ($lastSlashPos + 1));
    }

    /**
     * @param string $pattern
     * @return SchemaParser
     */
    public function addFilterPattern(string $pattern): SchemaParser
    {
        if (!in_array($pattern, $this->filterPatterns, true)) {
            $this->filterPatterns[] = $pattern;
        }
        return $this;
    }
}

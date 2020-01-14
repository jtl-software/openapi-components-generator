<?php

namespace Jtl\OpenApiComponentGenerator;

use Jtl\OpenApiComponentGenerator\Type\AbstractType;
use Jtl\OpenApiComponentGenerator\Type\ArrayType;
use Jtl\OpenApiComponentGenerator\Type\MultiObjectType;
use Jtl\OpenApiComponentGenerator\Type\ObjectType;
use Jtl\OpenApiComponentGenerator\Type\ObjectTypeProperty;
use Jtl\OpenApiComponentGenerator\Type\SimpleObjectType;

class SchemaParser
{
    /**
     * @var array AbstractType[]
     */
    protected $basicDataTypes = [];

    /**
     * @var ObjectType[]
     */
    protected $objectTypes = [];

    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @var string[]
     */
    protected $regexPatterns = [];

    /**
     * SchemaParser constructor.
     */
    public function __construct()
    {
        $this->basicDataTypes = [];
        foreach (AbstractType::getBasicDataTypes() as $dataType) {
            $typeClass = sprintf('Jtl\\OpenApiComponentGenerator\\Type\\%sType', ucfirst($dataType));
            if (class_exists($typeClass)) {
                $this->basicDataTypes[$dataType] = new $typeClass();
            }
        }
    }

    /**
     * @param string $schemaUrl
     * @return Schema
     * @throws \Exception
     */
    public function read(string $schemaUrl): Schema
    {
        $handle = fopen($schemaUrl, 'r');

        if (!$handle) {
            throw new \Exception(sprintf('%s not found', $schemaUrl));
        }

        fclose($handle);

        $schema = json_decode(file_get_contents($schemaUrl), true);
        if (!isset($schema['openapi'])) {
            throw new \Exception('\'openapi\' property not found in schema');
        }

        if (!version_compare($schema['openapi'], '3.0', '>=')) {
            throw new \Exception(sprintf('Given OpenAPI version (%s) is not supported', $schema['openapi']));
        }

        if (!isset($schema['components']['schemas'])) {
            throw new \Exception('No components found');
        }

        foreach ($schema['components']['schemas'] as $componentName => $componentData) {
            $found = true;
            foreach($this->regexPatterns as $pattern) {
                $found = false;
                if(preg_match($pattern, $componentName) === 1) {
                    $found = true;
                    break;
                }
            }

            if($found === false) {
                continue;
            }

            if (!isset($componentData['type'])) {
                throw new \Exception(sprintf('Type missing in %s component', $componentName));
            }

            switch ($componentData['type']) {
                case AbstractType::OBJECT:
                    $this->objectTypes[$componentName] = $this->createObjectType($componentName, $componentData);
                    break;
            }
        }

        return (new Schema($schemaUrl, $schema['openapi'], ...array_values($this->objectTypes)));
    }

    /**
     * @param string $name
     * @param array $data
     * @return ObjectType
     * @throws \Exception
     */
    protected function createObjectType(string $name, array $data): ObjectType
    {
        $objectType = new ObjectType($name);
        $requiredProperties = $data['required'] ?? [];
        $properties = $data['properties'] ?? [];
        foreach ($properties as $propertyName => $propertyData) {
            $objectType->setProperty($this->instantiateProperty($propertyName, $propertyData, in_array($propertyName, $requiredProperties)));

        }
        return $objectType;
    }

    /**
     * @param string $name
     * @param array $data
     * @param bool $required
     * @return ObjectType
     * @throws \Exception
     */
    protected function instantiateProperty(string $name, array $data, bool $required = false): ObjectTypeProperty
    {
        if (isset($data['$ref']) && !isset($data['type'])) {
            $data['type'] = AbstractType::OBJECT;
        }

        $mulitType = null;
        if (!isset($data['type']) && (isset($data['allOf']) || isset($data['oneOf']) || isset($data['anyOf']))) {
            $data['type'] = AbstractType::MULTI_OBJECT;
            if(isset($data['allOf'])) {
                $mulitType = 'allOf';
            } elseif(isset($data['oneOf'])) {
                $mulitType = 'oneOf';
            } else {
                $mulitType = 'anyOf';
            }
        }

        if (!isset($data['type'])) {
            throw new \Exception(sprintf('Type missing in object property (%s) data', $name));
        }

        $readOnly = isset($data['readOnly']) && $data['readOnly'] === true;
        $format = $data['format'] ?? '';

        $type = null;
        switch ($data['type']) {
            case AbstractType::ARRAY:
                $itemsType = null;
                if(isset($data['items']['$ref'])) {
                    $componentName = $this->getComponentNameFromRef($data['items']['$ref']);
                    if (!isset($this->objectTypes[$componentName])) {
                        $this->objectTypes[$componentName] = new ObjectType($componentName, $this->namespace);
                    }
                    $itemsType = $this->objectTypes[$componentName];
                }
                $type = new ArrayType($itemsType);
                break;

            case AbstractType::OBJECT:
                $type = new SimpleObjectType();
                if (isset($data['$ref'])) {
                    $componentName = $this->getComponentNameFromRef($data['$ref']);
                    if (!isset($this->objectTypes[$componentName])) {
                        $this->objectTypes[$componentName] = new ObjectType($componentName, $this->namespace);
                    }
                    $type = $this->objectTypes[$componentName];
                }
                break;

            case AbstractType::MULTI_OBJECT:
                $type = new MultiObjectType($mulitType);
                foreach ($data[$mulitType] as $i => $objRef) {
                    if (isset($objRef['$ref'])) {
                        $componentName = $this->getComponentNameFromRef($objRef['$ref']);
                        if (!isset($this->objectTypes[$componentName])) {
                            $this->objectTypes[$componentName] = new ObjectType($componentName, $this->namespace);
                        }
                        $type->addObjectType($this->objectTypes[$componentName]);
                    }
                }
                break;

            default:
                if (!isset($this->basicDataTypes[$data['type']])) {
                    throw new \Exception(sprintf('%s is not a basic data type!', $data['type']));
                }
                $type = $this->basicDataTypes[$data['type']];
                break;
        }

        return (new ObjectTypeProperty($name, $type, $required, $readOnly))->setFormat($format);
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

    public function addRegexPattern(string $pattern): SchemaParser
    {
        if(!in_array($pattern, $this->regexPatterns)) {
            $this->regexPatterns[] = $pattern;
        }
        return $this;
    }
}
<?php

namespace Jtl\OpenApiComponentsGenerator;

use Jtl\OpenApiComponentsGenerator\Type\AbstractFormatType;
use Jtl\OpenApiComponentsGenerator\Type\AbstractType;
use Jtl\OpenApiComponentsGenerator\Type\ArrayType;
use Jtl\OpenApiComponentsGenerator\Type\NamedObjectType;
use Jtl\OpenApiComponentsGenerator\Type\ObjectTypeProperty;
use Jtl\OpenApiComponentsGenerator\Type\StringType;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpLiteral;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\PsrPrinter;

class PhpGenerator
{
    const PARENT_ENTITY_CLASS_NAME = 'AbstractEntity';

    /**
     * @param Schema $schema
     * @param string $destinationDir
     * @throws \Exception
     */
    public function generateEntities(Schema $schema, string $destinationDir): void
    {
        $parentDir = dirname($destinationDir);
        if (!is_dir($parentDir)) {
            throw new \Exception(sprintf('Directory %s does not exist', $parentDir));
        }

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir);
        }

        $parentObject = (new NamedObjectType(self::PARENT_ENTITY_CLASS_NAME, $schema->getNamespace()))->setAbstract(true);
        $class = $this->createClassFromNamedObject($parentObject);
        $this->addClassPropertyReadOnly($class, $parentObject);
        $this->writeClass($destinationDir, $parentObject->getNamespace(), $class);
        foreach ($schema->getComponents() as $component) {
            if ($component instanceof NamedObjectType) {
                $class = $this->createClassFromNamedObject($component, $parentObject->getFullQualifiedPhpType());
                $this->writeClass($destinationDir, $component->getNamespace(), $class);
            }
        }
    }

    /**
     * @param string $destinationDir
     * @param string $namespace
     * @param ClassType $class
     * @return boolean
     */
    protected function writeClass(string $destinationDir, string $namespace, ClassType $class): bool
    {
        $file = (new PhpFile())
            ->addComment('This file is auto generated with the openapi3 component generator from JTL-Software');
        $file->addNamespace($namespace)->add($class);
        $classFile = sprintf('%s/%s.php', $destinationDir, $class->getName());
        return file_put_contents($classFile, (new PsrPrinter())->printFile($file)) !== false;
    }

    /**
     * @param NamedObjectType $type
     * @param string|null $extendsFrom
     * @return ClassType
     */
    protected function createClassFromNamedObject(NamedObjectType $type, string $extendsFrom = null): ClassType
    {
        $class = (new ClassType($type->getPhpType()))->setAbstract($type->isAbstract());
        if (!is_null($extendsFrom)) {
            $class->setExtends($extendsFrom);
        }

        $this->addClassConstructor($class, $type);
        foreach ($type->getProperties() as $property) {
            $this->addClassProperty($class, $type, $property);
        }
        return $class;
    }

    /**
     * @param ClassType $class
     * @param NamedObjectType $objectType
     * @param ObjectTypeProperty $property
     */
    protected function addClassProperty(ClassType $class, NamedObjectType $objectType, ObjectTypeProperty $property): void
    {
        $defaultValue = $this->determineDefaultValue($property);
        $commentDataType = '';
        $dataType = null;
        $isVariadic = false;
        if ($property->getType()->hasPhpType()) {
            $commentDataType = $dataType = $property->getType()->getPhpType();
            if ($property->getType() instanceof NamedObjectType) {
                $dataType = $property->getType()->getFullQualifiedPhpType();
            } elseif ($property->getType() instanceof StringType && $property->getType()->getFormat() === AbstractFormatType::FORMAT_DATETIME) {
                $dataType = 'DateTimeImmutable';
                $commentDataType = '\DateTimeImmutable';
            } elseif ($property->getType() instanceof ArrayType && !is_null($property->getType()->getItemsType())) {
                $commentDataType = sprintf('%s[]', $property->getType()->getItemsType()->getPhpType());
                $dataType = $property->getType()->getItemsType()->getPhpType();
                $isVariadic = true;
                if($property->getType()->getItemsType() instanceof NamedObjectType) {
                    $dataType = $property->getType()->getItemsType()->getFullQualifiedPhpType();
                }
            }
        }

        $classProperty = $class->addProperty($property->getName(), $defaultValue)
            ->setVisibility(ClassType::VISIBILITY_PROTECTED)
            ->addComment(PHP_EOL);
        if ($property->hasDescription()) {
            $classProperty->addComment(sprintf('%s%s', $property->getDescription(), PHP_EOL));
        }
        $classProperty->addComment(sprintf('@var %s', $commentDataType));

        $getMethod = $class->addMethod(sprintf('get%s', ucfirst($property->getName())))
            ->setBody(sprintf('return $this->%s;', $property->getName()))
            ->setReturnType($dataType)
            ->addComment(sprintf('@return %s', $commentDataType))
            ->setReturnNullable(is_null($defaultValue));

        $setMethod = $class->addMethod(sprintf('set%s', ucfirst($property->getName())))
            ->setBody(sprintf('$this->%s = $%s;%sreturn $this;', $property->getName(), $property->getName(), PHP_EOL))
            ->setReturnType($objectType->getFullQualifiedPhpType())
            ->addComment(sprintf('@param %s $%s', $commentDataType, $property->getName()))
            ->addComment(sprintf('@return %s', $objectType->getPhpType()))
            ->setVariadic($isVariadic)
        ;

        $setParam = $setMethod->addParameter($property->getName())
            ->setType($dataType)
        ;

    }

    /**
     * @param ClassType $class
     * @param NamedObjectType $type
     */
    protected function addClassPropertyReadOnly(ClassType $class, NamedObjectType $type): void
    {
        $property = (new ObjectTypeProperty('readOnlyProperties', new ArrayType(new StringType())))->setDefaultValue([]);
        $this->addClassProperty($class, $type, $property);

        $addMethod = $class->addMethod('addReadOnlyProperty')
            ->addBody('if(!in_array($property, $this->readOnlyProperties, true)) {')
            ->addBody('    $this->readOnlyProperties[] = $property;')
            ->addBody('}')
            ->addBody('return $this;')
            ->setReturnType($type->getFullQualifiedPhpType())
            ->setVisibility(ClassType::VISIBILITY_PUBLIC)
            ->addComment('@param string $property')
            ->addComment(sprintf('@return %s', $type->getPhpType()))
        ;

        $addParam = $addMethod->addParameter('property')
            ->setType('string')
        ;
    }

    /**
     * @param ClassType $class
     * @param NamedObjectType $type
     */
    protected function addClassConstructor(ClassType $class, NamedObjectType $type): void
    {
        $readOnlyProperties = [];
        foreach ($type->getProperties() as $property) {
            if ($property->isReadOnly() && !in_array($property->getName(), $readOnlyProperties, true)) {
                $readOnlyProperties[] = $property->getName();
            }
        }

        if(count($readOnlyProperties) > 0) {
            $constructor = $class->addMethod('__construct')
                ->setVisibility(ClassType::VISIBILITY_PUBLIC)
                ->addComment('Constructor')
            ;

            $constructor->addBody('$this');
            foreach($readOnlyProperties as $property) {
                $constructor->addBody('    ->addReadOnlyProperty(?)', [$property]);
            }
            $constructor->addBody(';');
        }
    }

    /**
     * @param ObjectTypeProperty $property
     * @return mixed|null
     */
    protected function determineDefaultValue(ObjectTypeProperty $property)
    {
        if ($property->hasDefaultValue()) {
            return $property->getDefaultValue();
        }

        $default = null;
        $type = $property->getType()->getPhpType();
        switch ($type) {
            case AbstractType::STRING:
                if (!in_array($property->getType()->getFormat(), [AbstractFormatType::FORMAT_DATETIME, AbstractFormatType::FORMAT_UUID], true)) {
                    $default = '';
                }
                break;
            case AbstractType::ARRAY:
                $default = [];
                break;
        }

        return $default;
    }
}

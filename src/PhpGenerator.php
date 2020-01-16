<?php
namespace Jtl\OpenApiComponentsGenerator;

use Jtl\OpenApiComponentsGenerator\Type\AbstractFormatType;
use Jtl\OpenApiComponentsGenerator\Type\ArrayType;
use Jtl\OpenApiComponentsGenerator\Type\NamedObjectType;
use Jtl\OpenApiComponentsGenerator\Type\ObjectTypeProperty;
use Jtl\OpenApiComponentsGenerator\Type\StringType;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;

class PhpGenerator
{
    /**
     * @param Schema $schema
     * @param string $destinationDir
     * @throws \Exception
     */
    public static function writeClass(Schema $schema, string $destinationDir): void
    {
        $parentDir = dirname($destinationDir);
        if (!is_dir($parentDir)) {
            throw new \Exception(sprintf('Directory %s does not exist', $parentDir));
        }

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir);
        }

        foreach ($schema->getComponents() as $component) {
            if ($component instanceof NamedObjectType) {
                $file = (new PhpFile())
                    ->addComment('This file is auto generated with the openapi3 component generator from JTL-Software')
                ;
                $namespace = $file->addNamespace($component->getNamespace());
                $class = $namespace->addClass($component->getPhpType());
                self::instantiateClassType($class, $component);
                $classFile = sprintf('%s/%s.php', $destinationDir, $class->getName());
                file_put_contents($classFile, (new PsrPrinter())->printFile($file));
            }
        }
    }

    /**
     * @param ClassType $class
     * @param NamedObjectType $type
     * @return ClassType
     */
    protected static function instantiateClassType(ClassType $class, NamedObjectType $type): ClassType
    {
        foreach ($type->getProperties() as $property) {
            self::addProperty($class, $type, $property);
        }
        return $class;
    }

    /**
     * @param ClassType $class
     * @param NamedObjectType $objectType
     * @param ObjectTypeProperty $property
     */
    protected static function addProperty(ClassType $class, NamedObjectType $objectType, ObjectTypeProperty $property): void
    {
        $commentDataType = '';
        $dataType = null;
        if ($property->getType()->hasPhpType()) {
            $commentDataType = $dataType = $property->getType()->getPhpType();
            if ($property->getType() instanceof NamedObjectType) {
                $dataType = $property->getType()->getFullQualifiedPhpType();
            } elseif ($property->getType() instanceof StringType && $property->getType()->getFormat() === AbstractFormatType::FORMAT_DATETIME) {
                $dataType = 'DateTimeImmutable';
                $commentDataType = '\DateTimeImmutable';
            } elseif ($property->getType() instanceof ArrayType && !is_null($property->getType()->getItemsType())) {
                $commentDataType = sprintf('%s[]', $property->getType()->getItemsType()->getPhpType());
            }
        }

        $classProperty = $class->addProperty($property->getName());
        if ($property->hasDescription()) {
            $classProperty->addComment(sprintf('%s%s', $property->getDescription(), PHP_EOL));
        }
        $classProperty->addComment(sprintf('@var %s', $commentDataType));

        $getMethod = $class->addMethod(sprintf('get%s', ucfirst($property->getName())))
            ->setBody(sprintf('return $this->%s;', $property->getName()))
            ->setReturnType($dataType)
            ->addComment(sprintf('@return %s', $commentDataType))
        ;

        $setMethod = $class->addMethod(sprintf('set%s', ucfirst($property->getName())))
            ->setBody(sprintf('$this->%s = $%s;%sreturn $this;', $property->getName(), $property->getName(), PHP_EOL))
            ->setReturnType($objectType->getFullQualifiedPhpType())
            ->addComment(sprintf('@param %s $%s', $commentDataType, $property->getName()))
            ->addComment(sprintf('@return %s', $objectType->getPhpType()))
        ;

        $setParam = $setMethod->addParameter($property->getName())
            ->setType($dataType)
        ;
    }
}

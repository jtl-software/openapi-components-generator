<?php
namespace Jtl\OpenApiComponentGenerator\Type;

class ArrayType extends AbstractType
{
    /**
     * @var AbstractType
     */
    protected $itemsType;

    /**
     * ArrayType constructor.
     * @param AbstractType|null $itemsType
     */
    public function __construct(AbstractType $itemsType = null)
    {
        $this->itemsType = $itemsType;
    }

    /**
     * @return string
     */
    public function getOpenApiType(): string
    {
        return self::ARRAY;
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return 'array';
    }

    /**
     * @return AbstractType
     */
    public function getItemsType(): ?AbstractType
    {
        return $this->itemsType;
    }

    /**
     * @return boolean
     */
    public function hasItemsType(): bool
    {
        return !is_null($this->itemsType);
    }
}

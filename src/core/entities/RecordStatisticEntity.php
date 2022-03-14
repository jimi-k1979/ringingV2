<?php

namespace DrlArchive\core\entities;

class RecordStatisticEntity extends Entity
{
    private ?string $name = null;
    private ?string $category = null;
    private bool $showOnIndex = false;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string|null $category
     */
    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return bool
     */
    public function isShowOnIndex(): bool
    {
        return $this->showOnIndex;
    }

    /**
     * @param bool $showOnIndex
     */
    public function setShowOnIndex(bool $showOnIndex): void
    {
        $this->showOnIndex = $showOnIndex;
    }


}

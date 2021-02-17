<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


class TeamEntity extends Entity
{
    private string $name;
    private DeaneryEntity $deanery;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return DeaneryEntity
     */
    public function getDeanery(): DeaneryEntity
    {
        return $this->deanery;
    }

    /**
     * @param DeaneryEntity $deanery
     */
    public function setDeanery(DeaneryEntity $deanery): void
    {
        $this->deanery = $deanery;
    }

}
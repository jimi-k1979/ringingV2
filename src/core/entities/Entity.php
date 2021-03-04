<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


abstract class Entity
{
    protected ?int $id = null;

    /**
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param null|int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

}

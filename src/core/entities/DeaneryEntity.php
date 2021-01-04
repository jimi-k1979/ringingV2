<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


use DrlArchive\core\Exceptions\InvalidEntityPropertyException;

class DeaneryEntity extends Entity
{
    private const LOCATIONS_IN_COUNTY = [
        'north',
        'south',
        'outofcounty',
        'n/a',
    ];

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $region;

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
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @param string $region
     * @throws InvalidEntityPropertyException
     */
    public function setRegion(string $region): void
    {
        $inArray = array_search(
            strtolower($region),
            self::LOCATIONS_IN_COUNTY
        );

        if (is_numeric($inArray)) {
            $this->region = strtolower($region);
        } else {
            throw new InvalidEntityPropertyException(
                'Location in county must be North, South or OutOfCounty'
            );
        }
    }


}
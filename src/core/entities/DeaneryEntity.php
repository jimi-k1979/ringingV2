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
    ];

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $locationInCounty;

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
    public function getLocationInCounty(): string
    {
        return $this->locationInCounty;
    }

    /**
     * @param string $locationInCounty
     * @throws InvalidEntityPropertyException
     */
    public function setLocationInCounty(string $locationInCounty): void
    {
        $inArray = array_search(
            strtolower($locationInCounty),
            self::LOCATIONS_IN_COUNTY
        );

        if (is_numeric($inArray)) {
            $this->locationInCounty = strtolower($locationInCounty);
        } else {
            throw new InvalidEntityPropertyException(
                'Location in county must be North, South or OutOfCounty'
            );
        }
    }


}
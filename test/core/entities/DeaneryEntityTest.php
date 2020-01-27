<?php
declare(strict_types=1);

namespace test\core\entities;

use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\Exceptions\InvalidEntityPropertyException;
use PHPUnit\Framework\TestCase;

class DeaneryEntityTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Entity::class,
            new DeaneryEntity()
        );
    }

    public function testIdProperty(): void
    {
        $deanery = new DeaneryEntity();
        $deanery->setId(4);
        $this->assertEquals(
            4,
            $deanery->getId()
        );
    }

    public function testNameProperty(): void
    {
        $deanery = new DeaneryEntity();
        $deanery->setName('Test team');
        $this->assertEquals(
            'Test team',
            $deanery->getName()
        );
    }

    /**
     * @throws InvalidEntityPropertyException
     */
    public function testLocationInCountyProperty(): void
    {
        $deanery = new DeaneryEntity();
        $deanery->setLocationInCounty('north');
        $this->assertEquals(
            'north',
            $deanery->getLocationInCounty()
        );
    }

    /**
     * @throws InvalidEntityPropertyException
     */
    public function testSouthLocation(): void
    {
        $deanery = new DeaneryEntity();
        $deanery->setLocationInCounty('south');
        $this->assertEquals(
            'south',
            $deanery->getLocationInCounty()
        );
    }

    /**
     * @throws InvalidEntityPropertyException
     */
    public function testOutOfCountyLocation(): void
    {
        $deanery = new DeaneryEntity();
        $deanery->setLocationInCounty('outofcounty');
        $this->assertEquals(
            'outofcounty',
            $deanery->getLocationInCounty()
        );
    }

    /**
     * @throws InvalidEntityPropertyException
     */
    public function testExceptionForBadLocation(): void
    {
        $deanery = new DeaneryEntity();
        $this->expectException(InvalidEntityPropertyException::class);
        $deanery->setLocationInCounty('east');
    }
}

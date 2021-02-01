<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\Entity;
use DrlArchive\core\Exceptions\InvalidEntityPropertyException;
use DrlArchive\TestConstants;
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
        $deanery->setId(TestConstants::TEST_DEANERY_ID);
        $this->assertEquals(
            TestConstants::TEST_DEANERY_ID,
            $deanery->getId()
        );
    }

    public function testNameProperty(): void
    {
        $deanery = new DeaneryEntity();
        $deanery->setName(TestConstants::TEST_DEANERY_NAME);
        $this->assertEquals(
            TestConstants::TEST_DEANERY_NAME,
            $deanery->getName()
        );
    }

    /**
     * @throws InvalidEntityPropertyException
     */
    public function testLocationInCountyProperty(): void
    {
        $deanery = new DeaneryEntity();
        $deanery->setRegion(TestConstants::TEST_DEANERY_REGION);
        $this->assertEquals(
            TestConstants::TEST_DEANERY_REGION,
            $deanery->getRegion()
        );
    }

    /**
     * @throws InvalidEntityPropertyException
     */
    public function testNorthLocation(): void
    {
        $deanery = new DeaneryEntity();
        $deanery->setRegion('north');
        $this->assertEquals(
            'north',
            $deanery->getRegion()
        );
    }

    /**
     * @throws InvalidEntityPropertyException
     */
    public function testOutOfCountyLocation(): void
    {
        $deanery = new DeaneryEntity();
        $deanery->setRegion('outOfCounty');
        $this->assertEquals(
            'outofcounty',
            $deanery->getRegion()
        );
    }

    /**
     * @throws InvalidEntityPropertyException
     */
    public function testExceptionForBadLocation(): void
    {
        $deanery = new DeaneryEntity();
        $this->expectException(InvalidEntityPropertyException::class);
        $deanery->setRegion('east');
    }
}

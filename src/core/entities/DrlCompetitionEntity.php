<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;


class DrlCompetitionEntity extends AbstractCompetitionEntity
{

    public function toArray(): array
    {
        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'isSingleTowerCompetition' => $this->singleTowerCompetition,
        ];

        if ($this->singleTowerCompetition) {
            $array['usualLocation'] = [
                'id' => $this->usualLocation->getId(),
                'location' => $this->usualLocation->getLocation(),
                'deanery' => [
                    'id' => $this->usualLocation->getDeanery()->getId(),
                    'name' => $this->usualLocation->getDeanery()->getName(),
                    'locationInCounty' => $this->usualLocation
                        ->getDeanery()
                        ->getLocationInCounty(),
                ],
                'dedication' => $this->usualLocation->getDedication(),
                'numberOfBells' => $this->usualLocation->getNumberOfBells(),
                'tenorWeight' => $this->usualLocation->getTenorWeight(),
            ];
        } else {
            $array['usualLocation'] = null;
        }

        return $array;
    }
}

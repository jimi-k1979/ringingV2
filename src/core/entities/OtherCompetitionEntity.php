<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;


class OtherCompetitionEntity extends AbstractCompetitionEntity
{

    private string $rules;

    /**
     * @return string
     */
    public function getRules(): string
    {
        return $this->rules;
    }

    /**
     * @param string $rules
     */
    public function setRules(string $rules): void
    {
        $this->rules = $rules;
    }

    public function toArray(): array
    {
        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'isSingleTowerCompetition' => $this->singleTowerCompetition,
            'rules' => $this->rules
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
                        ->getRegion(),
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

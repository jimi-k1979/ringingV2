<?php

declare(strict_types=1);

require_once __DIR__ . '/../init.php';

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\judgePage\JudgePageRequest;
use DrlArchive\core\interactors\pages\judgePage\JudgePageResponse;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\JudgePageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;


$presenter = new class extends AbstractTwigPagePresenter {
    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_STATS;

        if ($response->getStatus() === Response::STATUS_SUCCESS) {
            $this->dataForTemplate[self::JUDGE] =
                $response->getData()[JudgePageResponse::DATA_JUDGE];
            $this->dataForTemplate[self::EVENTS] =
                $response->getData()[JudgePageResponse::DATA_EVENTS];
            $this->dataForTemplate[self::STATS] =
                $response->getData()[JudgePageResponse::DATA_STATS];
            try {
                $this->twig->display(
                    'statistics/judge.twig',
                    $this->dataForTemplate
                );
            } catch (Throwable $e) {
                echo $response->getMessage();
                die();
            }
        } else {
            echo $response->getMessage();
        }
    }
};

$request = new JudgePageRequest();
$request->setJudgeId((int)$_GET['judgeId']);

$useCase = (new JudgePageFactory())->create(
    $presenter,
    $request
);
$useCase->execute();

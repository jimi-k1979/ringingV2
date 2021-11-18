<?php

declare(strict_types=1);

require_once __DIR__ . '/../init.php';

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\viewComposition\ViewCompositionRequest;
use DrlArchive\core\interactors\pages\viewComposition\ViewCompositionResponse;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\delivery\services\pdfCreatorService;
use DrlArchive\implementation\factories\interactors\pages\ViewCompositionFactory;

$presenter = new class implements PresenterInterface {
    private const FIRST_PAGE_ROWS = 20;
    private const OTHER_PAGE_ROWS = 24;

    private array $data;
    private array $pages = [];
    private array $html = [];
    private array $changes = [];

    public function send(?Response $response = null): void
    {
        if ($response->getStatus() === Response::STATUS_SUCCESS) {
            $this->data = $response->getData();
            try {
                $this->generateHtml();
            } catch (Throwable $e) {
                http_response_code(500);
                echo $e->getMessage();
                die();
            }

            header('Content-Type: application/pdf');
            pdfCreatorService::service()->setPdfMetaData(
                'Call Change Composition',
                $this->data[ViewCompositionResponse::DATA_COMPOSITION_NAME],
                'Call change composition, ' . $this->data[ViewCompositionResponse::DATA_COMPOSITION_NAME]
            );
            foreach ($this->pages as $page) {
                pdfCreatorService::service()->newPage()
                    ->addHtml($page);
            }
            pdfCreatorService::service()->outputToScreen(
                'composition.pdf'
            );
            exit();
        }
        http_response_code($response->getStatus());
        echo $response->getMessage();
    }

    private function generateHtml(): void
    {
        $rows = ceil(
            $this->data[ViewCompositionResponse::DATA_NUMBER_OF_CHANGES] / 4
        );

        if ($rows <= self::FIRST_PAGE_ROWS) {
            $numberOfPages = 1;
            $additionalRows = 0;
        } else {
            $additionalRows = $rows - self::FIRST_PAGE_ROWS;
            $numberOfPages = ceil($additionalRows / self::OTHER_PAGE_ROWS) + 1;
        }

        for ($i = 0; $i < $numberOfPages; $i++) {
            $this->html = [];
            if ($i === 0) {
                $this->html[] = <<<html
<h1 style="text-align: center">{$this->data[ViewCompositionResponse::DATA_COMPOSITION_NAME]}</h1>
<h3 style="text-align: center">({$this->data[ViewCompositionResponse::DATA_NUMBER_OF_CHANGES]} changes)</h3>
html;
                if (
                    !empty($this->data[ViewCompositionResponse::DATA_DESCRIPTION])
                ) {
                    $this->html[] = "<p style=\"text-align: center\">{$this->data[ViewCompositionResponse::DATA_DESCRIPTION]}</p>";
                }
            }
            $this->html[] = "<table>";

            $this->changes = $this->data[ViewCompositionResponse::DATA_CHANGES];

            if ($i === 0) {
                if ($rows <= self::FIRST_PAGE_ROWS) {
                    for ($j = 0; $j < $rows; $j++) {
                        $this->htmlRowBuilder(
                            $j,
                            (int)($j + $rows),
                            (int)($j + ($rows * 2)),
                            (int)($j + ($rows * 3))
                        );
                    }
                } else {
                    for ($j = 0; $j < self::FIRST_PAGE_ROWS; $j++) {
                        $this->htmlRowBuilder(
                            $j,
                            $j + self::FIRST_PAGE_ROWS,
                            $j + (self::FIRST_PAGE_ROWS * 2),
                            $j + (self::FIRST_PAGE_ROWS * 3)
                        );
                    }
                }
            } elseif ($i === (int)$numberOfPages - 1) {
                $rowsOnLastPage = $additionalRows % self::OTHER_PAGE_ROWS;
                for ($j = 0; $j < $rowsOnLastPage; $j++) {
                    $firstCell =
                        (
                            (self::OTHER_PAGE_ROWS * 4) * ($i - 1)
                        ) + (self::FIRST_PAGE_ROWS * 4);
                    $this->htmlRowBuilder(
                        $firstCell + $j,
                        $firstCell + $j + $rowsOnLastPage,
                        $firstCell + $j + ($rowsOnLastPage * 2),
                        $firstCell + $j + ($rowsOnLastPage * 3)
                    );
                }
            } else {
                for ($j = 0; $j < self::OTHER_PAGE_ROWS; $j++) {
                    $firstCell =
                        (
                            (self::OTHER_PAGE_ROWS * 4) * ($i - 1)
                        ) + (self::FIRST_PAGE_ROWS * 4);
                    $this->htmlRowBuilder(
                        $firstCell + $j,
                        $firstCell + $j + self::OTHER_PAGE_ROWS,
                        $firstCell + $j + (self::OTHER_PAGE_ROWS * 2),
                        $firstCell + $j + (self::OTHER_PAGE_ROWS * 3)
                    );
                }
            }

            $this->html[] = '</table>';

            $this->pages[] = implode("\n", $this->html);
        }
    }

    private function htmlRowBuilder(
        int $firstColumn,
        int $secondColumn,
        int $thirdColumn,
        int $fourthColumn
    ): void {
        $firstChange = $this->changes[$firstColumn][ViewCompositionResponse::DATA_CHANGE_TEXT];

        if (isset($this->changes[$secondColumn])) {
            $secondChange = $this->changes[$secondColumn][ViewCompositionResponse::DATA_CHANGE_TEXT];
        } else {
            $secondChange = '&nbsp;';
        }

        if (isset($this->changes[$thirdColumn])) {
            $thirdChange = $this->changes[$thirdColumn][ViewCompositionResponse::DATA_CHANGE_TEXT];
        } else {
            $thirdChange = '&nbsp;';
        }

        if (isset($this->changes[$fourthColumn])) {
            $fourthChange = $this->changes[$fourthColumn][ViewCompositionResponse::DATA_CHANGE_TEXT];
        } else {
            $fourthChange = '&nbsp;';
        }

        $this->html[] = <<<html
<tr>
<td style="text-align: center; font-size: 23px">
$firstChange
</td>
<td style="text-align: center; font-size: 23px">
$secondChange
</td>
<td style="text-align: center; font-size: 23px">
$thirdChange
</td>
<td style="text-align: center; font-size: 23px">
$fourthChange
</td>
</tr>
html;
    }
};

$request = new ViewCompositionRequest();
$request->setCompositionId((int)$_GET['id']);
if ($_GET['direction'] === 'down') {
    $request->setUpChanges(false);
}

$useCase = (new ViewCompositionFactory())->create(
    $presenter,
    $request
);
$useCase->execute();


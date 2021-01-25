<?php

declare(strict_types=1);


use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\event\newEventPage\NewEventPageRequest;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\implementation\factories\interactors\event\NewEventPageFactory;

require_once __DIR__ . '/../init.php';

if (!empty($_POST)) {
    $request = new NewEventPageRequest();
    $request->setYear($_POST['year-text-search']);
    $request->setCompetitionId((int)$_POST['competition-id']);
    $request->setLocationId((int)$_POST['location-id']);
    $request->setUsualLocation((int)$_POST['usual-location-id'] ?? null);
    for ($i = 1; $i <= $_POST['number-of-teams']; $i++) {
        $request->addResultsRow(
            (int)$_POST['position-' . $i],
            (float)$_POST['faults-' . $i],
            $_POST['team-' . $i],
            (int)$_POST['peal-' . $i] ?? null
        );
    }
} else {
    $request = null;
}

$presenter = new class implements PresenterInterface {

    public function send(?Response $response = null)
    {
        global $twig;

        if ($response->getStatus() === Response::STATUS_FORBIDDEN) {
            header('Location:./resultArchive.php');
            exit;
        } elseif ($response->getStatus() === Response::STATUS_SUCCESS) {
            if (!empty($response->getData())) {
                $_SESSION['message'] =
                    'Success! New event added to the archive.';
                header('Location:./newEvent.php');
                exit;
            }
            $message = $_SESSION['message'] ?? '';
            unset($_SESSION['message']);
            try {
                echo $twig->render(
                    'events/newEvent.twig',
                    [
                        'maxYear' => new DateTime(),
                        'message' => $message,
                    ]
                );
            } catch (Throwable $e) {
                include __DIR__ . '/../templates/failed.html';
            }
        } else {
            $_SESSION['message'] =
                'There was a problem: ' . $response->getMessage();
            header('Location:./newEvent.php');
            exit;
        }
    }
};

$useCase = (new NewEventPageFactory())->create(
    $presenter,
    $request,
);
$useCase->execute();

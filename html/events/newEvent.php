<?php

declare(strict_types=1);

require_once(__DIR__ . '/../init.php');

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\event\newEventPage\NewEventPageRequest;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\implementation\factories\interactors\event\NewEventPageFactory;

if (!empty($_POST)) {
    $request = new NewEventPageRequest();
    $request->setYear($_POST['year-text-search']);
    $request->setCompetitionId((int)$_POST['competition-id']);
    $request->setLocationId((int)$_POST['location-id']);
    $request->setUsualLocation((int)$_POST['usual-location-id'] ?? null);
    for ($i = 1; $i <= $_POST['number-of-teams']; $i++) {
        $request->addResultsRow(
            isset($_POST['position-' . $i])
                ? (int)$_POST['position-' . $i] : $i,
            (float)$_POST['faults-' . $i],
            $_POST['team-' . $i],
            isset($_POST['peal-' . $i])
                ? (int)$_POST['peal-' . $i] : null
        );
    }
} else {
    $request = null;
}

$presenter = new class implements PresenterInterface {

    public function send(?Response $response = null): void
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
            $status = $_SESSION['status'] ?? 200;
            unset($_SESSION['message']);
            unset($_SESSION['status']);
            try {
                echo $twig->render(
                    'events/newEvent.twig',
                    [
                        'maxYear' => new DateTime(),
                        'message' => $message,
                        'status' => $status,
                    ]
                );
            } catch (Throwable $e) {
                include __DIR__ . '/../templates/failed.html';
            }
        } else {
            $_SESSION['message'] = $response->getMessage();
            $_SESSION['status'] = $response->getStatus();
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

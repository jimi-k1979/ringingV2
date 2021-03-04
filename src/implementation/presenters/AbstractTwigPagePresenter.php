<?php

declare(strict_types=1);

namespace DrlArchive\implementation\presenters;


use DrlArchive\core\classes\Response;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AbstractTwigPagePresenter implements PresenterInterface
{
    protected Environment $twig;
    protected bool $loggedInStatus;

    /**
     * AbstractTwigPagePresenter constructor.
     */
    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../../html/templates');
        $this->twig = new Environment(
            $loader,
            [
            ]
        );
    }

    public function send(?Response $response = null): void
    {
        $this->loggedInStatus = is_numeric(
            $response->getLoggedInUser()->getId()
        );
    }
}

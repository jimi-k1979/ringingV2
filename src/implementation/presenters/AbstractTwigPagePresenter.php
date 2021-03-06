<?php

declare(strict_types=1);

namespace DrlArchive\implementation\presenters;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AbstractTwigPagePresenter implements PresenterInterface
{
    protected Environment $twig;
    protected array $dataForTemplate = [];

    /**
     * AbstractTwigPagePresenter constructor.
     */
    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../../templates');
        $this->twig = new Environment(
            $loader
        );

        $this->dataForTemplate = [
            'error' => [],
        ];
    }

    public function send(?Response $response = null): void
    {
        $loggedInStatus = false;
        if (!empty($response->getLoggedInUser())) {
            $loggedInStatus = is_numeric(
                $response->getLoggedInUser()->getId()
            );
            $this->dataForTemplate['user'] = [
                'userId' =>
                    $response->getLoggedInUser()->getId(),
                'permission' => [
                    'isAdd' =>
                        $response->getLoggedInUser()
                            ->getPermissions()[UserEntity::ADD_NEW_PERMISSION],
                    'isEdit' =>
                        $response->getLoggedInUser()
                            ->getPermissions()[UserEntity::EDIT_EXISTING_PERMISSION],
                    'isApprove' =>
                        $response->getLoggedInUser()
                            ->getPermissions()[UserEntity::APPROVE_EDIT_PERMISSION],
                    'isDelete' =>
                        $response->getLoggedInUser()
                            ->getPermissions()[UserEntity::CONFIRM_DELETE_PERMISSION],
                ],
            ];
        }

        $this->dataForTemplate['user']['isLoggedIn'] = $loggedInStatus;
    }
}

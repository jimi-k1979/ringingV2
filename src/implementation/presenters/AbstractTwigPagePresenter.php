<?php

declare(strict_types=1);

namespace DrlArchive\implementation\presenters;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

class AbstractTwigPagePresenter implements PresenterInterface
{
    public const ERROR = 'error';
    public const ERROR_MESSAGE = 'message';
    public const ERROR_TYPE = 'type';
    public const EVENT = 'event';
    public const EVENTS = 'events';
    public const JUDGE = 'judge';
    public const MESSAGING = 'messaging';
    public const NAV = 'nav';
    public const NAV_HIGHLIGHTED = 'highlighted';
    public const RESULTS = 'results';
    public const RINGER = 'ringer';
    public const SETTINGS = 'settings';
    public const STATS = 'stats';
    public const STATS_OPTIONS = 'statsOptions';
    public const TEAM = 'team';
    public const TEMPLATES = 'templates';
    public const TEMPLATES_BODY = 'body';
    public const USER = 'user';
    public const USER_EMAIL_ADDRESS = 'emailAddress';
    public const USER_ID = 'userId';
    public const USER_IS_LOGGED_IN = 'isLoggedIn';
    public const USER_PERMISSION = 'permission';
    public const PERMISSION_IS_ADD = 'isAdd';
    public const PERMISSION_IS_EDIT = 'isEdit';
    public const PERMISSION_IS_APPROVE = 'isApprove';
    public const PERMISSION_IS_DELETE = 'isDelete';

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
        $this->twig->addExtension(new IntlExtension());

        $this->dataForTemplate = [
            self::ERROR => [],
        ];
    }

    public function send(?Response $response = null): void
    {
        $loggedInStatus = false;
        if (!empty($response->getLoggedInUser())) {
            $loggedInStatus = is_numeric(
                $response->getLoggedInUser()->getId()
            );
            $this->dataForTemplate[self::USER] = [
                self::USER_ID =>
                    $response->getLoggedInUser()->getId(),
                self::USER_PERMISSION => [
                    self::PERMISSION_IS_ADD =>
                        $response->getLoggedInUser()
                            ->getPermissions()[UserEntity::ADD_NEW_PERMISSION],
                    self::PERMISSION_IS_EDIT =>
                        $response->getLoggedInUser()
                            ->getPermissions()[UserEntity::EDIT_EXISTING_PERMISSION],
                    self::PERMISSION_IS_APPROVE =>
                        $response->getLoggedInUser()
                            ->getPermissions()[UserEntity::APPROVE_EDIT_PERMISSION],
                    self::PERMISSION_IS_DELETE =>
                        $response->getLoggedInUser()
                            ->getPermissions()[UserEntity::CONFIRM_DELETE_PERMISSION],
                ],
            ];
        }

        $this->dataForTemplate[self::USER][self::USER_IS_LOGGED_IN] =
            $loggedInStatus;
    }
}

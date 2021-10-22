<?php

declare(strict_types=1);

namespace DrlArchive;


class Implementation
{
    public const DEFAULT_EMAIL_FROM_ADDRESS = 'site@' . Config::HOST_NAME;
    public const LOGIN_PERSISTENCE = 3600 * 24 * 7;
    public const USE_SMTP_MAILER = true;

    public const ALERT_DANGER = 'alert-danger';
    public const ALERT_SUCCESS = 'alert-success';

    public const REFERER_HOST = 'host';
    public const REFERER_PATH = 'path';

    public const SESSION_AUTH_LOGGED_IN = 'auth_logged_in';
    public const SESSION_MESSAGE = 'message';
    public const SESSION_STATUS = 'status';
    public const SESSION_REDIRECT_TO = 'redirectTo';

    public const NAV_HIGHLIGHT_HOME = 'home';
    public const NAV_HIGHLIGHT_COMPOSITIONS = 'compositions';
    public const NAV_HIGHLIGHT_ARCHIVE = 'archive';
    public const NAV_HIGHLIGHT_STATS = 'statistics';
    public const NAV_HIGHLIGHT_LEAGUE = 'league';
    public const NAV_HIGHLIGHT_EXTENDED_RINGS = 'extendedRings';
    public const NAV_HIGHLIGHT_LADDER = 'ladder';
    public const NAV_HIGHLIGHT_ACCOUNT = 'account';
    public const NAV_HIGHLIGHT_LOGIN = 'logIn';
    
}

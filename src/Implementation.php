<?php

declare(strict_types=1);

namespace DrlArchive;


class Implementation
{
    public const DEFAULT_EMAIL_FROM_ADDRESS = 'site@' . Config::HOST_NAME;
    public const USE_SMTP_MAILER = true;

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

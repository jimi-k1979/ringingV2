<?php

declare(strict_types=1);

namespace DrlArchive;


use DrlArchive\core\entities\UserEntity;

class TestConstants
{
    public const TEST_LOCATION_ID = 999;
    public const TEST_LOCATION_NAME = 'Test tower';
    public const TEST_LOCATION_DEDICATION = 'S Test';
    public const TEST_LOCATION_WEIGHT = 'test cwt';
    public const TEST_LOCATION_NUMBER_OF_BELLS = 6;

    public const TEST_DEANERY_ID = 123;
    public const TEST_DEANERY_NAME = 'Test deanery';
    public const TEST_DEANERY_REGION = 'south';

    public const TEST_DRL_COMPETITION_ID = 999;
    public const TEST_DRL_COMPETITION_NAME = 'Test competition';
    public const TEST_DRL_SINGLE_TOWER_COMPETITION = false;
    public const TEST_DRL_COMPETITION_NO_OF_BELLS = '6';

    public const TEST_EVENT_ID = 1234;
    public const TEST_EVENT_YEAR = '1970';
    public const TEST_EVENT_UNUSUAL_TOWER = false;
    public const TEST_EVENT_TOTAL_FAULTS = 123.5;
    public const TEST_EVENT_MEAN_FAULTS = 12.3;
    public const TEST_EVENT_WINNING_MARGIN = 5.5;

    public const TEST_TEAM_ID = 123;
    public const TEST_TEAM_NAME = 'Test team';

    public const TEST_RINGER_ID = 4321;
    public const TEST_RINGER_FIRST_NAME = 'Test';
    public const TEST_RINGER_LAST_NAME = 'Ringer';
    public const TEST_RINGER_NOTES = 'Known as Dinsdale';

    public const TEST_JUDGE_ID = 4321;
    public const TEST_JUDGE_FIRST_NAME = 'Test';
    public const TEST_JUDGE_LAST_NAME = 'Judge';

    public const TEST_RESULT_ID = 123;
    public const TEST_RESULT_POSITION = 1;
    public const TEST_RESULT_FAULTS = 10.25;
    public const TEST_RESULT_PEAL_NUMBER = 1;
    public const TEST_RESULT_POINTS = 10;

    public const TEST_OTHER_COMPETITION_ID = 888;
    public const TEST_OTHER_COMPETITION_NAME = 'Test other competition';
    public const TEST_OTHER_SINGLE_TOWER_COMPETITION = true;

    public const TEST_USER_ID = 555;
    public const TEST_USER_EMAIL = 'testUser@example.com';
    public const TEST_USER_USERNAME = 'testUser';
    public const TEST_USER_PASSWORD = 'testPassword';
    public const TEST_USER_SUPER_ADMIN_ROLE = [
        UserEntity::ADD_NEW_PERMISSION => true,
        UserEntity::EDIT_EXISTING_PERMISSION => true,
        UserEntity::APPROVE_EDIT_PERMISSION => true,
        UserEntity::CONFIRM_DELETE_PERMISSION => true,
    ];

    public const TEST_REDIRECT_TO = 'newPage.php';

    public const TEST_WINNING_RINGER_ID = 777;
    public const TEST_WINNING_RINGER_BELL = '1';

}

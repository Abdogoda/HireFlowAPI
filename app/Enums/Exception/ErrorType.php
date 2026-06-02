<?php

namespace App\Enums\Exception;

enum ErrorType: string
{
    // default
    case UNKNOWN_ERROR = 'unknown_error';

    // 400 Bad Request
    case OTP_INVALID                = 'otp_invalid';
    case PERIOD_HAS_ENDED           = 'period_has_ended';
    case PERIOD_HAS_NOT_STARTED     = 'period_has_not_started';
    case COURSE_HAS_ENDED           = 'course_has_ended';
    case COURSE_HAS_STARTED         = 'course_has_started';
    case COURSE_HAS_NOT_STARTED     = 'course_has_not_started';
    case ACTIVITY_HAS_ENDED         = 'activity_has_ended';
    case ACTIVITY_HAS_NOT_STARTED   = 'activity_has_not_started';
    case LESSON_HAS_COMPLETED       = 'lesson_has_completed';
    case LESSON_HAS_POSTPONED       = 'lesson_has_postponed';
    case LESSON_HAS_CANCELLED       = 'lesson_has_cancelled';
    case LESSON_IS_ONGOING          = 'lesson_is_ongoing';
    case LESSON_HAS_NOT_YET_STARTED = 'lesson_has_not_yet_started';

    // 401 Unauthorized
    case UNAUTHENTICATED     = 'unauthenticated';
    case INVALID_CREDENTIALS = 'invalid_credentials';

    // 403 Forbidden
    case NOT_AUTHORIZED             = 'not_authorized';
    case USER_NOT_ACCEPTED          = 'user_not_accepted';
    case USER_IS_BLACKLISTED        = 'user_is_blacklisted';
    case PERIOD_UPDATE_IS_LOCKED    = 'period_update_is_locked';
    case ROLES_ASSIGNED_TO_USERS    = 'roles_assigned_to_users';
    case SKILLS_ASSIGNED_TO_USERS   = 'skills_assigned_to_users';
    case SKILLS_ASSIGNED_TO_COURSES = 'skills_assigned_to_courses';
    case TEAM_HAS_USERS             = 'team_has_users';
    case TEAM_HAS_MIXED_GENDER      = 'team_has_mixed_gender';
    case TEAM_HAS_LEADER            = 'team_has_leader';
    case USER_HAS_TEAM              = 'user_has_team';
    case USER_NOT_TEAM_MEMBER       = 'user_not_team_member';
    case INVALID_DOMAIN             = 'invalid_domain';

    // Configuration
    case CONFIGURATION_KEY_NOT_EXISTS = 'configuration_key_not_exists';

    // 404 Not Found
    case MODEL_NOT_FOUND = 'model_not_found';

    // 409 Conflict
    case PERIOD_ALREADY_ACTIVATED                  = 'period_already_activated';
    case PERIOD_ALREADY_DEACTIVATED                = 'period_already_deactivated';
    case COURSE_ALREADY_ACTIVATED                  = 'course_already_activated';
    case COURSE_HAS_NO_UNCOMPLETED_LESSONS_TO_HOLD = 'course_has_no_uncompleted_lessons_to_hold';
    case COURSE_ALREADY_DEACTIVATED                = 'course_already_deactivated';
    case ACTIVITY_ALREADY_ACTIVATED                = 'activity_already_activated';
    case ACTIVITY_ALREADY_DEACTIVATED              = 'activity_already_deactivated';
    case DOCUMENT_ALREADY_EXISTS                   = 'document_already_exists';

    // 410 Gone
    case OTP_EXPIRED = 'otp_expired';

    // 422 Unprocessable Entity
    case VALIDATION_ERROR = 'validation_error';

    // 500 Internal Server Error
    case INTERNAL_SERVER_ERROR = 'internal_server_error';

    /**
     * Map this error type to a suggested HTTP status code.
     */
    public function toHttpStatus(): int
    {
        return match ($this) {
            // 400 Bad Request
            self::OTP_INVALID,
            self::PERIOD_HAS_ENDED,
            self::PERIOD_HAS_NOT_STARTED,
            self::COURSE_HAS_ENDED,
            self::COURSE_HAS_STARTED,
            self::COURSE_HAS_NOT_STARTED,
            self::ACTIVITY_HAS_ENDED,
            self::ACTIVITY_HAS_NOT_STARTED,
            self::LESSON_HAS_COMPLETED,
            self::LESSON_HAS_POSTPONED,
            self::LESSON_HAS_CANCELLED,
            self::LESSON_IS_ONGOING,
            self::LESSON_HAS_NOT_YET_STARTED => 400,

            // 401 Unauthorized
            self::UNAUTHENTICATED,
            self::INVALID_CREDENTIALS => 401,

            // 403 Forbidden
            self::NOT_AUTHORIZED,
            self::USER_NOT_ACCEPTED,
            self::USER_IS_BLACKLISTED,
            self::PERIOD_UPDATE_IS_LOCKED,
            self::ROLES_ASSIGNED_TO_USERS,
            self::SKILLS_ASSIGNED_TO_USERS,
            self::SKILLS_ASSIGNED_TO_COURSES,
            self::TEAM_HAS_USERS,
            self::TEAM_HAS_MIXED_GENDER,
            self::TEAM_HAS_LEADER,
            self::USER_HAS_TEAM,
            self::USER_NOT_TEAM_MEMBER,
            self::INVALID_DOMAIN => 403,

            // 404 Not Found
            self::MODEL_NOT_FOUND => 404,

            // 409 Conflict
            self::PERIOD_ALREADY_ACTIVATED,
            self::PERIOD_ALREADY_DEACTIVATED,
            self::COURSE_ALREADY_ACTIVATED,
            self::COURSE_HAS_NO_UNCOMPLETED_LESSONS_TO_HOLD,
            self::COURSE_ALREADY_DEACTIVATED,
            self::ACTIVITY_ALREADY_ACTIVATED,
            self::ACTIVITY_ALREADY_DEACTIVATED,
            self::DOCUMENT_ALREADY_EXISTS => 409,

            // 410 Gone
            self::OTP_EXPIRED => 410,

            // 422 Unprocessable Entity
            self::VALIDATION_ERROR => 422,

            // default / 500
            default => 500,
        };
    }
}

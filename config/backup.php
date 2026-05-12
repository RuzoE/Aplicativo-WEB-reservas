<?php

return [

    'backup' => [
        'name' => env('BACKUP_NAME', env('APP_NAME', 'hotel-piloto-sam')),

        'source' => [
            'files' => [
                'include' => array_values(array_filter([
                    env('BACKUP_INCLUDE_FILES', true) ? base_path() : null,
                ])),

                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                    storage_path('app/backup-temp'),
                    storage_path('app/backup'),
                ],

                'follow_links' => false,
                'ignore_unreadable_directories' => true,
                'relative_path' => base_path(),
            ],

            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],

        'database_dump_compressor' => null,
        'database_dump_file_timestamp_format' => 'Y-m-d-H-i-s',
        'database_dump_filename_base' => 'database',
        'database_dump_file_extension' => 'sql',

        'destination' => [
            'compression_method' => ZipArchive::CM_DEFLATE,
            'compression_level' => 9,
            'filename_prefix' => 'backup-',
            'disks' => [
                's3',
                'google',
            ],
        ],

        'temporary_directory' => storage_path('app/backup-temp'),
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),
        'encryption' => env('BACKUP_ARCHIVE_PASSWORD') ? 'default' : false,
        'tries' => 3,
        'retry_delay' => 10,
    ],

    'notifications' => [
        'notifications' => [
            Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => [],
            Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => [],
            Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => [],
        ],

        'notifiable' => Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_NOTIFICATION_EMAIL', env('MAIL_FROM_ADDRESS', '')),

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Backup Bot'),
            ],
        ],

        'slack' => [
            'webhook_url' => env('BACKUP_SLACK_WEBHOOK_URL', ''),
            'channel' => env('BACKUP_SLACK_CHANNEL'),
            'username' => env('BACKUP_SLACK_USERNAME'),
            'icon' => env('BACKUP_SLACK_ICON'),
        ],

        'discord' => [
            'webhook_url' => env('BACKUP_DISCORD_WEBHOOK_URL', ''),
            'username' => env('BACKUP_DISCORD_USERNAME', ''),
            'avatar_url' => env('BACKUP_DISCORD_AVATAR_URL', ''),
        ],
    ],

    'monitor_backups' => [
        [
            'name' => env('BACKUP_NAME', env('APP_NAME', 'hotel-piloto-sam')),
            'disks' => ['s3', 'google'],
            'health_checks' => [
                Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 2,
                Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 12000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => App\Backup\Strategies\RetentionCleanupStrategy::class,

        'default_strategy' => [
            'keep_all_backups_for_days' => 30,
            'keep_daily_backups_for_days' => 0,
            'keep_weekly_backups_for_weeks' => 0,
            'keep_monthly_backups_for_months' => 0,
            'keep_yearly_backups_for_years' => 0,
            'delete_oldest_backups_when_using_more_megabytes_than' => 12000,
        ],

        'max_backups' => 40,
        'tries' => 3,
        'retry_delay' => 10,
    ],

    'default_frequency' => env('BACKUP_FREQUENCY', 'daily'),
    'ui_path' => env('BACKUP_UI_PATH', ''),
    'runtime' => [
        'memory_limit' => env('BACKUP_MEMORY_LIMIT', '1024M'),
        'process_timeout' => (int) env('BACKUP_PROCESS_TIMEOUT', 600),
    ],

];

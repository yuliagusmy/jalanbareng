<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Audit Logging Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable audit logging globally. When disabled, no audit logs
    | will be created.
    |
    */
    'enabled' => env('AUDIT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Exclude Timestamps from Audit
    |--------------------------------------------------------------------------
    |
    | When true, created_at and updated_at fields will not be included in
    | the old_values and new_values comparison.
    |
    */
    'exclude_timestamps' => true,

    /*
    |--------------------------------------------------------------------------
    | Audit Log Retention Days
    |--------------------------------------------------------------------------
    |
    | How many days to keep audit logs before automatic cleanup.
    | Set to null to keep logs forever.
    |
    */
    'retention_days' => env('AUDIT_RETENTION_DAYS', 365),
];

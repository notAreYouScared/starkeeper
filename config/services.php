<?php

return [

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'discord' => [
        'client_id'               => env('DISCORD_CLIENT_ID'),
        'client_secret'           => env('DISCORD_CLIENT_SECRET'),
        'redirect'                => env('DISCORD_REDIRECT_URI', '/auth/discord/callback'),
        'bot_token'               => env('DISCORD_BOT_TOKEN'),
        'guild_id'                => env('DISCORD_GUILD_ID'),
        // Minimum Discord role ID a user must hold to be eligible for roster sync.
        // Update this value to the Discord snowflake ID of your org's "Member" role.
        'minimum_member_role_id'  => '1303561707196649512',
    ],

];

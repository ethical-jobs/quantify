<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Disable reporters during testing
     |--------------------------------------------------------------------------
    */
    'testing-disabled' => true,

    /*
     |--------------------------------------------------------------------------
     | Reporting channels
     |--------------------------------------------------------------------------
     |
     | Channels settings for report notifications
     |
     */
    'channels' => [
        'slack' => [
            'channel'   => '#general',
            'from'      => 'Quantify',
            'icon'      => ':zap:',
            'hook'      => 'https://hooks.slack.com/services/.../...',
        ],
    ],
];

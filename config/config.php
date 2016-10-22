<?php

return [
    /*
     * Contacts
     */
    'follower' => [
        /*
         * Relationship table for followers and followables
         */
        'table' => 'followables',

        /*
         * Cache follower/following counts
         */
        'cache' => [
            'enable' => true,
            'expiry' => 10,
        ]
    ],
];

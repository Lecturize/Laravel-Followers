<?php

return [
    /*
     * Followers
     */
    'follower' => [
        /*
         * Relationship table for followers and followables
         */
        'table' => 'followers',

        /*
         * Cache follower/following counts
         */
        'cache' => [
            'enable' => true,
            'expiry' => 10,
        ]
    ],
];

<?php

return [

    /**
     * Followers
     */
    'followers' => [

        /*
         * Relationship table for followers and followables.
         */
        'table' => 'followers',

        /*
         * The follower model.
         */
        'model' => \Lecturize\Followers\Models\Follower::class,

        /*
         * Cache follower/following counts.
         */
        'cache' => [
            'enable' => true,
            'expiry' => 10,
        ],

    ],

];

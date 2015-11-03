<?php

return [
	/**
	 * Pivot Table to relate Follower and Followables
	 */
	'table' => 'followables',

	/**
	 * Cache follower/following counts
	 */
	'cache' => [
		'enable' => true,
		'expiry' => 10,
	]
];

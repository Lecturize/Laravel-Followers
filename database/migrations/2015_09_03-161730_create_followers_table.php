<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FollowersTable extends Migration
{
    /**
     * Table name
     */
    private $table;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table = config('followers.table');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function(Blueprint $table)
		{
		    $table->increments('id');

            $table->integer('follower_id')->unsigned()->index();
            $table->string('follower_type');

            $table->integer('followable_id')->unsigned()->index();
            $table->string('followable_type');

            $table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->table);
    }
}


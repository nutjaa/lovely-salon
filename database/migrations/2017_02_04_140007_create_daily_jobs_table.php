<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('task_id');
            $table->text('description');
            $table->boolean('is_loyal_customer');
            $table->integer('customer_id');
            $table->decimal('amount', 7, 2);
            $table->dateTime('task_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_jobs');
    }
}

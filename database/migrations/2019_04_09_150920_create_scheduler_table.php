<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSchedulerTable.
 */
class CreateSchedulerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduler', function (Blueprint $table) {
            $table->increments('id');
            $table->string('caption');
            $table->text('schedule')->nullable();
            $table->string('event')->nullable();
            $table->boolean('enabled')->default(false);
            $table->string('job');
            $table->string('delay')->nullable();
            $table->text('options')->nullable(); //encrypted data, not json
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduler');
    }
}

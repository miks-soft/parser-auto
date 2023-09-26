<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parser_processes', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->string('status');
            $table->bigInteger('log_id')
                ->unsigned()
                ->nullable()
                ->default(null);
            $table->foreign('log_id')
                ->on('parser_logs')
                ->references('id')
                ->cascadeOnDelete();
            $table->bigInteger('cars_count')->nullable()->default(null);
            $table->timestamp('finished_at')->nullable()->default(null);
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
        Schema::dropIfExists('parser_processes');
    }
};

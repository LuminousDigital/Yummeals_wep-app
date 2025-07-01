<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskLogsTable extends Migration
{
    public function up()
    {
        Schema::create('task_logs', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->enum('status', ['success', 'failure']);
            $table->text('message')->nullable();
            $table->timestamp('run_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_logs');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlRedirectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('old_url', 2048)->unique();
            $table->string('new_url', 2048)->nullable();
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->unsignedBigInteger('hit_count')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('source', 32)->default('manual');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('url_redirects');
    }
}

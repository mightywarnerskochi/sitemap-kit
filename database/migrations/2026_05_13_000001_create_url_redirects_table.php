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
            // Full path can be long; unique index on utf8mb4 VARCHAR(2048) exceeds MySQL key limits.
            // Uniqueness is enforced on old_url_hash (sha256 hex); old_url is the human-readable value.
            $table->text('old_url');
            $table->string('old_url_hash', 64)->unique();
            $table->text('new_url')->nullable();
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

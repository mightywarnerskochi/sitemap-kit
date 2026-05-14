<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MySQL/InnoDB: a UNIQUE index on a long utf8mb4 VARCHAR (e.g. old_url 2048) exceeds the max key length (~3072 bytes).
 * We store the full path in old_url (TEXT, not unique) and enforce uniqueness on old_url_hash (64-char SHA-256 hex).
 */
class CreateUrlRedirectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Single migration for `url_redirects` (no separate alter migrations).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_redirects', function (Blueprint $table) {
            $table->id();
            $table->text('old_url');
            $table->char('old_url_hash', 64)->unique();
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

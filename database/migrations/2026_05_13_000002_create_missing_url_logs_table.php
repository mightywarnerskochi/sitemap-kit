<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissingUrlLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missing_url_logs', function (Blueprint $table) {
            $table->id();
            // Same pattern as url_redirects: avoid MySQL utf8mb4 unique on long VARCHAR.
            $table->string('url_hash', 64)->unique();
            $table->text('url');
            $table->text('referer')->nullable();
            $table->unsignedBigInteger('hit_count')->default(1);
            $table->timestamp('first_seen_at')->useCurrent();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->timestamps();

            $table->index('last_seen_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('missing_url_logs');
    }
}

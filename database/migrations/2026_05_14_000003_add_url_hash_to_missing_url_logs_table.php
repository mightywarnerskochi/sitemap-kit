<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUrlHashToMissingUrlLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('missing_url_logs')) {
            return;
        }

        if (Schema::hasColumn('missing_url_logs', 'url_hash')) {
            return;
        }

        Schema::table('missing_url_logs', function (Blueprint $table) {
            $table->string('url_hash', 64)->nullable()->after('id');
        });

        foreach (DB::table('missing_url_logs')->select('id', 'url')->cursor() as $row) {
            DB::table('missing_url_logs')->where('id', $row->id)->update([
                'url_hash' => hash('sha256', (string) $row->url),
            ]);
        }

        Schema::table('missing_url_logs', function (Blueprint $table) {
            $table->dropUnique(['url']);
        });

        Schema::table('missing_url_logs', function (Blueprint $table) {
            $table->unique('url_hash');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('missing_url_logs') || ! Schema::hasColumn('missing_url_logs', 'url_hash')) {
            return;
        }

        Schema::table('missing_url_logs', function (Blueprint $table) {
            $table->dropUnique(['url_hash']);
        });

        Schema::table('missing_url_logs', function (Blueprint $table) {
            $table->unique('url');
        });

        Schema::table('missing_url_logs', function (Blueprint $table) {
            $table->dropColumn('url_hash');
        });
    }
}

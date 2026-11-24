<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('catatan', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->boolean('is_archived')->default(false)->after('user_id');
            $table->string('lampiran')->nullable()->after('isi');
        });

        $userIds = DB::table('users')->orderBy('id')->limit(2)->pluck('id');

        if ($userIds->count() === 1) {
            DB::table('catatan')
                ->whereNull('user_id')
                ->update(['user_id' => $userIds->first()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catatan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['is_archived', 'lampiran']);
        });
    }
};

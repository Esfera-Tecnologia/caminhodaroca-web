<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->foreignId('partner_category_id')
                ->nullable()
                ->after('user_id')
                ->constrained('partner_categories')
                ->nullOnDelete();
            $table->string('routes', 1000)->nullable()->change();
            $table->string('circuits', 1000)->nullable()->change();
            $table->string('attractions', 1000)->nullable()->change();
        });

        Schema::table('preapproved_partners', function (Blueprint $table) {
            $table->foreignId('partner_category_id')
                ->nullable()
                ->after('user_id')
                ->constrained('partner_categories')
                ->nullOnDelete();
            $table->string('routes', 1000)->nullable()->change();
            $table->string('circuits', 1000)->nullable()->change();
            $table->string('attractions', 1000)->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('partners')->whereNull('routes')->update(['routes' => '']);
        DB::table('partners')->whereNull('circuits')->update(['circuits' => '']);
        DB::table('partners')->whereNull('attractions')->update(['attractions' => '']);
        DB::table('preapproved_partners')->whereNull('routes')->update(['routes' => '']);
        DB::table('preapproved_partners')->whereNull('circuits')->update(['circuits' => '']);
        DB::table('preapproved_partners')->whereNull('attractions')->update(['attractions' => '']);

        Schema::table('partners', function (Blueprint $table) {
            $table->dropConstrainedForeignId('partner_category_id');
            $table->string('routes', 1000)->nullable(false)->change();
            $table->string('circuits', 1000)->nullable(false)->change();
            $table->string('attractions', 1000)->nullable(false)->change();
        });

        Schema::table('preapproved_partners', function (Blueprint $table) {
            $table->dropConstrainedForeignId('partner_category_id');
            $table->string('routes', 1000)->nullable(false)->change();
            $table->string('circuits', 1000)->nullable(false)->change();
            $table->string('attractions', 1000)->nullable(false)->change();
        });
    }
};

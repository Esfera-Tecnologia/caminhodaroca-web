<?php

use App\Models\Property;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            //Para os novos registros, considera-se que ainda não passaram pela primeira aprovação.
            $table->boolean('approved')->default(0);
        });
        //Para os registros já existentes, considera-se que já passaram pela primeira aprovação
        Property::where('approved', 0)->update(['approved' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['approved']);
        });
    }
};

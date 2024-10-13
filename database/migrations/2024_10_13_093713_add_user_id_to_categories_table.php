<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_add_user_id_to_categories_table.php

public function up(): void
{
    Schema::table('categories', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    });
}

public function down(): void
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}

};

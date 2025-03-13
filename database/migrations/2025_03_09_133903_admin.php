<?php

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
        DB::statement("CREATE TABLE admin (
            role_id BIGINT CHECK (role_id = 2)
        ) INHERITS (users);");
        DB::statement("ALTER TABLE admin ADD PRIMARY KEY (id)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

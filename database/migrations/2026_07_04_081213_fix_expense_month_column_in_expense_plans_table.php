<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE expense_plans MODIFY expense_month DATE NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE expense_plans MODIFY expense_month VARCHAR(20) NULL');
    }
};

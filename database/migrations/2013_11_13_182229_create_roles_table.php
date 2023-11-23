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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $this->run(); // Call the run method after creating the table
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }

    /**
     * Seed the roles table.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'superadmin'],
            ['name' => 'admin'],
            ['name' => 'user'],
        ]);
    }
};

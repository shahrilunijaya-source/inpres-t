<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizens', function (Blueprint $table) {
            $table->id();
            $table->string('ic', 14)->unique();
            $table->string('full_name', 120);
            $table->date('dob');
            $table->enum('gender', ['M', 'F']);
            $table->string('address', 200);
            $table->string('postcode', 5);
            $table->string('state', 40);
            $table->timestamps();

            $table->index('full_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};

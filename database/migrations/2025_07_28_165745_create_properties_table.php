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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->enum('type',['rent','sale']);
             $table->decimal('price','8','2');
              $table->string('location');
               $table->unsignedBigInteger('region_id');
                  $table->enum('status', ['available', 'pending', 'sold'])->default('pending');
                    $table->string('featured_image')->nullable();
                    $table->softDeletes();
            $table->timestamps();
             $table->foreign('region_id')->references('id')->on('regions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};

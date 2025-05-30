<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class CreateCommitsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Capsule::schema()->hasTable('commits')) {
            Capsule::schema()->create('commits', function (Blueprint $table) {
                $table->id();
                $table->string('hash', 40)->unique(); // SHA-1 hash is 40 characters
                $table->string('author');
                $table->timestamp('date');
                $table->string('repository_owner');
                $table->string('repository_name');
                $table->string('platform', 20)->default('github');
                $table->text('message')->nullable();
                $table->timestamps();

                // Indexes for better query performance
                $table->index(['repository_owner', 'repository_name']);
                $table->index('date');
                $table->index('author');
                $table->index(['repository_owner', 'repository_name', 'platform'], 'commits_repo_platform_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Capsule::schema()->dropIfExists('commits');
    }
} 
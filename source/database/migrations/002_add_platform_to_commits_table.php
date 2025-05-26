<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class AddPlatformToCommitsTable extends Migration
{
    public function up(): void
    {
        Capsule::schema()->table('commits', function (Blueprint $table) {
            $table->string('platform', 20)->default('github')->after('repository_name');
            $table->index(['repository_owner', 'repository_name', 'platform'], 'commits_repo_platform_index');
        });
    }

    public function down(): void
    {
        Capsule::schema()->table('commits', function (Blueprint $table) {
            $table->dropIndex('commits_repo_platform_index');
            $table->dropColumn('platform');
        });
    }
} 
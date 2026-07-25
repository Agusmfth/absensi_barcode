<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { if (!Schema::hasColumn('school_settings','landing_background')) Schema::table('school_settings', fn(Blueprint $table) => $table->string('landing_background')->nullable()->after('school_logo')); }
    public function down(): void { if (Schema::hasColumn('school_settings','landing_background')) Schema::table('school_settings', fn(Blueprint $table) => $table->dropColumn('landing_background')); }
};

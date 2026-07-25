<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $usernameWasMissing = ! Schema::hasColumn('users', 'username');
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'kepala_sekolah', 'wali_kelas'])->default('admin')->after('password');
            }
            if (! Schema::hasColumn('users', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('class_id');
            }
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
        });

        DB::table('users')->whereNull('username')->orderBy('id')->each(function ($user) {
            $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', strstr($user->email, '@', true) ?: 'user'));
            DB::table('users')->where('id', $user->id)->update(['username' => ($base ?: 'user').$user->id]);
        });

        if ($usernameWasMissing && ! $this->hasUsernameUniqueIndex()) {
            Schema::table('users', fn (Blueprint $table) => $table->unique('username'));
        }
    }

    private function hasUsernameUniqueIndex(): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            return false;
        }

        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'users')
            ->where('column_name', 'username')
            ->where('non_unique', 0)
            ->exists();
    }

    public function down(): void
    {
        // Migrasi kompatibilitas tidak menghapus kolom agar data pengguna tetap aman.
    }
};

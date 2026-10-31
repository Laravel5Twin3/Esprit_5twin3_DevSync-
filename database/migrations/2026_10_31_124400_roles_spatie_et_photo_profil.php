<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Passage des rôles à Spatie Permission :
 *  - crée les permissions et les rôles admin / citoyen ;
 *  - reprend le rôle de chaque utilisateur existant (ancienne colonne users.role) puis supprime la colonne ;
 *  - ajoute la photo de profil.
 */
return new class extends Migration
{
    private const PERMISSIONS_ADMIN = [
        'acceder back office',
        'gerer utilisateurs',
        'gerer alertes meteo',
        'gerer coupures',
        'gerer points fraicheur',
        'gerer conseils',
        'gerer entraide',
    ];

    private const PERMISSIONS_CITOYEN = [
        'signaler coupure',
        'publier entraide',
    ];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('phone');
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ([...self::PERMISSIONS_ADMIN, ...self::PERMISSIONS_CITOYEN] as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions([...self::PERMISSIONS_ADMIN, ...self::PERMISSIONS_CITOYEN]);

        $citoyen = Role::findOrCreate('citoyen', 'web');
        $citoyen->syncPermissions(self::PERMISSIONS_CITOYEN);

        // Les utilisateurs déjà en base gardent leur rôle
        DB::table('users')->select('id', 'role')->orderBy('id')->each(function ($user) use ($admin, $citoyen) {
            DB::table('model_has_roles')->insert([
                'role_id' => $user->role === 'admin' ? $admin->id : $citoyen->id,
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('citoyen')->after('password');
        });

        $adminIds = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', 'admin')
            ->where('model_has_roles.model_type', User::class)
            ->pluck('model_has_roles.model_id');
        DB::table('users')->whereIn('id', $adminIds)->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};

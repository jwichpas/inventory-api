<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        Permission::create(['name' => 'ver empresas']);
        Permission::create(['name' => 'crear usuarios']);
        Permission::create(['name' => 'editar empresa']);
        Permission::create(['name' => 'eliminar empresa']);

        // Crear roles
        $admin = Role::create(['name' => 'admin']);
        $usuario = Role::create(['name' => 'usuario']);

        // Asignar permisos a roles
        $admin->givePermissionTo(Permission::all());

        $usuario->givePermissionTo(['ver empresas']);
    }
}

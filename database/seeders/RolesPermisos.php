<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermisos extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {

    $permisos = [
      'consultar-productos',
      'agregar-productos',
      'editar-productos',
      'eliminar-productos',
      'consultar-usuarios',
      'agregar-usuarios',
      'editar-usuarios',
      'eliminar-usuarios'
    ];

    foreach ($permisos as $permiso) {
      Permission::create(['name' => $permiso]);
    }

    $root = Role::updateOrCreate(['name' => 'root']);
    $root->givePermissionTo('consultar-productos',
      'agregar-productos',
      'editar-productos',
      'eliminar-productos',
      'consultar-usuarios',
      'agregar-usuarios',
      'editar-usuarios',
      'eliminar-usuarios');

    $reader = Role::updateOrCreate(['name' => 'reader']);
    $reader->givePermissionTo('consultar-productos', 'consultar-usuarios');

    $newUser = Role::updateOrCreate(['name' => 'new']);
  }
}

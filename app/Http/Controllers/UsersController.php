<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
  public function index()
  {
    $headers = ['ID', 'Nombre', 'Email', 'Estatus'];
    $users = User::select('id', 'name', 'email', 'active')
      ->get()
      ->toArray();
    $user = Auth::user();
    $permisos = $user->getAllPermissions()->pluck('name');
    $permisoEditar = 'editar-usuarios';
    $permisoEliminar = 'eliminar-usuarios';

    return view('admin.users.index', compact('users', 'headers', 'permisos', 'permisoEditar', 'permisoEliminar'));
  }

  public function store(Request $request)
  {
    $id = $request->id;


    if ($id) {
      $request->validate([
        'username' => 'required|string|max:255|unique:users,username,' . $id,
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
      ]);

      $user = User::find($id);

      // Verificar si se encontró al usuario
      if (!$user) {
        return response()->json([
          'status' => 'error',
          'title' => 'Error',
          'message' => 'Usuario no encontrado',
        ]);
      }

      // Actualizar los campos del usuario con los nuevos valores
      $user->update([
        'name' => $request->input('name'),
        'username' => $request->input('username'),
        'email' => $request->input('email'),
        'active' => $request->input('active'),
      ]);

      // Actualizar el rol del usuario
      $rol_id = $request->input('rol');
      $rol = Role::find($rol_id);
      $user->syncRoles([]);
      $user->assignRole($rol);

      return response()->json([
        'status' => 'success',
        'title' => 'Éxito',
        'message' => 'Usuario editado con éxito',
      ]);
    } else {
      $request->validate([
        'username' => 'required|string|max:255|unique:users,username',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
      ]);

      $ContrasenaTemp = Str::random(8);

      // Crear un nuevo usuario en la base de datos
      $user = User::create([
        'name' => $request->input('name'),
        'username' => $request->input('username'),
        'email' => $request->input('email'),
        'password' => Hash::make($ContrasenaTemp),
      ]);

      Notification::send($user, new VerifyEmail);

      $rol_id = $request->input('rol');

      $rol = Role::find($rol_id);
      $user->syncRoles([]);
      $user->assignRole($rol);

      // Devolver una respuesta JSON
      return response()->json([
        'status' => 'success',
        'title' => 'Éxito',
        'message' => 'Usuario creado exitosamente. Contraseña Temporal: ' . $ContrasenaTemp,
      ]);
    }
  }

  public function create()
  {
    $roles = Role::all();
    return view('admin.users.create', compact('roles'));
  }

  public function edit($id)
  {
    $roles = Role::all();
    $user = User::where('id', $id)->first();
    $userRol = $user->roles->pluck('id')->first();
    if (!$user) {
      return redirect()->route('admin.users.index')->with('error', 'Usuario no encontrado.');
    }

    return view('admin.users.edit', compact('user', 'roles', 'userRol'));
  }

  public function destroy($id)
  {
    // Obtener el usuario por su ID (si está activo)
    $user = User::where('id', $id)->first();

    // Si el usuario no existe o no está activo, redireccionar a la lista de usuarios con un mensaje de error
    if (!$user) {
      return response()->json([
        'status' => 'error',
        'title' => 'Error',
        'message' => 'Usuario no encontrado.',
      ]);
    }

    // Cambiar el estado del usuario a inactivo
    $user->active = $user->active ? false : true;
    $user->save();
    $message = $user->active ? 'Usuario habilitado exitosamente.' : 'Usuario deshabilitado exitosamente.';

    return response()->json([
      'status' => 'success',
      'title' => 'Éxito',
      'message' => $message,
    ]);
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductosController extends Controller
{
  public function index()
  {
    $productos = Producto::select('id', 'nombre', 'descripcion', 'codigo', 'imagen as image', 'precio', 'activo as active')->get();
    $headers = ['ID', 'Nombre', 'Descripcion', 'Código', 'Imagen', 'Precio', 'Activo'];
    $user = Auth::user();
    $permisos = $user->getAllPermissions()->pluck('name');
    $permisoEditar = 'editar-productos';
    $permisoEliminar = 'eliminar-productos';

    return view('admin.productos.index', compact('productos', 'headers', 'permisos', 'permisoEditar', 'permisoEliminar'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'nombre' => 'required|unique:productos,nombre' . ($request->id ? ',' . $request->id : ''),
      'codigo' => 'required',
      'imagen' => 'image|mimes:jpeg,png,jpg|max:2048',
      'precio' => 'required|numeric',
    ]);

    try {
      if ($request->id) {
        $producto = Producto::find($request->id);

        if (!$producto) {
          return response()->json([
            'status' => 'error',
            'title' => 'Error',
            'message' => 'El producto no existe.',
          ], 500);
        }

        $imagen = $request->file('imagen');
        $base64img = $imagen ? base64_encode(\Illuminate\Support\Facades\File::get($imagen->getPathname())) : '';

        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->codigo = $request->codigo;
        if ($base64img) {
          $producto->imagen = $base64img;
        }
        $producto->precio = $request->precio;
        $producto->save();

        $responseMessage = 'Producto actualizado correctamente.';
      } else {

        $imagen = $request->file('imagen');
        $base64img = $imagen ? base64_encode(\Illuminate\Support\Facades\File::get($imagen->getPathname())) : '';

        $producto = Producto::create([
          'nombre' => $request->nombre,
          'descripcion' => $request->descripcion,
          'codigo' => $request->codigo,
          'imagen' => $base64img,
          'precio' => $request->precio,
        ]);

        $responseMessage = 'Producto creado correctamente.';
      }

      return response()->json([
        'status' => 'success',
        'title' => 'Éxito',
        'message' => $responseMessage,
      ]);
    } catch (\Throwable $th) {
      Log::error("ProductosController@store. Error: {$th->getMessage()}");
      return response()->json([
        'status' => 'error',
        'title' => 'Error',
        'message' => 'Ocurrió un error al guardar el producto.',
      ], 500);
    }
  }

  public function create()
  {
    return view('admin.productos.create');
  }

  public function edit($id)
  {
    $producto = Producto::find($id);
    return view('admin.productos.edit', compact('producto'));
  }

  public function destroy($id)
  {
    try {
      $producto = Producto::find($id);

      if (!$producto) {
        return response()->json([
          'status' => 'error',
          'title' => 'Error',
          'message' => 'El producto no existe.',
        ], 500);
      }

      $producto->delete();

      return response()->json([
        'status' => 'success',
        'title' => 'Éxito',
        'message' => 'Producto eliminado correctamente.',
      ]);
    } catch (\Throwable $th) {
      Log::error("ProductosController@destroy. Error: {$th->getMessage()}");
      return response()->json([
        'status' => 'error',
        'title' => 'Error',
        'message' => 'Ocurrió un error al eliminar el producto.',
      ], 500);
    }
  }
}

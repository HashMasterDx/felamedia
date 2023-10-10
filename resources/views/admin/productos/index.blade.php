@extends('adminlte::page')

@section('title', 'ProductosController')

@section('content_header')
  <h1>Productos</h1>
  <div class="row">
    <div class="col-12 text-right">
      @can('agregar-productos')
        <a class="btn btn-primary" href="{{ route('admin.productos.create') }}">
          <i class="far fa-fw fa-file-alt"></i>
          Nuevo
        </a>
      @endcan
    </div>
  </div>
@stop

@section('content')

  <div class="row">
    <div class="col-12">
      <!-- Componente DT Generic -->
      <generic-data-table
        :headers="{{ json_encode($headers) }}"
        :data="{{ json_encode($productos) }}"
        :edit-route="{{ json_encode(route('admin.productos.edit')) }}"
        :delete-route="{{ json_encode(route('admin.productos.deactivate')) }}"
        :permisos_user="{{json_encode($permisos)}}"
        :permiso_editar="{{json_encode($permisoEditar)}}"
        :permiso_eliminar="{{json_encode($permisoEliminar)}}">
      </generic-data-table>
    </div>
  </div>
@stop

@section('adminlte_js')

@stop

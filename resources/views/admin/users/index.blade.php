@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
  <h1>Usuarios</h1>
  <div class="row">
    <div class="col-12 text-right">
      @can('agregar-usuarios')
        <a class="btn btn-primary" href="{{ route('admin.user.create') }}">
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
        :data="{{ json_encode($users) }}"
        :edit-route="{{ json_encode(route('admin.user.edit')) }}"
        :delete-route="{{ json_encode(route('admin.user.deactivate')) }}"
        :habilitar=true
        :permisos_user="{{json_encode($permisos)}}"
        :permiso_editar="{{json_encode($permisoEditar)}}"
        :permiso_eliminar="{{json_encode($permisoEliminar)}}">
      </generic-data-table>
    </div>
  </div>
@stop

@section('adminlte_js')

@stop

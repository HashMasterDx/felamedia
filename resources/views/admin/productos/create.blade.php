@extends('adminlte::page')

@section('title', 'ProductosController')

@section('content_header')
  <h1>{{ __('Detalles del Producto') }}</h1>
  <div class="row">
    <div class="col-12 text-right">
      <a class="btn btn-primary" href="{{ route('admin.productos.list') }}"><i class="fas fa-list"></i>&nbsp;Listar</a>
    </div>
  </div>
@stop

@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12">
      <div class="card">
        <form id="form" action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="card-body card-primary card-outline">
            <div class="row">
              <div class="col-6">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control">
                <span class="text-danger error-text nombre_error"></span>
              </div>
              <div class="col-6">
                <label for="descripcion">Descripción</label>
                <textarea type="text" name="descripcion" id="descripcion" class="form-control"></textarea>
              </div>
              <div class="col-6">
                <label for="codigo">Código</label>
                <input type="text" name="codigo" id="codigo" class="form-control">
                <span class="text-danger error-text codigo_error"></span>
              </div>
              <div class="col-6">
                <label for="precio">Precio</label>
                <input type="text" name="precio" id="precio" class="form-control">
                <span class="text-danger error-text precio_error"></span>
              </div>
              <div class="col-6">
                <label for="imagen">Imagen</label>
                <input type="file" name="imagen" id="imagen" class="form-control" accept=".jpg, .jpeg, .png">
                <span class="text-danger error-text imagen_error"></span>
              </div>
            </div>
          </div>
          <div class="card-footer text-right">
            <button id="btn-save" class="btn btn-primary">
              <i class="far fa-file-alt"></i>&nbsp;Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

@stop

@section('adminlte_js')
  <script type="text/javascript">
    $(document).ready(function () {
      $('#btn-save').on('click', function (e) {
        limpiarErrores();
        e.preventDefault();
        ruta = "{{ route('admin.productos.store') }}";
        var form = document.querySelector('#form');
        var formData = new FormData(form);

        axios({
          method: 'post',
          url: ruta,
          data: formData,
          headers: {'Content-Type': 'multipart/form-data'}
        })
          .then(function (response) {
            if (response.data.status === 'success') {
              Swal.fire({
                icon: response.data.status,
                title: response.data.title,
                text: response.data.message,
                showConfirmButton: true,
              }).then(() => {
                window.location.href = '{{ route('admin.productos.list') }}';
              });
            } else {
              Swal.fire({
                icon: response.data.status,
                title: response.data.title,
                text: response.data.message,
                showConfirmButton: true,
              });
            }
          })
          .catch(function (error) {
            if (error.response) {
              console.log(error.response.data.errors);
              var errors = error.response.data.errors;
              for (var key in errors) {
                console.log('span' + key + '_error ' + errors[key][0]);
                document.querySelector("span." + key + "_error").textContent = errors[key][0];
                document.getElementById(key).classList.add("is-invalid");
              }
            }
          });
      })
    });
  </script>
@stop

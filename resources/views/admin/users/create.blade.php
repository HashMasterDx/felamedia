@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
  <h1>{{ __('Detalles de Usuario') }}</h1>
  <div class="row">
    <div class="col-12 text-right">
      <a class="btn btn-primary" href="{{ route('admin.user.list') }}"><i class="fas fa-list"></i>&nbsp;Listar</a>
    </div>
  </div>
@stop

@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12">
      <div class="card">
        <form id="form" action="{{ route('admin.user.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="card-body card-primary card-outline">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group d-flex flex-column align-items-center">
                  <img src="{{ asset('image/perfil.png') }}" class="img-fluid mb-4" alt=""
                       style="width: 250px">
                  <input id="photo" type="file" name="photo" class="form-control">
                </div>
              </div>
              <div class="col-md-9">
                <div class="row">
                  <div class="col-md-5">
                    <div class="row">
                      <div class="form-group col-6">
                        <label>Usuario</label>
                        <input id="username" name="username" type="text"
                               class="form-control validate">
                        <span class="text-danger error-text username_error"></span>
                      </div>
                      <div class="form-group col-6">
                        <label>Nombre</label>
                        <input id="name" name="name" type="text" class="form-control validate">
                        <span class="text-danger error-text name_error"></span>
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-12">
                        <label>Email</label>
                        <input id="email" name="email" type="email"
                               class="form-control validate">
                        <span class="text-danger error-text email_error"></span>
                      </div>

                    </div>
                  </div>
                  <div class="col-md-7">
                    <div class="alert bg-secondary col-12 form-group" role="alert">
                      Si desea actualizar su contraseña haga
                      <a id="change-password" type="button" class="btn-confcam">
                        click aqui.
                      </a>
                    </div>

                    <div class="form-group col-12">
                      <label>Asignar Rol</label>
                      <select id="rol" name="rol" class="form-control">
                        <option value="" disabled selected>Seleccionar rol</option>
                        @foreach ($roles as $rol)
                          <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
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
        ruta = "{{ route('admin.user.store') }}";
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
                window.location.href = '{{ route('admin.user.list') }}';
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

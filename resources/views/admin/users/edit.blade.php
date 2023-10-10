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
      <br>
      <div class="card">
        <form id="form" action="" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <div class="card card-primary card-outline">
                  <div class="card-body box profile">
                    <div class="form-group col-12">
                      <input type="hidden" name="id" id="id" value="{{ $user->id }}">
                      <label>Foto de perfil</label>
                      <br>
                      <img src="{{ asset('image/perfil.png') }}" class="img-fluid mb-4" alt=""
                           style="width: 250px">
                      <input id="photo" type="file" name="photo" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-9">
                <div class="card card-primary card-outline">
                  <div class="row card-body box">
                    <div class="col-md-5">
                      <div class="row">
                        <div class="form-group col-6">
                          <label>Usuario</label>
                          <input id="username" name="username" type="text" class="form-control"
                                 value="{{ $user->username }}">
                          <span class="text-danger error-text username_error"></span>
                        </div>
                        <div class="form-group col-6">
                          <label>Nombre</label>
                          <input id="name" name="name" type="text" class="form-control"
                                 value="{{ $user->name }}">
                          <span class="text-danger error-text name_error"></span>
                        </div>
                      </div>

                      <div class="row">
                        <div class="form-group col-12">
                          <label>Email</label>
                          <input id="email" name="email" type="email" class="form-control"
                                 value="{{ $user->email }}">
                          <span class="text-danger error-text email_error"></span>
                        </div>
                      </div>
                      <div class="form-group col-4">
                        <label>Estado</label>
                        @foreach (['Inactivo','Activo' ] as $index => $value)
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="active" id="active{{ $index }}"
                                   value="{{ $index }}"
                              {{ isset($user) && $user->active == $index ? 'checked' : '' }}>
                            <label class="form-check-label" for="active{{ $index }}">{{ $value }}</label>
                          </div>
                        @endforeach
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
                          <option value="">Seleccione un rol</option>
                          @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" {{ $rol->id == $userRol ? 'selected' : '' }}>
                              {{ $rol->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <br>
                      <br>

                      <br>
                      <a id="btn-actualizar" class="btn btn-primary float-right">
                        <i class="far fa-file-alt"></i>&nbsp;Guardar
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <input type="hidden" name="id" id="id" value="{{ $user->id }}">
@stop

@section('adminlte_js')
  <script type="text/javascript">
    $(document).ready(function () {
      $('#btn-actualizar').on('click', function (e) {
        e.preventDefault();
        limpiarErrores();
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

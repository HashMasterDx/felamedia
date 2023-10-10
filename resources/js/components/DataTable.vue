<template>
  <div class="row pt-3">
    <div class="col-lg-12 col-md-12 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <DataTable :options="tableOptions" class="table table-striped table-bordered display">
              <thead>
              <tr>
                <th v-for="header in headers">{{ header }}</th>
                <th v-if="tienePermisoEditar || tienePermisoEliminar">Acciones</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="row in data">
                <input id="hiddenInput" :value="row" type="hidden">
                <td v-for="(value, key) in row">
                  <span v-html="renderCellContent(value, key)"></span>
                </td>
                <td v-if="tienePermisoEditar || tienePermisoEliminar">
                  <div class="dropdown">
                    <button aria-expanded="false" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                            type="button">
                      Acciones
                    </button>
                    <ul class="dropdown-menu">
                      <li v-if="tienePermisoEditar">
                        <a :data-edit="row" :data-id="row.id" :data-target="editRoute === '' ? '#genericModal' : null" :data-toggle="editRoute === '' ? 'modal' : null"
                           :href="editRoute === '' ? null : getEditRoute(row.id)"
                           class="dropdown-item"
                           name="btn-Editar"
                           @click="asignarID(row)">
                          <i class="fas fa-pencil-alt text-warning"></i>
                          Editar
                        </a>
                      </li>
                      <li v-if="tienePermisoEliminar"><a v-if="habilitar"
                                                         :class="customDeleteButtonText(row).buttonClass"
                                                         :href="getDeleteRoute(row.id)"
                                                         @click="changeStatus($event, row.id)">
                        <i :class="customDeleteButtonText(row).iconClass"></i>
                        {{ customDeleteButtonText(row).text }}
                      </a>
                        <a v-else :href="getDeleteRoute(row.id)" class="dropdown-item bg-danger"
                           @click="changeStatus($event, row.id)">
                          <i class="fas fa-trash-alt"></i>
                          Eliminar
                        </a>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
              </tbody>
            </DataTable>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DataTable from 'datatables.net-vue3';
import DataTableLib from 'datatables.net-bs5';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5';
import pdfmake from 'pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
import 'datatables.net-responsive-bs5';
import JsZip from 'jszip';

pdfmake.vfs = pdfFonts.pdfMake.vfs;

window.JSZip = JsZip;

DataTable.use(DataTableLib);
DataTable.use(pdfmake);
DataTable.use(ButtonsHtml5);
export default {
  mounted() {

  },
  name: 'generic-data-table',
  data() {
    return {
      usuarioTienePermiso: true,
      tableOptions: {
        language: {
          search: "Buscar:",
          searchPlaceholder: "Buscar en la tabla",
          lengthMenu: "Mostrar _MENU_ registros por página",
          zeroRecords: "No se encontraron registros coincidentes",
          info: "Mostrando _START_ al _END_ de _TOTAL_ registros",
          infoEmpty: "Mostrando 0 al 0 de 0 registros",
          infoFiltered: "(filtrados de _MAX_ registros en total)",
          paginate: {
            first: "Primero",
            previous: "Anterior",
            next: "Siguiente",
            last: "Último",
          },
        },
        columnDefs: this.generateColumnDefs(),
        order: [[this.tableOrder.columna, this.tableOrder.order]],
      },
      editID: null
    };
  },
  components: {
    DataTable,
  },
  props: {
    editrow: {
      type: String,
      required: false,
    },
    permiso_editar: {
      type: String,
      required: false,

    },
    permiso_eliminar: {
      type: String,
      required: false,

    },
    permisos_user: {
      type: Array,
      required: false,
    },
    editdata: {
      type: Array,
      required: false,
    },
    data: {
      type: Array,
      required: false,
    },
    headers: {
      type: Array,
      required: true,
    },
    hiddenColumns: {
      type: Array,
      default: () => [],
    },
    editRoute: {
      type: String,
      required: false
    },
    deleteRoute: {
      type: String,
      required: false
    },
    showButtons: {
      type: Boolean,
      default: false,
    },
    tableOrder: {
      type: Object,
      default: () => ({
        columna: 0,
        order: 'asc'
      }),
    },
    textoEliminarBtn: {
      type: String,
      default: 'Eliminar'
    },
    habilitar: {
      type: Boolean,
      default: false,
    }
  },
  computed: {
    tienePermisoEditar({permiso_editar}) {
      if (permiso_editar) {
        console.log(this.permisos_user)
        var m = this.permisos_user.includes(permiso_editar);
        console.log(m)
        return m;
      } else {
        return true;
      }
    },
    tienePermisoEliminar({permiso_eliminar}) {
      if (permiso_eliminar) {
        return this.permisos_user.includes(permiso_eliminar);
      } else {
        return true;
      }
    },
  },
  methods: {
    asignarID(row) {
      for (const key in row) {
        if (row.hasOwnProperty(key)) {
          const element = document.getElementById(key);

          if (element) {
            if (element.tagName === 'SELECT') {

              const options = element.querySelectorAll('option');
              for (const option of options) {
                if (option.textContent === row[key]) {
                  option.selected = true;
                  break; // Salir del bucle una vez que se encuentre la opción
                } else if (option.value == row[key]) {
                  option.selected = true;
                  break;
                }
              }
            } else {
              element.value = row[key];
            }
          }
        }
      }

    },
    verificarEditID() {
      const element = document.getElementById('id');
      if (element.value !== '') {

        return true;
      } else {
        // El campo editid está vacío
        return false;
      }
    },
    showEditModal(row) {
      this.editdata = {...row}; // Copia los datos del elemento seleccionado
    },

    changeStatus(event, id) {
      event.preventDefault();
      axios.get(this.deleteRoute + "/" + id).then(response => {
        console.log(response.data)
        Swal.fire({
          icon: response.data.status,
          title: response.data.title,
          text: response.data.message,
        }).then((result) => {
          if (result.value) {
            window.location.reload();
          }
        })
      }).catch(error => {
        console.log(error.response);
      });
    },
    editData(event, id) {
      this.editID = id;
      return this.editRoute + '/' + id;
    },
    renderCellContent(value, key) {
      if (key === 'active') {
        return this.renderIcon(value);
      }

      if (key === 'id') {
        return `<a href="#">${value}</a>`;
      }

      if (key === 'image') {
        return `<img src="data:image/png;base64,${value}" alt="Imagen" width="100" height="100">`;
      }

      return value;
    },
    renderIcon(value) {
      return value ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>';
    },
    getEditRoute(id) {
      this.editID = id;
      return this.editRoute + '/' + id;
    },
    getDeleteRoute(id) {
      return this.deleteRoute + '/' + id;
    },
    generateColumnDefs() {
      return this.headers.map((header, index) => {
        return {
          targets: [index],
          visible: !this.hiddenColumns.includes(header)
        };
      });
    },
    customDeleteButtonText(row) {
      if (row.active === 1) {
        return {
          text: 'Deshabilitar', // Cambia a "Habilitar" si lo deseas
          iconClass: 'fas fa-times-circle', // Cambia el icono si es necesario
          buttonClass: 'dropdown-item bg-danger', // Cambia la clase CSS del botón si es necesario
        };
      } else {
        return {
          text: 'Habilitar', // Cambia a "Deshabilitar" si lo deseas
          iconClass: 'fas fa-check-circle', // Cambia el icono si es necesario
          buttonClass: 'dropdown-item bg-success', // Cambia la clase CSS del botón si es necesario
        };
      }
    },
  },
  beforeMount() {
    if (this.showButtons) {
      this.tableOptions.dom = `
        <'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f><'col-sm-12 col-md-12'B>>
        <'row'<'col-sm-12'tr>>
        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`;
      this.tableOptions.buttons = [
        {
          extend: "excel",
          text: "<i class='fas fa-file-excel text-success'></i> &nbsp; Exportar a Excel",
          className: "btn btn-primary",
        },
        {
          extend: "pdf",
          text: "<i class='fas fa-file-pdf text-danger'></i> &nbsp; Exportar a PDF",
          className: "btn btn-primary",
        },
        {
          extend: "csv",
          text: "<i class='fas fa-file-csv text-primary'></i> &nbsp; Exportar a CSV",
          className: "btn btn-primary",
        }
      ]
    }
  },
  created() {
    //
  },
};
</script>

<style>
@import 'datatables.net-bs5';
</style>

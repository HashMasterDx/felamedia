<template>
  <div :id="id" aria-hidden="true" class="modal fade" style="display: none;">
    <div :class="['modal-dialog', size]">
      <form :action="form.route" :method="form.method" enctype="multipart/form-data">
        <input :value="csrfToken" name="_token" type="hidden">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h4 class="modal-title">
              {{ title }}
            </h4>
            <button aria-label="Close" class="close" data-dismiss="modal" type="button" @click="closeForm">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <slot></slot>
          </div>
          <div class="modal-footer justify-content-between">
            <button class="btn btn-default bg-secondary" data-dismiss="modal" type="button" @click="closeForm">Cerrar
            </button>
            <button class="btn btn-primary" type="button" @click="submitForm"><i
              class="fas fa-save"></i>&nbsp;Guardar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>

// Funcion para buscar un input por el name y agregar un span con el error

export default {
  mounted() {

  },
  name: 'generic-modal',
  data() {
    return {
      errorMessage: {}
    };
  },
  props: {
    editdataM: {
      type: Object,
      default: () => ({}),
    },
    title: {
      type: String,
      default: 'Generic Modal'
    },
    id: {
      type: String,
      default: 'genericModal'
    },
    size: {
      type: String,
      default: ' '
    },
    form: {
      type: Object,
      required: true,
      default: () => ({'route': '', 'method': 'POST', 'redirect': ''})
    },
  },
  methods: {
    toggleM() {

    },
    addError(name, errors) {
      let input = document.querySelector(`input[name="${name}"]`);
      input.classList.add('is-invalid');
      let span = document.createElement('span');
      span.classList.add('error');
      span.classList.add('invalid-feedback');
      span.innerHTML = errors[name][0];
      input.parentNode.insertBefore(span, input.nextSibling);
    },
    submitForm() {
      axios.post(this.form.route, new FormData(this.$el.querySelector('form')), {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).then(response => {
        this.errorMessage = response.data.errors;
        this.$emit('success', response.data);

        if (this.errorMessage) {
          Object.keys(this.errorMessage).map(key => {
            this.addError(key, this.errorMessage);
          });
        } else {
          Swal.fire({
            icon: response.data.type,
            title: response.data.title,
            text: response.data.message,
          }).then((result) => {
            if (result.value && this.form.redirect) {
              window.location.href = this.form.redirect + '/' + response.data.id;
            } else {
              window.location.reload();
            }
          });
        }
      }).catch(error => {
        let errorTitle = 'Error';
        let errorMessage = 'Hubo un error al procesar la solicitud.';

        if (error.response && error.response.data) {
          errorTitle = error.response.data.title;
          errorMessage = error.response.data.message;
        }

        Swal.fire({
          icon: 'error',
          title: errorTitle,
          text: errorMessage,
        }).then((result) => {
          if (result.value && this.form.redirect) {
            window.location.href = this.form.redirect;
          } else {
            window.location.reload();
          }
        });


      });
    },

    clearFormFields() {
      const formFields = this.$el.querySelectorAll('input, textarea, select');
      formFields.forEach(field => {
        if (field.type === 'checkbox' || field.type === 'radio') {
          field.checked = false;
        } else {
          field.value = '';
        }
      });
      this.errorMessage = '';
    },
    closeForm() {
      this.clearFormFields();
    },

  },
  computed: {
    csrfToken() {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    },
  },

};
</script>

<style></style>

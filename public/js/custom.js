function limpiarErrores() {
  var errorSpans = document.querySelectorAll('.error-text');
  errorSpans.forEach(function (span) {
    span.textContent = '';
  });
  var invalidInputs = document.querySelectorAll('.is-invalid');
  invalidInputs.forEach(function (input) {
    input.classList.remove('is-invalid');
  });
}

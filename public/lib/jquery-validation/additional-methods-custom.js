$.validator.addMethod("lettersonlyc", function (value, element) {
    return this.optional(element) || /^[ a-záéíóúñ]+$/i.test(value);
}, "Por favor escriba solo letras y espacios.");

$.validator.addMethod("numbersonlyc", function (value, element) {
    return this.optional(element) || /^[0-9]+$/i.test(value);
}, "Por favor escriba solo números.");

$.validator.addMethod("alphanumericc", function (value, element) {
  return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
}, "Por favor escriba solo letras sin acento y números.");

$.validator.addMethod("alphanumeric2c", function (value, element) {
  return this.optional(element) || /^[a-zA-Z0-9 ]+$/.test(value);
}, "Por favor escriba solo letras sin acento, números y espacios.");

$.validator.addMethod("alphanumeric3c", function (value, element) {
  return this.optional(element) || /^[a-zA-Z0-9_]+$/.test(value);
}, "Por favor escriba solo letras sin acento, números y guiones bajos.");

$.validator.addMethod('filesizec', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param * 1000000)
}, 'El tamaño del archivo debe ser menor a {0} mb');

$.validator.addMethod("notequaltoc", function(value, element, param) {
    return this.optional(element) || value !== $(param).val();
}, "Los valores deben ser diferentes");

$.validator.addMethod("emailc", function (value, element) {
  return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value);
}, "Por favor ingrese un correo válido.");
/** Muestra una alerta con SweetAlert2 */
function mainAlert(icon = "success", title = "Operación exitosa", html = "", confirmBtn = false, timer = 1500, callback = null) {
  Swal.fire({
    icon: icon,
    title: title,
    html: html,
    showConfirmButton: confirmBtn,
    allowOutsideClick: false,
    timer: timer,
  }).then(() => {
    if (typeof callback === "function") {
      callback();
    }
  });
}

/** Crea un enlace temporal para descargar un archivo */
function createAndDownloadFile(url, filename) {
   const a = document.createElement('a');
   a.href = url;
   a.download = filename || '';
   document.body.appendChild(a);
   a.click();
   document.body.removeChild(a);
}

/** Formatea una fecha en formato yyyy/mm/dd hh:mm */
function formatDate(dateValue, withTime = true) {
  if (!dateValue) {
    return "";
  }

  const date = new Date(dateValue);
  if (Number.isNaN(date.getTime())) {
    return dateValue;
  }

  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();

  if (!withTime) {
    return `${year}/${month}/${day}`;
  }

  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");

  return `${year}/${month}/${day} ${hours}:${minutes}`;
}
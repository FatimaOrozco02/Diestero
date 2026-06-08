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
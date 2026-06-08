$(document).ready(() => {
   // Datatable
   const certificationsTable = $('#certificationsTable').DataTable({
      ajax: {
         type: "GET",
         url: `${baseUrl}admin/certificaciones/data`,
         dataSrc: 'data',
      },
      scrollX: true,
      scrollCollapse: true,
      language: {
         url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
      },
      dom: '<"text-end mb-2"B><"d-flex justify-content-center justify-content-md-between"lf>rt<"d-flex justify-content-center justify-content-md-between"ip>',
      buttons: [
         {
            extend: 'excelHtml5',
            text: 'Exportar a Excel',
            title: 'Certificaciones',
            className: 'btn btn-success',
            exportOptions: {
               columns: ':not(:last-child)' // Excluye la última columna
            }
         }
      ],
      order: [
         [6, 'desc']
      ],
      columns: [
         { data: 'institution' },
         { data: 'certify_name' },
         { data: 'code' },
         { data: 'start_date' },
         { data: 'end_date' },
         { data: 'is_active', render: (data) => { return (data) ? 'Activo' : 'Inactivo'; } },
         { data: 'created_at', render: (data) => { return formatDate(data); } },
         {
            data: 'id', render: (data, col, row) => {
               return `
                  <a href="${baseUrl}admin/certificaciones/${data}/actualizar" type="button" class="btn btn-main m-1" data-bs-toggle="tooltip" data-bs-title="Editar"><i class="fa-solid fa-pencil"></i></a>
                  <a href="${baseUrl}media/certificaciones/${data}" target="_blank" type="button" class="btn btn-main m-1" data-bs-toggle="tooltip" data-bs-title="Visualizar"><i class="fa-solid fa-eye"></i></a>
               `;
            }
         }
      ]
   });

   // On Datatable redraw
   certificationsTable.on('draw', function () {
      // Tooltips
      var tooltipTriggerListTable = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      var tooltipListTable = [...tooltipTriggerListTable].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
   });
});
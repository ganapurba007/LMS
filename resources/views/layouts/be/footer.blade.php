      <!-- Footer Bar -->
      <footer class="app-footer">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
          {{-- <div>Inspired by Tabler. Built with Bootstrap 5.</div> --}}
        </div>
      </footer>
    </div>
  </div>

  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- Bootstrap 5 JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- DataTables JS CDN -->
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

  <!-- ApexCharts CDN (deferred) -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>

  <!-- Select2 JS CDN -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- TinyMCE CDN & On-Demand Auto-Init (only loads 1.5MB script if textarea.tinymce is present) -->
  <script>
  window.initTinyMCE = function(selector) {
    var targetSelector = selector || 'textarea.tinymce';
    if ($(targetSelector).length === 0) return;

    function doInit() {
      if (typeof tinymce === 'undefined') return;
      var isDark = document.documentElement.getAttribute('data-theme') === 'dark';

      tinymce.init({
        selector: targetSelector,
        height: 400,
        menubar: true,
        plugins: [
          'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
          'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
          'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
          'bold italic backcolor forecolor | alignleft aligncenter ' +
          'alignright alignjustify | bullist numlist outdent indent | ' +
          'removeformat | link image media table | help',
        content_style: 'body { font-family: "Plus Jakarta Sans", sans-serif; font-size: 14px; line-height: 1.6; }',
        skin: isDark ? 'oxide-dark' : 'oxide',
        content_css: isDark ? 'dark' : 'default',
        branding: false,
        promotion: false,
        setup: function(editor) {
          editor.on('change keyup blur', function() {
            editor.save();
          });
        }
      });
    }

    if (typeof tinymce === 'undefined') {
      var s = document.createElement('script');
      s.src = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js';
      s.onload = doInit;
      document.head.appendChild(s);
    } else {
      doInit();
    }
  };

  $(document).ready(function() {
    window.initTinyMCE();
  });
  </script>

  <!-- Custom Scripts -->
  <script src="{{ asset('template/be/assets/js/theme-toggle.js') }}"></script>
  <script src="{{ asset('template/be/assets/js/sidebar.js') }}"></script>

  <!-- Global Select2 Auto-Init (kelas: select2) -->
  <script>
  window.initSelect2 = function(targetSelector) {
    if (!$.fn.select2) return;
    
    var $targets = targetSelector ? $(targetSelector) : $('select.select2, .select2, select.form-select:not(.no-select2)');
    $targets.each(function() {
      var $el = $(this);
      if ($el.hasClass('select2-hidden-accessible') || $el.data('no-select2')) {
        return;
      }

      var placeholder = $el.attr('placeholder') || $el.find('option[value=""]').first().text() || 'Pilih...';
      $el.select2({
        theme: 'bootstrap-5',
        width: $el.data('width') ? $el.data('width') : '100%',
        placeholder: placeholder,
        allowClear: $el.data('allow-clear') !== undefined ? $el.data('allow-clear') : true,
        dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $(document.body)
      });
    });
  };

  $(document).ready(function() {
    window.initSelect2();

    // Trigger onchange handler for form submits on select2 change
    $(document).on('select2:select select2:unselect select2:clear', 'select', function(e) {
      if (typeof this.onchange === 'function') {
        this.onchange();
      }
    });
  });
  </script>

  <!-- Global DataTable Auto-Init (kelas: data-table) -->
  <script>
  $(document).ready(function() {
    if ($.fn.DataTable) {
      // Suppress DataTables alert warnings (use console instead)
      $.fn.dataTable.ext.errMode = 'none';

      $('table.data-table').each(function() {
        $(this).DataTable({
          responsive: true,
          columnDefs: [{ orderable: false, targets: -1 }],
          language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Tidak ada data yang cocok",
            paginate: {
              first: "Pertama",
              last: "Terakhir",
              next: "Berikutnya",
              previous: "Sebelumnya"
            }
          }
        });
      });
    }
  });
  </script>

  <!-- Global Auto-Dismiss Alerts -->
  <script>
  $(document).ready(function() {
    // Sembunyikan alert yang sudah pernah ditutup (data-alert-id disimpan di localStorage)
    $('[data-alert-id]').each(function() {
      var alertId = $(this).data('alert-id');
      if (localStorage.getItem('alert_dismissed_' + alertId)) {
        $(this).remove();
      }
    });

    // Saat tombol X diklik pada alert dengan data-alert-id, simpan ke localStorage
    $(document).on('click', '[data-alert-id] .btn-close', function() {
      var alertId = $(this).closest('[data-alert-id]').data('alert-id');
      if (alertId) {
        localStorage.setItem('alert_dismissed_' + alertId, '1');
      }
    });

    // Auto-dismiss semua alert biasa (session flash): success/info 4 detik, warning/danger 7 detik
    $('.alert').each(function() {
      var $alert = $(this);
      var delay = ($alert.hasClass('alert-warning') || $alert.hasClass('alert-danger')) ? 7000 : 4000;
      setTimeout(function() {
        $alert.fadeTo(500, 0).slideUp(300, function() {
          $(this).remove();
        });
      }, delay);
    });
  });
  </script>

  <!-- PWA Service Worker Registration -->
  <script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
      navigator.serviceWorker.register('/sw.js').then(function(registration) {
        console.log('PWA ServiceWorker registered with scope: ', registration.scope);
      }, function(err) {
        console.log('PWA ServiceWorker registration failed: ', err);
      });
    });
  }
  </script>

  @stack('scripts')
</body>
</html>

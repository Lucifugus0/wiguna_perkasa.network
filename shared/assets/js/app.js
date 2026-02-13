/**
 * ==================== MODERN APP JS ====================
 * Wiguna Perkasa Network - Global JavaScript
 * Created: 2026-02-13
 */

(function($) {
    'use strict';

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        // Initialize Select2
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap',
                width: '100%'
            });
        }

        // Initialize DataTables with modern style
        if ($.fn.DataTable) {
            $('.datatable').DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }

        // Confirm delete action
        $('[data-confirm-delete]').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var message = $(this).data('confirm-delete') || 'Apakah Anda yakin ingin menghapus data ini?';

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert:not(.alert-permanent)').fadeOut('slow');
        }, 5000);

        // Form validation helper
        $('form[data-validate]').on('submit', function(e) {
            var isValid = true;
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    title: 'Form Tidak Lengkap',
                    text: 'Mohon lengkapi semua field yang wajib diisi',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });

        // Number formatting for currency inputs
        $('[data-currency]').on('keyup', function() {
            var value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(parseInt(value).toLocaleString('id-ID'));
            }
        });

        // Tooltip initialization
        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Popover initialization
        if ($.fn.popover) {
            $('[data-toggle="popover"]').popover();
        }
    });

    /**
     * Global helper functions
     */
    window.App = {
        // Show loading overlay
        showLoading: function(message) {
            message = message || 'Loading...';
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },

        // Hide loading overlay
        hideLoading: function() {
            Swal.close();
        },

        // Show success message
        success: function(message, callback) {
            Swal.fire({
                title: 'Berhasil!',
                text: message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(callback);
        },

        // Show error message
        error: function(message) {
            Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        },

        // Show info message
        info: function(message) {
            Swal.fire({
                title: 'Info',
                text: message,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        },

        // Format number as currency
        formatCurrency: function(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        },

        // Format date
        formatDate: function(date) {
            return new Date(date).toLocaleDateString('id-ID');
        }
    };

})(jQuery);

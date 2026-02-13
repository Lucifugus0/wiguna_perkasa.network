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
                pagingType: 'simple_numbers', // Hanya nomor + Previous/Next, tanpa First/Last
                pageLength: 10, // Default 10 items per page
                language: {
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    search: "Cari:",
                    paginate: {
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                drawCallback: function() {
                    // Tunggu sebentar untuk ensure DOM fully rendered
                    setTimeout(function() {
                        var $paginate = $('.dataTables_paginate');

                        // 1. Hapus semua tombol disabled (Previous/Next yang disabled)
                        $paginate.find('.paginate_button.disabled').remove();

                        // 2. Hapus tombol "First" dan "Last" (tidak diperlukan, cukup nomor halaman)
                        $paginate.find('.paginate_button.first').remove();
                        $paginate.find('.paginate_button.last').remove();

                        // 3. Hapus ellipsis (...)
                        $paginate.find('.ellipsis').remove();
                        $paginate.find('span.ellipsis').remove();

                        // 4. Hapus ghost buttons (empty atau tanpa text)
                        $paginate.find('.paginate_button').each(function() {
                            var $btn = $(this);
                            var text = $btn.text().trim();

                            // Hapus jika kosong atau hanya whitespace
                            if (!text || text === '' || text === '...' || text === '…') {
                                $btn.remove();
                                return;
                            }

                            // Hapus jika bukan nomor dan bukan Previous/Next/Selanjutnya/Sebelumnya
                            if (text &&
                                !text.match(/^\d+$/) &&
                                text !== 'Selanjutnya' &&
                                text !== 'Sebelumnya' &&
                                text !== 'Next' &&
                                text !== 'Previous') {
                                $btn.remove();
                            }
                        });

                        // 5. Clean up empty spans
                        $paginate.find('span:empty').remove();

                        // 6. AGGRESSIVE CLEANUP - Hapus semua yang bukan nomor atau Previous/Next
                        // Cek semua elemen di dalam pagination
                        $paginate.find('*').each(function() {
                            var $el = $(this);

                            // Skip jika parent container
                            if ($el.hasClass('dataTables_paginate') || $el.hasClass('pagination')) {
                                return;
                            }

                            var text = $el.text().trim();

                            // Jika elemen punya children, skip (let children handle)
                            if ($el.children().length > 0) {
                                return;
                            }

                            // Hapus jika text tidak valid
                            if (!text ||
                                (text !== '' &&
                                 !text.match(/^\d+$/) &&
                                 text !== 'Selanjutnya' &&
                                 text !== 'Sebelumnya' &&
                                 text !== 'Next' &&
                                 text !== 'Previous')) {
                                $el.remove();
                            }
                        });

                        // 7. Final cleanup - hapus parent span/li yang kosong
                        $paginate.find('span:empty, li:empty').remove();

                        // 8. ULTIMATE FIX: Hitung jumlah li, hapus yang berlebih
                        setTimeout(function() {
                            var $pagination = $paginate.find('ul.pagination');
                            var $items = $pagination.children('li');

                            // Count items: harus ada max (previous + numbers + next)
                            var $previous = $items.filter('.previous');
                            var $next = $items.filter('.next');
                            var $numbers = $items.not('.previous').not('.next').not('.disabled');

                            // Hapus semua yang bukan previous, next, atau angka dengan class current
                            $items.each(function(index) {
                                var $item = $(this);

                                // Keep previous, next, dan nomor halaman
                                if ($item.hasClass('previous') ||
                                    $item.hasClass('next') ||
                                    $item.hasClass('active') ||
                                    $item.hasClass('current') ||
                                    $item.find('a').text().trim().match(/^\d+$/)) {
                                    return; // Skip, keep this
                                }

                                // Hapus yang lain
                                if (!$item.hasClass('disabled')) {
                                    $item.remove();
                                }
                            });

                            // Double check: Jika ada lebih dari 1 item setelah .next, hapus semua
                            var $afterNext = $pagination.find('.next').nextAll();
                            if ($afterNext.length > 0) {
                                $afterNext.remove();
                            }
                        }, 50); // Cleanup tambahan 50ms setelah pertama
                    }, 150); // Increased delay untuk ensure full render
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

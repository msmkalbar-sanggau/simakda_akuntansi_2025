<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#datainputJurnal').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 50],
            ajax: {
                url: "{{ route('input_jurnal.show_load_data') }}",
                type: "post",   
                data: function(d) {
                    d.no_voucher = document.getElementById('no_voucher').value;
                    d.kd_skpd = document.getElementById('kd_skpd').value;
                },
            },
            columns: [{
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                },
                {
                    data: 'nm_sub_kegiatan',
                    name: 'nm_sub_kegiatan',
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6',
                },
                {
                    data: 'debet',
                    name: 'debet',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data)
                    }
                },
                {
                    data: 'kredit',
                    name: 'kredit',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data)
                    }
                },
                {
                    data: 'rk',
                    name: 'rk',
                },
                {
                    data: 'pos',
                    name: 'pos',
                },
            ],
        });
    });
</script>

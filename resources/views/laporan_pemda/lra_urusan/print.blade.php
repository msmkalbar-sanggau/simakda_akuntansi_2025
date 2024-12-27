<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LRA URUSAN</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
        }

        .bordered {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .bordered th,
        .bordered td {
            border: 1px solid black;
            padding: 4px;
        }

        .bordered th {
            background-color: #cccccc;
        }

        .bordered td:nth-child(n+3) {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div id="header" style="border: 1px solid black; text-align: center; width: 100%">
        <img style="width: 84px; float: left; margin: 8px;"
            src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75" height="100" />
        <h4 style="margin: 8px 0px;">PEMERINTAH {{ strtoupper($header->nm_pemda) }}</h4>
        <h4 style="margin: 8px 0px;">REKAPITULASI REALISASI BELANJA DAERAH MENURUT URUSAN PEMERINTAH DAERAH,</h4>
        <h4 style="margin: 8px 0px;">ORGANISASI, PROGRAM, DAN KEGIATAN</h4>
        <h4 style="margin: 8px 0px;">TAHUN ANGGARAN {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div>
    <table class="bordered" style="width: 100%">
        <thead>
            <tr>
                <th rowspan="2">KODE</th>
                <th rowspan="2">URUSAN PEMERINTAH DAERAH</th>
                <th colspan="9">ANGGARAN BELANJA</th>
                <th rowspan="2">JUMLAH</th>
                <th colspan="9">REALISASI BELANJA</th>
                <th rowspan="2">JUMLAH</th>
            </tr>
            <tr>
                <th>PEGAWAI</th>
                <th>BARANG DAN JASA</th>
                <th>BUNGA</th>
                <th>MODAL</th>
                <th>HIBAH</th>
                <th>BANTUAN SOSIAL</th>
                <th>BAGI HASIL</th>
                <th>BANTUAN KEUANGAN</th>
                <th>BELANJA TIDAK TERDUGA</th>
                <th>PEGAWAI</th>
                <th>BARANG DAN JASA</th>
                <th>BUNGA</th>
                <th>MODAL</th>
                <th>HIBAH</th>
                <th>BANTUAN SOSIAL</th>
                <th>BAGI HASIL</th>
                <th>BANTUAN KEUANGAN</th>
                <th>BELANJA TIDAK TERDUGA</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
                <th>11</th>
                <th>12</th>
                <th>13</th>
                <th>14</th>
                <th>15</th>
                <th>16</th>
                <th>17</th>
                <th>18</th>
                <th>19</th>
                <th>20</th>
                <th>21</th>
                <th>22</th>
            </tr>
        </thead>

        <body>
            @php
                $tot_ang = 0;
                $tot_real = 0;
                $tot_ang_peg = 0;
                $tot_ang_brng = 0;
                $tot_ang_bng = 0;
                $tot_ang_mod = 0;
                $tot_ang_hibah = 0;
                $tot_ang_bansos = 0;
                $tot_ang_bghasil = 0;
                $tot_ang_bankeu = 0;
                $tot_ang_btt = 0;
                $tot_real_peg = 0;
                $tot_real_brng = 0;
                $tot_real_bng = 0;
                $tot_real_mod = 0;
                $tot_real_hibah = 0;
                $tot_real_bansos = 0;
                $tot_real_bghasil = 0;
                $tot_real_bankeu = 0;
                $tot_real_btt = 0;
                $tot_ang_akhir = 0;
                $tot_real_akhir = 0;
            @endphp
            @foreach ($data as $key => $value)
                @php
                    if (strlen($value->kode) == '1') {
                        $tot_ang_peg += $value->ang_peg;
                        $tot_ang_brng += $value->ang_brng;
                        $tot_ang_bng += $value->ang_bng;
                        $tot_ang_mod += $value->ang_mod;
                        $tot_ang_hibah += $value->ang_hibah;
                        $tot_ang_bansos += $value->ang_bansos;
                        $tot_ang_bghasil += $value->ang_bghasil;
                        $tot_ang_bankeu += $value->ang_bankeu;
                        $tot_ang_btt += $value->ang_btt;
                        $tot_real_peg += $value->real_peg;
                        $tot_real_brng += $value->real_brng;
                        $tot_real_bng += $value->real_bng;
                        $tot_real_mod += $value->real_mod;
                        $tot_real_hibah += $value->real_hibah;
                        $tot_real_bansos += $value->real_bansos;
                        $tot_real_bghasil += $value->real_bghasil;
                        $tot_real_bankeu += $value->real_bankeu;
                        $tot_real_btt += $value->real_btt;
                        $tot_ang_akhir += $value->ang_peg + $value->ang_brng + $value->ang_bng + $value->ang_mod + $value->ang_hibah + $value->ang_bansos + $value->ang_bghasil + $value->ang_bankeu + $value->ang_btt;
                        $tot_real_akhir += $value->real_peg + $value->real_brng + $value->real_bng + $value->real_mod + $value->real_hibah + $value->real_bansos + $value->real_bghasil + $value->real_bankeu + $value->real_btt;
                    }
                    $tot_ang = $value->ang_peg + $value->ang_brng + $value->ang_bng + $value->ang_mod + $value->ang_hibah + $value->ang_bansos + $value->ang_bghasil + $value->ang_bankeu + $value->ang_btt;
                    $tot_real = $value->real_peg + $value->real_brng + $value->real_bng + $value->real_mod + $value->real_hibah + $value->real_bansos + $value->real_bghasil + $value->real_bankeu + $value->real_btt;
                @endphp
                @if (strlen($value->kode) == '1')
                    <tr>
                        <td><b>{{ $value->kode }}</b></td>
                        <td><b>{{ $value->nm_rek }}</b></td>
                        <td><b>{{ rupiah($value->ang_peg) }}</b></td>
                        <td><b>{{ rupiah($value->ang_brng) }}</b></td>
                        <td><b>{{ rupiah($value->ang_bng) }}</b></td>
                        <td><b>{{ rupiah($value->ang_mod) }}</b></td>
                        <td><b>{{ rupiah($value->ang_hibah) }}</b></td>
                        <td><b>{{ rupiah($value->ang_bansos) }}</b></td>
                        <td><b>{{ rupiah($value->ang_bghasil) }}</b></td>
                        <td><b>{{ rupiah($value->ang_bankeu) }}</b></td>
                        <td><b>{{ rupiah($value->ang_btt) }}</b></td>
                        <td><b>{{ rupiah($tot_ang) }}</b></td>
                        <td><b>{{ rupiah($value->real_peg) }}</b></td>
                        <td><b>{{ rupiah($value->real_brng) }}</b></td>
                        <td><b>{{ rupiah($value->real_bng) }}</b></td>
                        <td><b>{{ rupiah($value->real_mod) }}</b></td>
                        <td><b>{{ rupiah($value->real_hibah) }}</b></td>
                        <td><b>{{ rupiah($value->real_bansos) }}</b></td>
                        <td><b>{{ rupiah($value->real_bghasil) }}</b></td>
                        <td><b>{{ rupiah($value->real_bankeu) }}</b></td>
                        <td><b>{{ rupiah($value->real_btt) }}</b></td>
                        <td><b>{{ rupiah($tot_real) }}</b></td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $value->kode }}</td>
                        <td>{{ $value->nm_rek }}</td>
                        <td>{{ rupiah($value->ang_peg) }}</td>
                        <td>{{ rupiah($value->ang_brng) }}</td>
                        <td>{{ rupiah($value->ang_bng) }}</td>
                        <td>{{ rupiah($value->ang_mod) }}</td>
                        <td>{{ rupiah($value->ang_hibah) }}</td>
                        <td>{{ rupiah($value->ang_bansos) }}</td>
                        <td>{{ rupiah($value->ang_bghasil) }}</td>
                        <td>{{ rupiah($value->ang_bankeu) }}</td>
                        <td>{{ rupiah($value->ang_btt) }}</td>
                        <td>{{ rupiah($tot_ang) }}</td>
                        <td>{{ rupiah($value->real_peg) }}</td>
                        <td>{{ rupiah($value->real_brng) }}</td>
                        <td>{{ rupiah($value->real_bng) }}</td>
                        <td>{{ rupiah($value->real_mod) }}</td>
                        <td>{{ rupiah($value->real_hibah) }}</td>
                        <td>{{ rupiah($value->real_bansos) }}</td>
                        <td>{{ rupiah($value->real_bghasil) }}</td>
                        <td>{{ rupiah($value->real_bankeu) }}</td>
                        <td>{{ rupiah($value->real_btt) }}</td>
                        <td>{{ rupiah($tot_real) }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td colspan="2" style="text-align: center"><b>Total</b></td>
                <td><b>{{ rupiah($tot_ang_peg) }}</b></td>
                <td><b>{{ rupiah($tot_ang_brng) }}</b></td>
                <td><b>{{ rupiah($tot_ang_bng) }}</b></td>
                <td><b>{{ rupiah($tot_ang_mod) }}</b></td>
                <td><b>{{ rupiah($tot_ang_hibah) }}</b></td>
                <td><b>{{ rupiah($tot_ang_bansos) }}</b></td>
                <td><b>{{ rupiah($tot_ang_bghasil) }}</b></td>
                <td><b>{{ rupiah($tot_ang_bankeu) }}</b></td>
                <td><b>{{ rupiah($tot_ang_btt) }}</b></td>
                <td><b>{{ rupiah($tot_ang_akhir) }}</b></td>
                <td><b>{{ rupiah($tot_real_peg) }}</b></td>
                <td><b>{{ rupiah($tot_real_brng) }}</b></td>
                <td><b>{{ rupiah($tot_real_bng) }}</b></td>
                <td><b>{{ rupiah($tot_real_mod) }}</b></td>
                <td><b>{{ rupiah($tot_real_hibah) }}</b></td>
                <td><b>{{ rupiah($tot_real_bansos) }}</b></td>
                <td><b>{{ rupiah($tot_real_bghasil) }}</b></td>
                <td><b>{{ rupiah($tot_real_bankeu) }}</b></td>
                <td><b>{{ rupiah($tot_real_btt) }}</b></td>
                <td><b>{{ rupiah($tot_real_akhir) }}</b></td>
            </tr>
        </body>
    </table>
    <div style="padding: 16px; font-size: 14px;">
        <div style="float: right; text-align: center;">
            {{ $daerah->daerah }}, {{ tanggal($tgl_ttd) }}<br />
            {{ $ttd->jabatan }}
            <div style="height: 64px;"></div>
            <b><u><?= $ttd->nama ?></u></b>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>

</html>

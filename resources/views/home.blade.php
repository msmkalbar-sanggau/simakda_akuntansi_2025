@extends('layouts.template')
@section('title', 'Beranda | SIMAKDA')

@section('content')
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mt-0 mb-3">Pendapatan</h4>
                    <div id="pie-chart">
                        <div id="persentase" style="height: 250px;">
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- end col-->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mt-0 mb-3">Belanja</h4>
                    <div id="pie-chart">
                        <div id="persentase1" style="height: 250px;">
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- end col-->
    </div>
@endsection

@section('js')
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
         var data = google.visualization.arrayToDataTable([
            ['Pendapatan', 'Persen'],
            ['Pagu', {{ $pagu_pendapatan - $rea_pendapatan }}],
            ['Realisasi', {{ $rea_pendapatan }}]
        ]);
        var options = {                
            curveType: 'function',
            pieHole: 0.4,
            legend: { position: 'bottom' },
            // legend: none,
            chartArea: {width: 220, height: 210}
        };
        var chart = new google.visualization.PieChart(document.getElementById('persentase'));
        chart.draw(data, options);
    }
</script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
         var data = google.visualization.arrayToDataTable([
            ['Belanja', 'Persen'],
            ['Pagu', {{ $pagu_belanja - $rea_belanja }}],
            ['Realisasi', {{ $rea_belanja }}]
        ]);
        var options = {                
            curveType: 'function',
            pieHole: 0.4,
            legend: { position: 'bottom' },
            // legend: none,
            chartArea: {width: 220, height: 210}
        };
        var chart = new google.visualization.PieChart(document.getElementById('persentase1'));
        chart.draw(data, options);
    }
</script>
@endsection

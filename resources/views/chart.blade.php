@extends('layouts.sidebar')

@section('dashboard-content')
<section class="section">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4>Line Area Chart</h4>
                </div>
                <div class="card-body">
                    <div id="area"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4>Radial Gradient Chart</h4>
                </div>
                <div class="card-body">
                    <div id="radialGradient"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Line Chart</h4>
                </div>
                <div class="card-body">
                    <div id="line"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Bar Chart</h4>
                </div>
                <div class="card-body">
                    <div id="bar"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Candlestick Chart</h4>
                </div>
                <div class="card-body">
                    <div id="candle"></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('dashboard-script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Line Area Chart
    var areaOptions = {
        chart: {
            type: 'area'
        },
        series: [{
            name: 'sales',
            data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
        }],
        xaxis: {
            categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999]
        }
    };
    var areaChart = new ApexCharts(document.querySelector("#area"), areaOptions);
    areaChart.render();

    // Radial Gradient Chart
    var radialOptions = {
        chart: {
            type: 'radialBar'
        },
        series: [67],
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '70%',
                }
            }
        },
        labels: ['Cricket']
    };
    var radialChart = new ApexCharts(document.querySelector("#radialGradient"), radialOptions);
    radialChart.render();

    // Line Chart
    var lineOptions = {
        chart: {
            type: 'line'
        },
        series: [{
            name: 'sales',
            data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
        }],
        xaxis: {
            categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999]
        }
    };
    var lineChart = new ApexCharts(document.querySelector("#line"), lineOptions);
    lineChart.render();

    // Bar Chart
    var barOptions = {
        chart: {
            type: 'bar'
        },
        series: [{
            name: 'sales',
            data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
        }],
        xaxis: {
            categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999]
        }
    };
    var barChart = new ApexCharts(document.querySelector("#bar"), barOptions);
    barChart.render();

    // Candlestick Chart
    var candleOptions = {
        chart: {
            type: 'candlestick'
        },
        series: [{
            data: [{
                x: new Date(1538778600000),
                y: [6629.81, 6650.5, 6623.04, 6633.33]
            }, {
                x: new Date(1538780400000),
                y: [6632.01, 6643.59, 6620, 6630.11]
            }]
        }],
        xaxis: {
            type: 'datetime'
        }
    };
    var candleChart = new ApexCharts(document.querySelector("#candle"), candleOptions);
    candleChart.render();
</script>
@endsection

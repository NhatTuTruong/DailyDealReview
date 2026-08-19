@extends('backend.index')

@section('title')
    DashBoard
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $total['store'] ?? 0 }}</h3>
                        <p>Store</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <a href="{{ route('backend_store') }}" class="small-box-footer">Xem chi tiết
                        <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $total['offer'] ?? 0 }}</h3>
                        <p>Offers</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <a href="{{ route('backend_offer') }}" class="small-box-footer">Xem chi tiết
                        <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $total['deal'] ?? 0 }}</h3>
                        <p>Deals</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-percent"></i>
                    </div>
                    <a href="#!" class="small-box-footer">Xem chi tiết
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $total['post'] }}</h3>
                        <p>Post</p>
                    </div>
                    <div class="icon">
                        <i class="far fa-newspaper"></i>
                    </div>
                    <a href="{{ route('backend_post') }}" class="small-box-footer">Xem chi tiết <i
                                class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <div class="row">
            <div class="col-6">
                <canvas id="summarize_store"></canvas>
            </div>
            <div class="col-6">
                <canvas id="summarize_ads_status"></canvas>
            </div>
        </div>
    </div>

@endsection

@section('bottom')
    <script src="{{ asset('backend_assets/vendor/chart/chart.js') }}"></script>
    <script>
        const ctx = document.getElementById('summarize_store');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chart_summarize_store['labels']) !!},
                datasets: [{
                    label: '# Tổng hợp store',
                    data: {!! json_encode($chart_summarize_store['totals']) !!},
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        type: 'logarithmic',
                        min: 1, // tránh log(0)
                        title: {
                            display: true,
                            text: 'Tổng số store'
                        },
                        ticks: {
                            callback: function (value) {
                                return value.toLocaleString(); // format số dễ đọc
                            }
                        }
                    }
                }
            }
        });

        const ctx2 = document.getElementById('summarize_ads_status');

        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chart_summarize_ads_status['labels']) !!},
                datasets: [{
                    label: '# Tổng hợp ads_status',
                    data: {!! json_encode($chart_summarize_ads_status['totals']) !!},
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        type: 'logarithmic',
                        min: 1, // tránh log(0)
                        title: {
                            display: true,
                            text: 'Tổng số store'
                        },
                        ticks: {
                            callback: function (value) {
                                return value.toLocaleString(); // format số dễ đọc
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection


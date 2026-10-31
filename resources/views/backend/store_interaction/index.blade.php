@extends('backend.index')

@section('title')
    Thống kê tương tác
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Thống kê tương tác</li>
@endsection

@section('content')
    <hr class="mt-0">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total']) }}</h3>
                            <p>Lượt xem</p>
                        </div>
                        <div class="icon"><i class="fas fa-eye"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['unique_ip']) }}</h3>
                            <p>IP duy nhất</p>
                        </div>
                        <div class="icon"><i class="fas fa-network-wired"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['unique_store']) }}</h3>
                            <p>Cửa hàng</p>
                        </div>
                        <div class="icon"><i class="fas fa-store"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($stats['today']) }}</h3>
                            <p>Hôm nay</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                    </div>
                </div>
            </div>

            @include('backend.store_interaction._tabs', ['activeTab' => 'logs', 'suspiciousCount' => $suspiciousCount, 'blockedCount' => $blockedCount])

            <div class="mb-2 text-right">
                <x-forms.button-bulk-delete title="Xóa đã chọn" url="{{ route('backend_store_interaction_bulk_delete') }}"/>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('backend_store_interaction') }}" class="form-filter-top-index">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" name="keyword" class="form-control"
                                       value="{{ $filter['keyword'] }}" placeholder="IP, cửa hàng, trình duyệt...">
                            </div>
                            <div class="col-md-2">
                                <select name="store_id" class="form-control" onchange="this.form.submit()">
                                    <option value="0">Tất cả cửa hàng</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" @selected($filter['store_id'] == $store->id)>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="device" class="form-control" onchange="this.form.submit()">
                                    <option value="">Tất cả thiết bị</option>
                                    @foreach($devices as $device)
                                        <option value="{{ $device }}" @selected($filter['device'] === $device)>
                                            {{ $device }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="country" class="form-control" onchange="this.form.submit()">
                                    <option value="">Tất cả quốc gia</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country }}" @selected($filter['country'] === $country)>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="from_date" class="form-control"
                                       value="{{ $filter['from_date'] }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="to_date" class="form-control"
                                       value="{{ $filter['to_date'] }}">
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
                                <a href="{{ route('backend_store_interaction') }}" class="btn btn-default btn-sm">Xóa lọc</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-grid-admin mb-0">
                        <thead>
                        <tr>
                            <th width="1%">
                                <div class="icheck-primary">
                                    <input type="checkbox" class="checkall" id="store-log-checkall">
                                    <label for="store-log-checkall"></label>
                                </div>
                            </th>
                            <th>Cửa hàng</th>
                            <th>IP</th>
                            <th>Thiết bị</th>
                            <th>Trình duyệt</th>
                            <th>Hệ điều hành</th>
                            <th>Quốc gia</th>
                            <th>User Agent</th>
                            <th>Thời gian</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <div class="icheck-primary">
                                        <input type="checkbox" class="checker chk" id="store_log_chk_{{ $log->id }}"
                                               value="{{ $log->id }}">
                                        <label for="store_log_chk_{{ $log->id }}"></label>
                                    </div>
                                </td>
                                <td>
                                    @if($log->store)
                                        <a href="{{ \App\Libs\Util::url_store($log->store) }}" target="_blank">
                                            {{ $log->store->name }}
                                        </a>
                                    @else
                                        {{ $log->store_name ?: ('#' . $log->store_id) }}
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($log->ip, $suspiciousIps, true))
                                        <a href="{{ route('backend_store_interaction_suspicious') }}" class="text-danger font-weight-bold" title="IP đáng ngờ">
                                            {{ $log->ip }}
                                        </a>
                                    @else
                                        {{ $log->ip }}
                                    @endif
                                </td>
                                <td>{{ $log->device ?: '-' }}</td>
                                <td>{{ $log->browser ?: '-' }}</td>
                                <td>{{ $log->os ?: '-' }}</td>
                                <td>
                                    @if($log->country_code && $log->country_code !== 'LO')
                                        <span class="flag-icon flag-icon-{{ strtolower($log->country_code) }}"></span>
                                    @endif
                                    {{ $log->country ?: '-' }}
                                </td>
                                <td style="max-width: 280px; word-break: break-all;">
                                    <small title="{{ $log->user_agent }}">{{ $log->user_agent ?: '-' }}</small>
                                </td>
                                <td nowrap>{{ optional($log->created_at)->format('d-m-Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center p-4">Chưa có lượt xem store nào.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $logs->links() }}
        </div>
    </section>
@endsection

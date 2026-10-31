@extends('backend.index')

@section('title')
    IP đáng ngờ
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_store_interaction') }}">Thống kê tương tác</a></li>
    <li class="breadcrumb-item active">IP đáng ngờ</li>
@endsection

@section('content')
    <hr class="mt-0">
    <section class="content">
        <div class="container-fluid">
            @include('backend.store_interaction._tabs', ['activeTab' => 'suspicious', 'suspiciousCount' => $suspiciousCount, 'blockedCount' => $blockedCount])

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('backend_store_interaction_suspicious') }}" class="form-inline">
                        <label class="mr-2">Trạng thái</label>
                        <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                            <option value="open" @selected($status === 'open')>Đang mở</option>
                            <option value="ignored" @selected($status === 'ignored')>Đã bỏ qua</option>
                            <option value="all" @selected($status === 'all')>Tất cả</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-grid-admin mb-0">
                        <thead>
                        <tr>
                            <th>IP</th>
                            <th>Điểm</th>
                            <th>Dấu hiệu</th>
                            <th>Xem / 5 phút</th>
                            <th>Affiliate / 5 phút</th>
                            <th>Store</th>
                            <th>Quốc gia</th>
                            <th>User Agent</th>
                            <th>Phát hiện</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('backend_store_interaction', ['keyword' => $item->ip]) }}">
                                        {{ $item->ip }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $item->score >= 60 ? 'danger' : 'warning' }}">
                                        {{ $item->score }}
                                    </span>
                                </td>
                                <td>
                                    @foreach($item->reasonLabels() as $label)
                                        <span class="badge badge-secondary mb-1">{{ $label }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $item->view_count_window }}</td>
                                <td>{{ $item->affiliate_count_window }}</td>
                                <td>{{ $item->unique_store_count }}</td>
                                <td>{{ $item->country ?: '-' }}</td>
                                <td style="max-width: 240px; word-break: break-all;">
                                    <small title="{{ $item->last_user_agent }}">{{ $item->last_user_agent ?: '-' }}</small>
                                </td>
                                <td nowrap>
                                    {{ optional($item->last_detected_at)->format('d-m-Y H:i:s') }}
                                    <br>
                                    <small class="text-muted">{{ $item->status === 'ignored' ? 'Đã bỏ qua' : 'Đang mở' }}</small>
                                </td>
                                <td nowrap>
                                    @if(in_array($item->ip, $blockedIps, true))
                                        <form method="POST" action="{{ route('backend_store_interaction_unblock_ip') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="ip_address" value="{{ $item->ip }}">
                                            <button type="submit" class="btn btn-sm btn-success">Gỡ chặn</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('backend_store_interaction_block_ip') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="ip_address" value="{{ $item->ip }}">
                                            <input type="hidden" name="note" value="Chặn từ IP đáng ngờ">
                                            <button type="submit" class="btn btn-sm btn-danger">Chặn IP</button>
                                        </form>
                                    @endif
                                    <form method="POST" class="d-inline mt-1"
                                          action="{{ route('backend_store_interaction_suspicious_status', $item) }}">
                                        @csrf
                                        @if($item->status === 'ignored')
                                            <input type="hidden" name="status" value="open">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Mở lại</button>
                                        @else
                                            <input type="hidden" name="status" value="ignored">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Bỏ qua</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center p-4">Chưa phát hiện IP đáng ngờ.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $items->links() }}
        </div>
    </section>
@endsection

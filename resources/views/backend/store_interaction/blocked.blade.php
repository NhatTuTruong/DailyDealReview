@extends('backend.index')

@section('title')
    Chặn IP
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_store_interaction') }}">Thống kê tương tác</a></li>
    <li class="breadcrumb-item active">Chặn IP</li>
@endsection

@section('content')
    <hr class="mt-0">
    <section class="content">
        <div class="container-fluid">
            @include('backend.store_interaction._tabs', ['activeTab' => 'blocked', 'suspiciousCount' => $suspiciousCount, 'blockedCount' => $blockedCount])

            <div class="card mb-3">
                <div class="card-body">
                    <form method="POST" action="{{ route('backend_store_interaction_block_ip') }}" class="form-inline">
                        @csrf
                        <label class="mr-2">IP</label>
                        <input type="text" name="ip_address" class="form-control mr-2" placeholder="Ví dụ: 203.0.113.10"
                               value="{{ old('ip_address') }}" required>
                        <input type="text" name="note" class="form-control mr-2" placeholder="Ghi chú (không bắt buộc)"
                               value="{{ old('note') }}">
                        <button type="submit" class="btn btn-danger">Chặn IP</button>
                    </form>
                    @error('ip_address')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                    <p class="text-muted mb-0 mt-2">
                        IP bị chặn không vào được website (trang store, trang chủ, API).
                        Admin / login không bị chặn để bạn vẫn gỡ được.
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-grid-admin mb-0">
                        <thead>
                        <tr>
                            <th>IP</th>
                            <th>Ghi chú</th>
                            <th>Thời gian chặn</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->ip }}</td>
                                <td>{{ $item->note ?: '-' }}</td>
                                <td>{{ optional($item->created_at)->format('d-m-Y H:i:s') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('backend_store_interaction_unblock_ip') }}">
                                        @csrf
                                        <input type="hidden" name="ip_address" value="{{ $item->ip }}">
                                        <button type="submit" class="btn btn-sm btn-success">Gỡ chặn</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center p-4">Chưa chặn IP nào.</td>
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

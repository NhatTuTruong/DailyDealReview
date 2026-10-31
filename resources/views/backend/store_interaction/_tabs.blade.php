<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link {{ ($activeTab ?? '') === 'logs' ? 'active' : '' }}"
           href="{{ route('backend_store_interaction') }}">Lượt xem</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ ($activeTab ?? '') === 'suspicious' ? 'active' : '' }}"
           href="{{ route('backend_store_interaction_suspicious') }}">
            IP đáng ngờ
            @if(($suspiciousCount ?? 0) > 0)
                <span class="badge badge-danger">{{ $suspiciousCount }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ ($activeTab ?? '') === 'blocked' ? 'active' : '' }}"
           href="{{ route('backend_store_interaction_blocked') }}">
            Chặn IP
            @if(($blockedCount ?? 0) > 0)
                <span class="badge badge-dark">{{ $blockedCount }}</span>
            @endif
        </a>
    </li>
</ul>

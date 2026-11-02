<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use App\Models\Store;
use App\Models\StoreViewLog;
use App\Models\SuspiciousIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StoreInteractionController extends Controller
{
    public function __construct()
    {
        $this->selectedMainMenu = 'store_interaction';

        parent::__construct();

        if (!Gate::allows('store_interaction')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $visitor = trim((string) $request->get('visitor', StoreViewLog::VISITOR_HUMAN));
        if (!array_key_exists($visitor, StoreViewLog::VISITOR_LABELS)) {
            $visitor = StoreViewLog::VISITOR_HUMAN;
        }

        $filter = [
            'visitor' => $visitor,
            'keyword' => trim((string) $request->get('keyword')),
            'store_id' => (int) $request->get('store_id', 0),
            'device' => trim((string) $request->get('device')),
            'country' => trim((string) $request->get('country')),
            'from_date' => trim((string) $request->get('from_date')),
            'to_date' => trim((string) $request->get('to_date')),
        ];

        $query = StoreViewLog::query()
            ->with('store:id,name,slug')
            ->where('event_type', '!=', StoreViewLog::TYPE_LEAVE)
            ->visitor($filter['visitor']);

        if ($filter['keyword'] !== '') {
            $keyword = $filter['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('store_name', 'like', '%' . $keyword . '%')
                    ->orWhere('ip', 'like', '%' . $keyword . '%')
                    ->orWhere('browser', 'like', '%' . $keyword . '%')
                    ->orWhere('os', 'like', '%' . $keyword . '%')
                    ->orWhere('country', 'like', '%' . $keyword . '%')
                    ->orWhere('user_agent', 'like', '%' . $keyword . '%');
            });
        }

        if ($filter['store_id'] > 0) {
            $query->where('store_id', $filter['store_id']);
        }

        if ($filter['device'] !== '') {
            $query->where('device', $filter['device']);
        }

        if ($filter['country'] !== '') {
            $query->where('country', $filter['country']);
        }

        if ($filter['from_date'] !== '') {
            $query->whereDate('created_at', '>=', $filter['from_date']);
        }

        if ($filter['to_date'] !== '') {
            $query->whereDate('created_at', '<=', $filter['to_date']);
        }

        $stats = [
            'total' => (clone $query)->count(),
            'unique_ip' => (int) (clone $query)->selectRaw('COUNT(DISTINCT ip) as aggregate')->value('aggregate'),
            'unique_store' => (int) (clone $query)->selectRaw('COUNT(DISTINCT store_id) as aggregate')->value('aggregate'),
            'today' => StoreViewLog::query()
                ->where('event_type', '!=', StoreViewLog::TYPE_LEAVE)
                ->visitor($filter['visitor'])
                ->whereDate('created_at', now()->toDateString())
                ->count(),
        ];

        $logs = $query->orderByDesc('id')->paginate(30)->withQueryString();

        $stores = Store::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->limit(500)
            ->get();

        $devices = StoreViewLog::query()
            ->whereNotNull('device')
            ->where('device', '<>', 'Bot')
            ->distinct()
            ->orderBy('device')
            ->pluck('device');

        $countries = StoreViewLog::query()
            ->whereNotNull('country')
            ->where('country', '<>', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        $suspiciousIps = SuspiciousIp::query()
            ->where('status', SuspiciousIp::STATUS_OPEN)
            ->pluck('ip')
            ->all();
        $suspiciousCount = SuspiciousIp::query()->where('status', SuspiciousIp::STATUS_OPEN)->count();
        $blockedIps = BlockedIp::cachedList();
        $blockedCount = BlockedIp::query()->count();

        return view('backend.store_interaction.index', compact(
            'logs',
            'filter',
            'stats',
            'stores',
            'devices',
            'countries',
            'suspiciousIps',
            'suspiciousCount',
            'blockedIps',
            'blockedCount'
        ));
    }

    public function suspicious(Request $request)
    {
        $status = $request->get('status', SuspiciousIp::STATUS_OPEN);
        if (!in_array($status, [SuspiciousIp::STATUS_OPEN, SuspiciousIp::STATUS_IGNORED, 'all'], true)) {
            $status = SuspiciousIp::STATUS_OPEN;
        }

        $query = SuspiciousIp::query()->orderByDesc('score')->orderByDesc('last_detected_at');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $items = $query->paginate(30)->withQueryString();
        $this->attachStoreNames($items);
        $suspiciousCount = SuspiciousIp::query()->where('status', SuspiciousIp::STATUS_OPEN)->count();
        $blockedCount = BlockedIp::query()->count();
        $blockedIps = BlockedIp::cachedList();

        return view('backend.store_interaction.suspicious', compact(
            'items',
            'status',
            'suspiciousCount',
            'blockedCount',
            'blockedIps'
        ));
    }

    public function blockedIps()
    {
        $items = BlockedIp::query()->orderByDesc('id')->paginate(30);
        $suspiciousCount = SuspiciousIp::query()->where('status', SuspiciousIp::STATUS_OPEN)->count();
        $blockedCount = $items->total();

        return view('backend.store_interaction.blocked', compact(
            'items',
            'suspiciousCount',
            'blockedCount'
        ));
    }

    public function blockIp(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip',
            'note' => 'nullable|string|max:255',
        ], [
            'ip_address.required' => 'Vui lòng nhập IP.',
            'ip_address.ip' => 'IP không hợp lệ.',
        ]);

        $ip = $validated['ip_address'];
        if ($ip === $request->ip()) {
            return redirect()->back()->with('error', 'Không thể chặn IP đang dùng để đăng nhập admin.');
        }

        BlockedIp::query()->firstOrCreate(
            ['ip' => $ip],
            [
                'note' => $validated['note'] ?? null,
                'created_by' => Auth::id(),
            ]
        );
        BlockedIp::forgetCache();

        return redirect()->back()->with('success', 'Đã chặn IP ' . $ip);
    }

    public function unblockIp(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip',
        ]);

        BlockedIp::query()->where('ip', $validated['ip_address'])->get()->each->delete();
        BlockedIp::forgetCache();

        return redirect()->back()->with('success', 'Đã gỡ chặn IP ' . $validated['ip_address']);
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        if ($ids === []) {
            return $this->responseJsonBadRequest();
        }

        StoreViewLog::query()
            ->whereIn('id', $ids)
            ->where('event_type', '!=', StoreViewLog::TYPE_LEAVE)
            ->delete();

        return $this->responseJsonOk();
    }

    public function updateSuspiciousStatus(Request $request, SuspiciousIp $suspiciousIp)
    {
        $status = $request->get('status');
        if (!in_array($status, [SuspiciousIp::STATUS_OPEN, SuspiciousIp::STATUS_IGNORED], true)) {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ');
        }

        $suspiciousIp->status = $status;
        $suspiciousIp->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái IP');
    }

    private function attachStoreNames($items): void
    {
        $missingIps = collect($items->items())
            ->filter(fn ($item) => empty($item->store_names))
            ->pluck('ip')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($missingIps === []) {
            return;
        }

        $rows = StoreViewLog::query()
            ->whereIn('ip', $missingIps)
            ->whereNotNull('store_name')
            ->where('store_name', '<>', '')
            ->orderByDesc('id')
            ->get(['ip', 'store_name']);

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row->ip] ??= [];
            if (!in_array($row->store_name, $grouped[$row->ip], true)) {
                $grouped[$row->ip][] = $row->store_name;
            }
        }

        foreach ($items as $item) {
            if (empty($item->store_names) && !empty($grouped[$item->ip])) {
                $item->setAttribute('store_names', $grouped[$item->ip]);
            }
        }
    }
}

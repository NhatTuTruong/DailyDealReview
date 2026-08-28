<?php

namespace App\Http\Controllers\Backend;

use App\Exports\StoreOfferExport;
use App\Imports\StoreOfferImport;
use App\Imports\StoreOfferPreview;
use App\Libs\DataGrid;
use App\Libs\Util;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Category;
use App\Models\Offer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class StoreController extends Controller
{
    private Store $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
        $this->selectedMainMenu = 'store';

        parent::__construct();

        if (!Gate::allows('store')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $language = App::getLocale();
        $this->selectedSubMenu('store');
        $category = new Category();
        $category->getParentArray();
        $user = auth()->user();

        $paginate = 15;

        $filter['name'] = $request->get('name', '');
        $filter['cat_id'] = $request->get('cat_id', 0);
        $filter['ads_user_id'] = $request->get('ads_user_id', 0);
        $filter['status'] = $request->get('status', -1);
        $filter['af_flag'] = $request->get('af_flag', -1);
        $filter['af_net'] = $request->get('af_net', '');
        $filter['af_visit'] = $request->get('af_visit', 0);
        $filter['ads_status'] = $request->get('ads_status', '');
        $filter['sort_by'] = $request->get('sort_by', '');
        $filter['sort_order'] = $request->get('sort_order', 'desc');
        $query = Store::with(['category', 'user'])
            ->where('language', $language);

        if (!$user->isSuperAdmin()) {
//            $query->where(function ($q) use ($user, $filter) {
//                $q->where('user_id', $user->id)
//                    ->orWhere('user_id', 0);
//            });
        }

        if ($filter['name'] !== '') {
            $query->where(function ($q) use ($user, $filter) {
                $q->where('name', 'like', '%' . $filter['name'] . '%')
                    ->orWhere('slug', 'like', '%' . $filter['name'] . '%')
                    ->orWhere('af_website', 'like', '%' . $filter['name'] . '%')
                    ->orWhere('id', $filter['name']);
            });
        }
        if ($filter['cat_id'] > 0) {
            $all_cat = $category->getAllCatStr($filter['cat_id']);
            $all_cat[] = (int)$filter['cat_id'];
            //$category_stores = CategoryProduct::whereIn('cat_id', $all_cat)->pluck('store_id')->toArray();
            $query->whereIn('cat_id', $all_cat);
        }
        if ($filter['status'] > -1) {
            $query->where('status', $filter['status']);
        }
        if ($filter['af_flag'] > -1) {
            $query->where('af_flag', $filter['af_flag']);
        }
        if ($filter['af_net']) {
            $query->where('af_net', $filter['af_net']);
        }
        if ($filter['ads_status']) {
            $query->where('ads_status', $filter['ads_status']);
        }
        if ($filter['ads_user_id'] > 0) {
            $query->where('ads_user_id', $filter['ads_user_id']);
        }
        if ($filter['af_visit'] > 0) {
            $query->where('af_visit', '>=', $filter['af_visit']);
        }
        if ($filter['sort_by'] && in_array($filter['sort_by'], ['view_num'])) {
            $query->orderBy($filter['sort_by'], $filter['sort_order']);
        } else {
            $query->orderBy('id', 'desc');
        }

        $stores = $query->paginate($paginate)->appends($filter);
        $options['categories'] = Category::makeListCategory(0, Category::CATEGORY_TYPE_STORE, $filter['cat_id']);
        $options['events'] = Category::makeListCategory(0, Category::CATEGORY_TYPE_EVENT, $filter['cat_id']);
        $options['status'] = Util::makeHTMLOptions(Store::STATE_ARRAY, $filter['status']);
        $options['af_flag'] = Util::makeHTMLOptions(Store::AFFILIATE_STATUS, $filter['af_flag']);
        $options['af_net'] = Util::makeHTMLOptions(Store::AFFILIATE_NETS, $filter['af_net']);
        $options['ads_status'] = Util::makeHTMLOptions(Store::ADS_STATUS, $filter['ads_status']);
        $options['ads_users'] = User::makeListUser($filter['ads_user_id']);
        $arr_categories = Category::makeArrayListCategory(0, Category::CATEGORY_TYPE_STORE);

        $route_name = 'backend_store_edit';
        $option_column_button = Store::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='15%' nowrap");
        $clsDataGrid->addColumnLabel("af_status", "Aff Flag", "width='5%' nowrap");
        $clsDataGrid->addColumnLabel("ads_status_info", "Ads Status", "width='5%' nowrap");
        $clsDataGrid->addColumnImage("image", "Image", "", "width='10%'  nowrap");
        $clsDataGrid->addColumnLabel("user_name", "User Name", "width='5%' nowrap");
        $clsDataGrid->addColumnSelect("status", "Hiển thị", "width='5%' ", ["Không", "Có"]);
        //$clsDataGrid->addColumnSelect("cat_id", "Danh mục", "width='5%' ", $arr_categories);
        $clsDataGrid->addColumnLabel("view_num", "Lượt xem", "width='5%' nowrap");
        $clsDataGrid->addColumnLabel("af_net", "Aff Net", "width='5%' nowrap");
        $clsDataGrid->addColumnLabel("af_visit", "Lượt visit", "width='5%' nowrap");
//        $clsDataGrid->addColumnSelect("event_id", "Events", "width='5%' ", $arr_events);
        //$clsDataGrid->addColumnText("order_no", "STT", "width='5%' ");
        $clsDataGrid->addColumnDate("created_at", "Ngày đăng", "width='5%'  nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%'  nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($stores, $paginate, $stores->total());

        return view('backend.store.index', compact('stores', 'filter', 'options', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        if (!Gate::allows('store/edit')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Store::where('id', $key)->update($value);
        }
        return redirect()->route('backend_store')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Store $store)
    {
        if (!Gate::allows('store/' . ($store->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->selectedSubMenu('store');
        $store->af_website = strpos($store->af_website, 'https') !== false ? $store->af_website : 'https://' . $store->af_website;
        $option_categories = Category::makeListCategory(0, Category::CATEGORY_TYPE_STORE, $store->cat_id);
        $option_af_flag = Store::makeListAfFlag($store->af_flag);
        $option_af_net = Store::makeListAfFNet($store->af_net);
        $option_ads_status = Store::makeListAdsStatus($store->ads_status);
        $option_ads_user = User::makeListUser($store->ads_user_id, true);
        
        $offers = $store->exists ? Offer::where('store_id', $store->id)->orderBy('priority', 'desc')->orderBy('id', 'desc')->get() : collect([]);
        
        return view('backend.store.create', compact(
                'store',
                'option_categories',
                'option_af_flag',
                'option_af_net',
                'option_ads_user',
                'option_ads_status',
                'offers',
            )
        );
    }

    public function save(Store $store, Request $request)
    {
        if (!Gate::allows('store/' . ($store->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|alpha_dash|unique:stores,slug,' . $store->id,
            'price' => 'integer',
        ]);

        $description = $request->get('description');
        $meta_title = $request->get('meta_title');
        $meta_keywords = $request->get('meta_keywords');
        $meta_description = $request->get('meta_description');
        $max_offer = $request->get('max_offer');
        $language = App::getLocale();
        $name = $request->get('name');
        $slug = $request->get('slug');

        $store->name = $name;
        $store->slug = $slug ?: Str::slug($name);
        $store->description = $description ?: Setting::getRandomShortDescription($name);
        $store->about_store = $request->get('about_store');
        $store->how_to_apply = $request->get('how_to_apply');
        $store->faqs = $request->get('faqs');
        $store->image = $request->get('image', '');
        $store->cat_id = $request->get('cat_id', 0);
        $store->ads_user_id = $request->get('ads_user_id', 0);
        $store->event_id = $request->get('event_id', 0);
        $store->af_visit = $request->get('af_visit', 0);
        $store->commission_amount = $request->get('commission_amount', 0);
        $store->af_flag = $request->get('af_flag', 'approved');
        $store->af_net = $request->get('af_net', '');
        $store->af_website = $request->get('af_website', '');
        $store->af_portal = $request->get('af_portal', '');
        $store->ads_email = $request->get('ads_email', '');
        $store->ads_status = $request->get('ads_status', 'default');
        $store->af_account = $request->get('af_account', '');
        $store->note = $request->get('note', '');
        $store->status = intval($request->get('status'));
        $store->allow_search = intval($request->get('allow_search'));
        $store->max_offer = $max_offer;
        $store->meta_title = $meta_title ?: Setting::generateMetaSEO($name, $max_offer);
        $store->meta_title = $meta_title ?: Setting::generateMetaSEO($name, $max_offer);
        $store->meta_keywords = $meta_keywords ?: Setting::generateMetaSEO($name, $max_offer, 'meta_keywords');
        $store->meta_description = $meta_description ?: Setting::generateMetaSEO($name, $max_offer, 'meta_description');

        if (!$store->exists) {
            $store->language = $language;
            $store->user_id = auth()->id();
        } elseif (!$store->user_id) {
            $store->user_id = auth()->id();
        }

        $store->save();

        if ($store->exists) {
            $offerIds = $request->get('offer_id', []);
            $offerNames = $request->get('offer_name', []);
            $offerCodes = $request->get('offer_code', []);
            $offerValues = $request->get('offer_value', []);
            $offerUrls = $request->get('offer_url', []);
            $offerStatuses = $request->get('offer_status', []);
            $offerVerifieds = $request->get('offer_verified', []);
            $offerOrders = $request->get('offer_order', []);

            $existingIds = Offer::where('store_id', $store->id)->pluck('id')->toArray();
            $submittedIds = array_filter($offerIds);

            foreach ($existingIds as $existingId) {
                if (!in_array($existingId, $submittedIds)) {
                    Offer::where('id', $existingId)->delete();
                }
            }

            foreach ($offerIds as $index => $offerId) {
                $orderValue = $offerOrders[$index] ?? $index;
                $offerData = [
                    'store_id' => $store->id,
                    'name' => $offerNames[$index] ?? '',
                    'code' => $offerCodes[$index] ?? '',
                    'offer' => $offerValues[$index] ?? '',
                    'url' => $offerUrls[$index] ?? '',
                    'status' => isset($offerStatuses[$index]) ? 1 : 0,
                    'verified' => isset($offerVerifieds[$index]) ? 1 : 0,
                    'priority' => count($offerIds) - $orderValue,
                    'language' => $language,
                    'user_id' => auth()->id(),
                ];

                if (!empty($offerId) && in_array($offerId, $existingIds)) {
                    Offer::where('id', $offerId)->update($offerData);
                } elseif (!empty(trim($offerNames[$index] ?? ''))) {
                    Offer::create($offerData);
                }
            }
        }

        return redirect()->route('backend_store_edit', $store)->with('success', 'Cập nhật thành công');
    }

    public function clone(Store $store)

    {
        if (!Gate::allows('store/clone')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        if ($store->id) {
            $store_new = $store->replicate();
            $store_new->name = $store->name . " copy";
            if ($store_new->save()) {
                return back()->with('success', 'Sao chép thành công');
            }
        }
        return back()->with('error', 'Sao chép không thành công');
    }

    public function delete(Request $request, $id)
    {
        if (!Gate::allows('store/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->store->destroy($id);
        return redirect()->to(route('backend_store'))->with('success', 'Xóa thành công');
    }

    public function bulkDelete(Request $request)
    {
        if (!Gate::allows('store/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate(['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->store->destroy($ids);
        return $this->responseJsonOk();
    }

    public function restore(Request $request, $id)
    {
        if (!Gate::allows('store/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $store = Store::withTrashed()->findOrFail($id);
        $store->restore();
        return redirect()->route('backend_store')->with('success', 'Khôi phục store thành công');
    }

    public function forceDelete(Request $request, $id)
    {
        if (!Gate::allows('store/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $store = Store::withTrashed()->findOrFail($id);
        $store->forceDelete();
        return redirect()->route('backend_store', 'status=2')->with('success', 'Xóa store thành công');
    }

    public function importPreview(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'File không hợp lệ',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        try {
            $preview = new StoreOfferPreview();
            Excel::import($preview, $request->file('file'));

            return response()->json([
                'success' => true,
                'data' => [
                    'store_count' => $preview->storeCount,
                    'offer_count' => $preview->offerCount,
                    'stores' => array_slice($preview->stores, 0, 10),
                    'total_stores' => count($preview->stores),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể đọc file: ' . $e->getMessage()
            ], 422);
        }
    }

    public function import(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls|max:5120', // Tối đa 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'File không hợp lệ',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        DB::beginTransaction();
        try {
            Excel::import(new StoreOfferImport, $request->file('file'));

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Import dữ liệu thành công!',
            ]);

        } catch (ValidationException $e) {
            // Lỗi Validation trong file Excel
            // Hủy toàn bộ thao tác insert trước đó
            DB::rollBack();

            // Lấy danh sách lỗi từ thư viện Excel
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                // Format: "Dòng 3: Tên Store không được để trống"
                $errorMessages[] = 'Dòng ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }

            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu trong file Excel không hợp lệ.',
                'errors' => $errorMessages
            ], 422);

        } catch (\Exception $e) {
            // Lỗi hệ thống khác (Code lỗi, DB lỗi...)
            DB::rollBack();

            \Log::error($e); // Ghi log để dev kiểm tra

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        // Validate IDs đầu vào
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:stores,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Dữ liệu chọn không hợp lệ.'], 422);
        }

        $fileName = 'stores_offers_' . now()->format('Ymd_His') . '.xlsx';

        // Trả về file trực tiếp

        return Excel::download(new StoreOfferExport($request->ids), $fileName);
    }
}


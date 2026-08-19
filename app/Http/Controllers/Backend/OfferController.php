<?php

namespace App\Http\Controllers\Backend;

use App\Libs\DataGrid;
use App\Libs\Util;
use App\Models\Setting;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class OfferController extends Controller
{
    private Offer $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
        $this->selectedMainMenu = 'offer';

        parent::__construct();

        if (!Gate::allows('offer')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $language = App::getLocale();
        $this->selectedSubMenu('offer');
        $category = new Category();
        $category->getParentArray();
        $user = auth()->user();

        $paginate = 15;

        $filter['name'] = $request->get('name', '');
        $filter['status'] = $request->get('status', -1);
        $filter['store_id'] = $request->get('store_id', 0);
        $query = Offer::with('store')
            ->where('language', $language)
            ->orderBy('id', 'desc');

        if (!$user->isSuperAdmin()) {
            $query->where(function ($q) use ($user, $filter) {
                $q->where('user_id', $user->id)
                    ->orWhere('user_id', 0);
            });
        }

        if ($filter['name'] !== '') {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter['name'] . '%')
                    ->orWhere('code', 'like', '%' . $filter['name'] . '%');
            });
        }

        if ($filter['status'] > -1) {
            $query->where('status', $filter['status']);
        }

        if ($filter['store_id'] > 0) {
            $query->where('store_id', $filter['store_id']);
        }

        $offers = $query->paginate($paginate)->appends(['status' => $filter['status'], 'name' => $filter['name']]);
        $options['stores'] = Store::makeListStore($filter['store_id']);
        $options['status'] = Util::makeHTMLOptions(Offer::STATUS_ARRAY, $filter['status']);

        $route_name = 'backend_offer_edit';
        $option_column_button = Offer::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='15%' nowrap");
        $clsDataGrid->addColumnLabel("url", "URL Ref", "width='15%' nowrap");
        $clsDataGrid->addColumnLabel("code", "Code", "width='15%' nowrap");
        $clsDataGrid->addColumnSelect("status", "Duyệt", "width='5%' ", ["Không", "Có"]);
        //$clsDataGrid->addColumnSelect("verified", "Verified", "width='5%' ", ["Không", "Có"]);
        $clsDataGrid->addColumnText("priority", "STT", "width='5%' ");
        $clsDataGrid->addColumnDate("created_at", "Ngày đăng", "width='5%'  nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%'  nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($offers, $paginate, $offers->total());

        return view('backend.offer.index', compact('offers', 'filter', 'options', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        if (!Gate::allows('offer/edit')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $update = $request->get('update', []);
        $query_string = [
            'name' => $request->get('name'),
            'store_id' => $request->get('store_id'),
            'status' => $request->get('status'),
        ];
        foreach ($update as $key => $value) {
            Offer::where('id', $key)->update($value);
        }
        return redirect()->route('backend_offer', $query_string)->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Offer $offer)
    {
        if (!Gate::allows('offer/' . ($offer->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
        $this->selectedSubMenu('offer');
        $option_stores = Store::makeListStore($offer->store_id);
        return view('backend.offer.create', compact('offer', 'option_stores'));
    }

    public function save(Offer $offer, Request $request)
    {
        if (!Gate::allows('offer/' . ($offer->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate([
            'name' => 'required|string',
            'offer' => 'required|string',
            'url' => 'required|string',
        ]);

        $description = $request->get('description', '');
        $store_id = $request->get('store_id', 0);
        $store_name = '';

        if ($store_id) {
            $store = Store::find($store_id);
            if ($store) {
                $store_name = $store->name;
            }

        }
        $language = App::getLocale();
        $offer->name = $request->get('name');
        $offer->code = $request->get('code');
        $offer->offer = $request->get('offer');
        $offer->url = $request->get('url', '');
        $offer->description = $description ?: Setting::getRandomShortDescription($store_name);
        $offer->image = $request->get('image', '');
        $offer->store_id = $request->get('store_id', 0);
        $offer->status = intval($request->get('status'));
        $offer->verified = intval($request->get('verified'));

        if (!$offer->exists) {
            $offer->language = $language;
            $offer->user_id = auth()->id();
        }
        $offer->save();

        return redirect()->route('backend_offer_edit', $offer)->with('success', 'Cập nhật thông tin thành công');
    }

    public function delete(Request $request, $id)
    {
        if (!Gate::allows('offer/clone')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->offer->destroy($id);
        return redirect()->route('backend_offer')->with('success', 'Delete success');
    }

    public function clone(Offer $offer)
    {
        $this->authorize('clone', $offer);

        if ($offer->id) {
            $offer_new = $offer->replicate();
            $offer_new->name = $offer->name . ' copy';
            $offer_new->description = '';
            if ($offer_new->save()) {
                return redirect()->route('backend_offer_edit', ['offer' => $offer_new])->with('success', 'Sao chép thành công');
            }
        }
        return back()->with('error', 'Sao chép không thành công');
    }

    public function bulkDelete(Request $request)
    {
        if (!Gate::allows('offer/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate(['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->offer->destroy($ids);
        return $this->responseJsonOk();
    }
}


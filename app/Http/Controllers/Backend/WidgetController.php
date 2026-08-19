<?php

namespace App\Http\Controllers\Backend;

use App\Libs\DataGrid;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class WidgetController extends Controller
{
    private Widget $widget;
    public array $positions = [
        'HOME_BANNER' => 'Home banner',
        'HOME_SIDEBAR' => 'Home sidebar',
    ];

    public function __construct(Widget $widget)
    {
        $this->widget = $widget;
        $this->selectedMainMenu = 'widget';

        parent::__construct();

        if (!Gate::allows('widget')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $language = App::getLocale();
        $this->selectedSubMenu('widget');

        $filter['position'] = $request->get('position', 'all');
        $query = $this->widget->where('language', $language)->orderBy('position');
        if ($filter['position'] !== 'all') {
            $query->where('position', $filter['position']);
        }
        $widgets = $query->paginate(20);
        $option_positions = Util::makeHTMLOptions($this->positions, $filter['position'], 0, 0, 0);

        $paginate = 20;
        $route_name = 'backend_widget_edit';
        $option_column_button = Widget::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Tiêu đề", "width='20%' nowrap");
        $clsDataGrid->addColumnImage("image", "Image", "", "width='10%' nowrap");
        $clsDataGrid->addColumnSelect("status", "Hiển thị", "width='5%'", ["Không", "Có"]);
        $clsDataGrid->addColumnText("priority", "STT", "width='5%'");
        $clsDataGrid->addColumnDate("created_at", "Ngày tạo", "width='5%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($widgets, $paginate, $widgets->total());

        return view('backend.widget.index', compact('widgets', 'option_positions', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        if (!Gate::allows('widget/edit')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Widget::where('id', $key)->update($value);
        }
        return redirect()->route('backend_widget')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Widget $widget)
    {
        if (!Gate::allows('widget/' . ($widget->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $options['positions'] = Util::makeHTMLOptions($this->positions, $widget->position, 0, 0, 0);
        return view('backend.widget.create', compact('widget', 'options'));
    }

    public function save(Widget $widget, Request $request)
    {
        if (!Gate::allows('widget/' . ($widget->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate([
            'name' => 'required|string',
        ]);

        $language = App::getLocale();
        $widget->name = $request->get('name');
        $widget->description = $request->get('description');
        $widget->image = $request->get('image');
        $widget->priority = $request->get('priority');
        $widget->link = $request->get('link');
        $position = $request->get('position');
        $widget->position = $position;
        $widget->status = (int)$request->get('status', 0);
        if (!$widget->exists) {
            $widget->language = $language;
        }

        $widget->save();

        $url_back = route('backend_widget') . '?position=' . $position;
        return redirect()->to(url($url_back))->with('success', 'Cập nhật thông tin thành công');
    }

    public function clone(Widget $widget)
    {
        if (!Gate::allows('widget/clone')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $widget_id = data_get($widget, 'id', 0);
        $widget = Widget::find($widget_id);
        if ($widget) {
            $new_widget = $widget->replicate();
            $new_widget->name = $widget->name . " copy";
            if ($new_widget->save()) {
                return back()->with('success', 'Sao chép thành công');
            }
        }
        return back()->with('error', 'Sao chép không thành công');
    }

    public function delete(Request $request, $id)
    {
        if (!Gate::allows('widget/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->widget->destroy($id);
        return redirect()->route('backend_widget')->with('success', 'Xóa thành công');
    }

    public function bulkDelete(Request $request)
    {
        if (!Gate::allows('widget/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate(['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->widget->destroy($ids);
        return $this->responseJsonOk();
    }
}


<?php

namespace App\Http\Controllers\Backend;

use App\Libs\DataGrid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Support\Facades\Gate;

class FeedbackController extends Controller
{
    private Feedback $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
        $this->selectedMainMenu = 'feedback';

        parent::__construct();

        if (!Gate::allows('feedback')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $filter['keyword'] = $request->get('keyword');
        $filter['type'] = $request->get('type', -1);

        $query = Feedback::query();
        if ($filter['keyword']) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter['keyword'] . '%')
                    ->orWhere('email', 'like', '%' . $filter['keyword'] . '%')
                    ->orWhere('phone', 'like', '%' . $filter['keyword'] . '%');
            });
        }

        if ($filter['type'] > -1) {
            $query->where('type', $filter['type']);
        }

        $feedbacks = $query->orderBy('id', 'desc')->paginate(20);

        $option_column_button = Feedback::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit('backend_feedback_edit');
        $clsDataGrid->addColumnLabel("name", "Name", "width='20%' nowrap");
        $clsDataGrid->addColumnLabel("phone", "Phone", "width='20%' nowrap");
        $clsDataGrid->addColumnLabel("email", "Email", "width='20%' nowrap");
        $clsDataGrid->addColumnDate("created_at", "Ngày liên hệ", "width='5%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($feedbacks);

        return view('backend.feedback.index',
            compact(
                'feedbacks',
                'filter',
                'dataGrid'
            )
        );
    }

    public function edit(Feedback $feedback)
    {
        if (!Gate::allows('feedback/' . ($feedback->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        if ($feedback->exists) {
            $feedback->update(['status' => 1]);
        }
        return view('backend.feedback.create', compact('feedback'));
    }

    public function save(Feedback $feedback, Request $request)
    {
        if (!Gate::allows('feedback/' . ($feedback->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
        ]);

        $feedback->name = $request->get('name');
        $feedback->kananame = $request->get('kananame');
        $feedback->email = $request->get('email');
        $feedback->phone = $request->get('phone');
        $feedback->content = $request->get('content');
        $feedback->save();
        return redirect()->route('backend_feedback');
    }

    public function bulkDelete(Request $request)
    {
        if (!Gate::allows('feedback/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate(['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->feedback->destroy($ids);
        return $this->responseJsonOk();
    }
}


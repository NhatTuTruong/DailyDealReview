<?php

namespace App\Http\Controllers\Backend;

use App\Libs\DataGrid;
use App\Libs\Util;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PostController extends Controller
{
    private Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
        $this->selectedMainMenu = 'post';

        parent::__construct();

        if (!Gate::allows('post')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $language = App::getLocale();
        $this->selectedSubMenu('post');

        $filter['name'] = $request->get('name', '');
        $filter['cat_id'] = $request->get('cat_id', 0);
        $filter['status'] = $request->get('status', -1);
        $query = $this->post->with(['categories', 'user'])
            ->where('language', $language)
            ->orderBy('id', 'desc');
        if ($filter['name'] !== '') {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }
        if ($filter['cat_id'] > 0) {
            $category = Category::find($filter['cat_id']);
            if ($category) {
                $category_ids = $category->allChildrenIds()->toArray();
                $query->whereHas('categories', function ($q) use ($category_ids) {
                    $q->whereIn('categories.id', $category_ids);
                });
            }
        }
        if ($filter['status'] > -1) {
            if ($filter['status'] == 2) {
                $query->onlyTrashed();
            } else {
                $query->where('status', $filter['status']);
            }
        }

        $posts = $query->paginate(20);
        $options['categories'] = Category::makeListCategory(0, Category::CATEGORY_TYPE_POST, $filter['cat_id']);
        $options['status'] = Util::makeHTMLOptions(Post::STATUS_ARRAY, $filter['status']);

        $paginate = 20;
        $route_name = 'backend_post_edit';
        $option_column_button = Post::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='20%' nowrap");
        $clsDataGrid->addColumnImage("image", "Image", "", "width='10%' nowrap");
        $clsDataGrid->addColumnLabel("category_name", "Danh mục", "width='20%' nowrap");
        $clsDataGrid->addColumnSelect("status", "Hiển thị", "width='15%'", ["Không", "Có"]);
        $clsDataGrid->addColumnText("priority", "STT", "width='10%'");
        $clsDataGrid->addColumnDate("created_at", "Ngày tạo", "width='15%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($posts, $paginate, $posts->total());

        return view('backend.post.index', compact('posts', 'filter', 'options', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        if (!Gate::allows('post/edit')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Post::where('id', $key)->update($value);
        }
        return redirect()->route('backend_post')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Post $post)
    {
        if (!Gate::allows('post/' . ($post->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $cat_ids = $post->categories()->pluck('categories.id')->toArray();
        $option_categories = Category::makeListCategory(0, Category::CATEGORY_TYPE_POST, $cat_ids);
        $option_stores = Store::makeListStore($post->store_id, true);
        return view('backend.post.create', compact('post', 'option_categories', 'option_stores'));
    }

    public function save(Post $post, Request $request)
    {
        if (!Gate::allows('post/' . ($post->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate([
            'name' => 'required|string',
            'slug' => 'nullable|alpha_dash|',//unique:posts,slug,' . $post->id,
            'description' => 'required|string',
            'content' => 'required|string',
            'cat_ids' => 'array',
        ]);

        $language = App::getLocale();
        $name = strip_tags($request->get('name'));
        $slug = strip_tags($request->get('slug'));
        $cat_ids = $request->get('cat_ids', []);
        $post->name = $name;
        $post->slug = $slug ?: Str::slug($name);
        $post->description = $request->get('description');
        $post->content = $request->get('content');
        $post->image = strip_tags($request->get('image'));
        $post->cat_id = intval($request->get('cat_id'));
        $post->store_id = intval($request->get('store_id'));
        $post->status = intval($request->get('status'));
        $post->is_hot = intval($request->get('is_hot'));

        $post->meta_title = $request->get('meta_title');
        $post->meta_keywords = $request->get('meta_keywords');
        $post->meta_description = $request->get('meta_description');

        $created_at = $request->get('created_at');
        $created_at_timestamp = $created_at ? strtotime($created_at) : now();

        $post->created_at = Carbon::createFromTimestamp($created_at_timestamp);

        if (!$post->exists) {
            $post->language = $language;
        }

        try {
            $post->save();
            if (!empty($cat_ids)) {
                $post->categories()->sync($cat_ids);
            }
        } catch (\Exception $ex) {
            return back()->withInput()->withErrors(['slug' => $ex->getMessage()]);
        }

        return redirect()->route('backend_post_edit', $post)->with('success', 'Cập nhật thành công');
    }

    public function clone(Post $post)
    {
        if (!Gate::allows('post/clone')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $new_id = data_get($post, 'id', 0);
        $post = Post::find($new_id);
        if ($post) {
            $new_post = $post->replicate();
            $new_post->name = $post->name . ' copy';
            $new_post->slug = $post->slug . '-' . strtolower(Str::random(5));
            if ($new_post->save()) {
                return back()->with('success', 'Sao chép thành công');
            }
        }
        return back()->with('error', 'Sao chép không thành công');
    }

    public function delete(Request $request, $id)
    {
        if (!Gate::allows('post/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->post->destroy($id);
        return redirect()->to(route('backend_post'))->with('success', 'Xóa thành công');
    }

    public function bulkDelete(Request $request)
    {
        if (!Gate::allows('post/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate(['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->post->destroy($ids);
        return $this->responseJsonOk();
    }

    public function restore(Request $request, $id)
    {
        if (!Gate::allows('post/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $post = Post::withTrashed()->findOrFail($id);
        $post->restore();
        return redirect()->route('backend_post')->with('success', 'Khôi phục bài viết thành công');
    }

    public function forceDelete(Request $request, $id)
    {
        if (!Gate::allows('post/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $post = Post::withTrashed()->findOrFail($id);
        $post->forceDelete();
        return redirect()->route('backend_post', 'status=2')->with('success', 'Xóa bài viết thành công');
    }
}


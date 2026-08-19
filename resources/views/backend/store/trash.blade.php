<table class="table table-hover table-grid-admin">
    <thead>
    <tr>
        <th width="1% " class="grid_header">ID</th>
        <th width="20%" nowrap="" class="grid_header">Name</th>
        <th width="10%" class="grid_header">Danh mục</th>
        <th width="10%" class="grid_header">Image</th>
        <th width="5%" class="grid_header">Ngày tạo</th>
        <th width="5%" align="center" class="grid_header">Ngày xóa</th>
        <th width="5%" align="center" nowrap="" class="grid_header1">&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    @foreach($posts as $post)
        <tr id="grid_tr_0">
            <td style="color:#666666">{{ $post->id }}</td>
            <td>{{ $post->name  }}</td>
            <td><b>{{ data_get($post, 'category.name', 'No category') }}</b></td>
            <td><img src="{{ $post->image }}" style="width: 60px;"></td>
            <td>{{ $post->created_at }}</td>
            <td>{{ $post->deleted_at }}</td>
            <td>
                <a class="btn btn-success btn-sm" href="{{ route('backend_post_restore', $post->id) }}"
                   title="Khôi phục">
                    <i class="fas fa-trash-restore"></i>
                </a>
                <a class="btn btn-danger btn-sm" href="{{ route('backend_post_force_delete', $post->id) }}"
                   title="Xóa vĩnh viễn">
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

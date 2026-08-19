@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $group->exists ? 'Sửa group' : 'Thêm mới group' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_user') }}">User</a></li>
    <li class="breadcrumb-item"><a href="{{ route('backend_group') }}">Group</a></li>
    <li class="breadcrumb-item active">{{ $group->exists ? 'Sửa group' : 'Thêm mới group' }}</li>
@endsection

@section('content')

    <script src="{{ asset('backend_assets/js/globals.js') }}"></script>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="float-right mb-3">
                        @can('group/' . ($group->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($group->exists)
                            @can('group/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_group_create') }}"/>
                            @endcan
                            @can('group/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_group_delete', $group->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_group_save', $group) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body" style="max-width: 700px; margin: auto">

                        <div class="form-group row">
                            <label class="col-sm-12 col-form-label">
                                Tên group
                            </label>
                            <div class="col-sm-12">
                                <input type="text" name="name" value="{{ old('name') ?: $group->name }}" id="name"
                                       class="form-control" placeholder="Tên group">
                                @if($errors->has('name'))
                                    <div class="text-danger">{{ $errors->first('name') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="frm-grid">
                            <div>
                                <label>Permission Grant</label>
                                @if($errors->has('permission'))
                                    <div class="text-danger">{{ $errors->first('permission') }}</div>
                                @endif
                                <div class="backend-perm-tree js-permission_tree">
                                    <ul>
                                        @foreach($permission_configs as $module_key => $permission_data)
                                            <li>
                                                <input type="checkbox" name="permission[{{ $module_key }}]"
                                                       id="permission_{{ $module_key }}" value="1"
                                                       @if(in_array($module_key , ($group->permission_data ?? [])))
                                                           checked="checked"
                                                        @endif
                                                >
                                                <label class="label-inline" for="permission_{{ $module_key }}">
                                                    {{ $permission_data['label'] }}
                                                </label>
                                                @if(!empty($permission_data['items']))
                                                    <ul>
                                                        @foreach($permission_data['items'] as $per_lv1_key => $per_lv1_value)
                                                            <li>
                                                                <input type="checkbox"
                                                                       name="permission[{{ $module_key }}][{{ $per_lv1_key }}]"
                                                                       id="permission_{{ $module_key }}_{{ $per_lv1_key }}"
                                                                       value="1"
                                                                       @if(in_array($module_key . '/' . $per_lv1_key , ($group->permission_data ?? [])))
                                                                           checked="checked"
                                                                        @endif
                                                                >
                                                                <label class="label-inline"
                                                                       for="permission_{{ $module_key }}_{{ $per_lv1_key }}">
                                                                    {{ $per_lv1_value }}
                                                                </label>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection

@section('bottom')
    <style>
        .backend-perm-tree {
            padding: 0 0 20px 0;
        }

        .backend-perm-tree ul {
            list-style-type: none;
            padding: 0;
            margin: 10px 0 0 0;
        }

        .backend-perm-tree li {
            padding: 5px 0 5px 30px;
            position: relative;
        }

        .backend-perm-tree li:before {
            content: "";
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            width: 1px;
            height: 100%;
            background: #ddd;
        }

        .backend-perm-tree li:first-child:before {
            height: calc(100% + 10px);
            top: -10px;
        }

        .backend-perm-tree li:last-child:before {
            height: 13px;
        }

        .backend-perm-tree li:last-child:first-child:before {
            height: 22px;
        }

        .backend-perm-tree li:after {
            content: "";
            display: block;
            position: absolute;
            top: 12px;
            left: 0;
            width: 28px;
            height: 1px;
            background: #ddd;
        }

        .backend-perm-tree li > ul {
            display: none;
        }

        .backend-perm-tree li > input {
            cursor: pointer;
            vertical-align: -2px;
            position: relative;
            z-index: 2;
            margin-right: 3px;
            margin-left: 0;
        }

        .backend-perm-tree li > label {
            font-style: italic;
            color: #da0000;
        }

        .backend-perm-tree li > input:checked + label {
            font-weight: bold;
            font-style: normal;
            color: #007bda;
            margin-bottom: 0;
        }

        .backend-perm-tree li > input:checked + label + ul {
            display: block;
        }
    </style>
    <script>
        $(document).ready(function () {
            $('input[type="checkbox"]').change(function () {
                if (!$(this).is(':checked')) {
                    $(this).closest('li').find('ul input[type="checkbox"]').prop('checked', false);
                }
            });
        });

    </script>
@endsection

@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    Trang công việc
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Trang công việc</li>
@endsection

@section('content')
    <script src="{{ asset('js/ckfinder/ckfinder.js') }}"></script>
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('backend_assets/js/globals.js') }}"></script>
    <script>CKFinder.config({connectorPath: '/ckfinder/connector'});</script>


    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="float-right mb-3 mt-3">
                        <x-forms.button-save/>
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_landing_page_save', 'job_data') }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-0">[1] Section banner</h5>
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <input name="job_data[banner][heading_1]" placeholder="Heading 1"
                                           class="form-control mb-3"
                                           value="{{ $job_data['banner']['heading_1'] ?? '' }}"/>
                                    <input name="job_data[banner][heading_2]" placeholder="Heading 2"
                                           class="form-control mb-3"
                                           value="{{ $job_data['banner']['heading_2'] ?? '' }}"/>
                                    <x-forms.textarea name="job_data[banner][content]"
                                                      value="{{ $job_data['banner']['content'] ?? '' }}"
                                                      label="Nội dung" editor="true"
                                                      class_label="d-none" class_input="col-sm-12"/>
                                </div>
                                <div class="col-md-4">
                                    <x-forms.upload name="job_data[banner][image]"
                                                    value="{{ $job_data['banner']['image'] ?? '' }}" label="Image"
                                                    type="image" class_label="d-none"
                                                    class_input="col-sm-12 section_banner_image"/>
                                </div>
                            </div>
                            <hr class="mt-1">
                            <h5 class="font-weight-bold mb-0">[2] Section intro</h5>
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <input name="job_data[intro][heading]" placeholder="Heading"
                                           class="form-control mb-3"
                                           value="{{ $job_data['intro']['heading'] ?? '' }}"/>
                                </div>
                                <div class="col-md-4">
                                    <x-forms.upload name="job_data[intro][image]"
                                                    value="{{ $job_data['intro']['image'] ?? '' }}" label="Image"
                                                    type="image" class_label="d-none"
                                                    class_input="col-sm-12 section_intro_image"/>
                                </div>
                            </div>
                            <hr class="mt-1">
                            <h5 class="font-weight-bold mb-0">[3] Section các công việc</h5>
                            <div class="row mt-3">
                                <div class="row col-12">
                                    <div class="col-md-4">
                                        <label class="form-label">Tên công việc</label>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Mô tả công việc</label>
                                    </div>
                                </div>
                                @foreach($job_data['jobs'] ?? [] as $index =>$jobs)
                                    <div class="item_row row col-12">
                                        <div class="col-md-4">
                                            <input name="job_data[jobs][{{ $index }}][title]"
                                                   placeholder="Tên công việc"
                                                   class="form-control mb-3"
                                                   value="{{ $job_data['jobs'][$index]['title'] ?? '' }}"/>
                                        </div>
                                        <div class="col-md-7">
                                            <textarea name="job_data[jobs][{{ $index }}][content]"
                                                      placeholder="Mô tả công việc" rows="3"
                                                      class="form-control mb-3">{{ $job_data['jobs'][$index]['content'] ?? '' }}</textarea>
                                        </div>
                                        <div class="col-md-1">
                                            <button class="btn btn-danger btn-delete">Xóa</button>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-add-new" type="button">Thêm mới</button>
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

    <script>
        $(document).ready(function () {
            // Function to add a new item
            $(".btn-add-new").click(function () {
                let type = $(this).data("type"); // Lấy type từ data-type
                let index = $(".item_row").length; // Đếm số lượng item hiện tại để đặt index

                // Tạo HTML mới
                let newItem = `
            <div class="item_row row col-12">
                <div class="col-md-4">
                    <input name="job_data[jobs][${index}][title]" placeholder="Tên công việc" class="form-control mb-3" />
                </div>
                <div class="col-md-7">
                    <textarea name="job_data[jobs][${index}][content]" placeholder="Mô tả công việc" rows="3" class="form-control mb-3"></textarea>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-danger btn-delete">Xóa</button>
                </div>
            </div>
        `;

                // Append vào form
                $(this).closest(".col-md-12").before(newItem);
            });

            // Function to delete an item
            $(document).on("click", ".btn-delete", function () {
                $(this).closest(".item_row").remove(); // Xóa item chứa button
                updateIndexes(); // Cập nhật lại index để đảm bảo dữ liệu gửi lên đúng định dạng array
            });

            // Function to update name attributes after deletion
            function updateIndexes() {
                $(".item_row").each(function (index) {
                    $(this).find("input").eq(0).attr("name", `job_data[jobs][${index}][title]`);
                    $(this).find("textarea").eq(1).attr("name", `job_data[jobs][${index}][content]`);
                });
            }
        });

    </script>
@endsection

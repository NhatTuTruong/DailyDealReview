@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    Trang chủ
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Trang chủ</li>
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
                <form action="{{ route('backend_landing_page_save', 'home_data') }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-0">[1] Section banner</h5>
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <input name="home_data[banner][heading_1]" placeholder="Heading 1"
                                           class="form-control mb-3"
                                           value="{{ $home_data['banner']['heading_1'] ?? '' }}"/>
                                    <input name="home_data[banner][heading_2]" placeholder="Heading 2"
                                           class="form-control mb-3"
                                           value="{{ $home_data['banner']['heading_2'] ?? '' }}"/>
                                    <x-forms.textarea name="home_data[banner][content]"
                                                      value="{{ $home_data['banner']['content'] ?? '' }}"
                                                      label="Nội dung" editor="true"
                                                      class_label="d-none" class_input="col-sm-12"/>
                                </div>
                                <div class="col-md-4">
                                    <x-forms.upload name="home_data[banner][image]"
                                                    value="{{ $home_data['banner']['image'] ?? '' }}" label="Image"
                                                    type="image" class_label="d-none"
                                                    class_input="col-sm-12 section_banner_image"/>
                                </div>
                            </div>
                            <hr class="mt-1">
                            <h5 class="font-weight-bold mb-0">[2] Section intro</h5>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <input name="home_data[intro][heading]" placeholder="Heading 1"
                                           class="form-control mb-3"
                                           value="{{ $home_data['intro']['heading'] ?? '' }}"/>
                                    <x-forms.textarea name="home_data[intro][content]"
                                                      value="{{ $home_data['intro']['content'] ?? '' }}"
                                                      label="Nội dung" editor="true"
                                                      class_label="d-none" class_input="col-sm-12"/>
                                </div>
                            </div>
                            <hr class="mt-1">
                            <h5 class="font-weight-bold mb-0">[3] Section lịch sử công ty</h5>
                            <div class="row mt-3">
                                <div class="row col-12">
                                    <div class="col-md-4">
                                        <label class="form-label">Ngày tháng</label>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Sự kiện</label>
                                    </div>
                                </div>
                                @foreach($home_data['history'] ?? [] as $index =>$history)
                                    <div class="item_row row col-12">
                                        <div class="col-md-4">
                                            <input name="home_data[history][{{ $index }}][col_1]"
                                                   placeholder="Ngày tháng"
                                                   class="form-control mb-3"
                                                   value="{{ $home_data['history'][$index]['col_1'] ?? '' }}"/>
                                        </div>
                                        <div class="col-md-7">
                                            <input name="home_data[history][{{ $index }}][col_2]" placeholder="Event"
                                                   class="form-control mb-3"
                                                   value="{{ $home_data['history'][$index]['col_2'] ?? '' }}"/>
                                        </div>
                                        <div class="col-md-1">
                                            <button class="btn btn-danger btn-delete">Xóa</button>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-add-new" type="button"
                                            data-type="history">Thêm mới
                                    </button>
                                </div>
                            </div>
                            <hr class="mt-3">
                            <h5 class="font-weight-bold mb-0">[4] Section đối tác</h5>
                            <div class="row mt-3">
                                <div class="row col-12">
                                    <div class="col-md-4">
                                        <label class="form-label">Công ty</label>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Nhiệm vụ</label>
                                    </div>
                                </div>
                                @foreach($home_data['partner'] ?? [] as $index =>$partner)
                                    <div class="item_row row col-12">
                                        <div class="col-md-4">
                                            <input name="home_data[partner][{{ $index }}][col_1]"
                                                   placeholder="Ngày tháng"
                                                   class="form-control mb-3"
                                                   value="{{ $home_data['partner'][$index]['col_1'] ?? '' }}"/>
                                        </div>
                                        <div class="col-md-7">
                                            <input name="home_data[partner][{{ $index }}][col_2]" placeholder="Event"
                                                   class="form-control mb-3"
                                                   value="{{ $home_data['partner'][$index]['col_2'] ?? '' }}"/>
                                        </div>
                                        <div class="col-md-1">
                                            <button class="btn btn-danger btn-delete">Xóa</button>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-add-new" type="button"
                                            data-type="partner">Thêm mới
                                    </button>
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
        .section_intro_image .preview_image {
            max-width: 100% !important;
        }
    </style>

    <script>
        $(document).ready(function () {
            // Function to add a new item
            $(".btn-add-new").click(function () {
                let type = $(this).data("type"); // Lấy type từ data-type
                let index = $(".item_row").length; // Đếm số lượng item hiện tại để đặt index

                // Xác định name input và label dựa trên type
                let namePrefix = type === "history" ? "home_data[history]" : "home_data[partner]";
                let label1 = type === "history" ? "Ngày tháng" : "Tên công ty";
                let label2 = type === "history" ? "Event" : "Nhiệm vụ";

                // Tạo HTML mới
                let newItem = `
            <div class="item_row row col-12">
                <div class="col-md-4">
                    <input name="${namePrefix}[${index}][col_1]" placeholder="${label1}" class="form-control mb-3" />
                </div>
                <div class="col-md-7">
                    <input name="${namePrefix}[${index}][col_2]" placeholder="${label2}" class="form-control mb-3" />
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
                    let type = $(this).find("input").first().attr("name").includes("history") ? "history" : "partner";
                    $(this).find("input").eq(0).attr("name", `home_data[${type}][${index}][col_1]`);
                    $(this).find("input").eq(1).attr("name", `home_data[${type}][${index}][col_2]`);
                });
            }
        });

    </script>
@endsection

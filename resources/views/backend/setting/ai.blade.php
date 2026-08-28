@extends('backend.index')

@section('title')
    Cấu hình AI
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_setting_general') }}">Cài đặt</a></li>
    <li class="breadcrumb-item active">Cấu hình AI</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="float-right mb-3 mt-3">
                        <x-forms.button-save/>
                    </div>
                </div>
            </div>
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="ai-settings-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="gemini-tab" data-toggle="pill" href="#gemini" role="tab">Gemini</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="apify-tab" data-toggle="pill" href="#apify" role="tab">Apify</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="autopost-tab" data-toggle="pill" href="#autopost" role="tab">Auto Post</a>
                        </li>
                    </ul>
                </div>
                <form action="{{ route('backend_setting_save') }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">
                        <div class="tab-content" id="ai-settings-tabsContent">

                            {{-- =================== GEMINI TAB =================== --}}
                            <div class="tab-pane fade show active" id="gemini" role="tabpanel">

                                <div class="alert alert-info alert-dismissible">
                                    <h5><i class="icon fas fa-info"></i> Hướng dẫn</h5>
                                    <ul class="mb-0">
                                        <li><strong>API Keys:</strong> Mỗi key một dòng. Hệ thống tự xoay vòng key khi gặp lỗi.</li>
                                        <li><strong>Thứ tự Model ưu tiên:</strong> Mỗi model một dòng theo thứ tự mong muốn. Model đầu tiên được dùng trước; nếu lỗi sẽ tự chuyển sang model tiếp theo.</li>
                                    </ul>
                                </div>

                                {{-- Gemini API Keys --}}
                                <x-forms.textarea name="settings[gemini_api_keys]"
                                                 value="{{ $settings['gemini_api_keys'] ?? '' }}"
                                                 label="API Keys Gemini"
                                                 :rows="5"
                                                 help="Mỗi key một dòng. Ví dụ: AIzaSyAGeqYBwgYG76wMGAZpJOwcoLpQwcLFpbg"/>

                                {{-- Gemini Models Priority --}}
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Thứ tự ưu tiên Model</label>
                                    <div class="col-sm-9">
                                        <div class="alert alert-warning py-2 px-3 mb-2" style="font-size:0.875rem">
                                            <i class="fas fa-sort-numeric-down"></i>
                                            Model đầu tiên = ưu tiên cao nhất. Mỗi model một dòng theo thứ tự mong muốn.
                                        </div>
                                        <textarea name="settings[gemini_models]" id="settings_gemini_models"
                                                  rows="6" class="form-control"
                                        >{{ $settings['gemini_models'] ?? 'gemini-2.0-flash' }}</textarea>
                                        <div class="text-muted mt-2">
                                            Các model khả dụng:
                                            <div class="mt-1">
                                                @foreach(\App\Services\AI\ModelManager::AVAILABLE as $key => $label)
                                                    <span class="badge badge-info mr-1 mb-1" style="cursor:pointer"
                                                          onclick="addModel('{{ $key }}')">{{ $key }}</span>
                                                @endforeach
                                                <span class="text-muted ml-2" style="font-size:0.8rem">(click để thêm)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- =================== APIFY TAB =================== --}}
                            <div class="tab-pane fade" id="apify" role="tabpanel">

                                <div class="alert alert-info alert-dismissible">
                                    <h5><i class="icon fas fa-info"></i> Hướng dẫn</h5>
                                    <ul class="mb-0">
                                        <li>Mỗi key một dòng. Dùng để lấy ảnh từ website store làm ảnh đại diện và ảnh trong bài viết.</li>
                                        <li>Lấy key tại: <a href="https://console.apify.com/" target="_blank">https://console.apify.com/</a></li>
                                    </ul>
                                </div>

                                <x-forms.textarea name="settings[apify_api_keys]"
                                                 value="{{ $settings['apify_api_keys'] ?? '' }}"
                                                 label="API Keys Apify"
                                                 :rows="5"
                                                 help="Mỗi key một dòng. Ví dụ: apify_api_..."/>

                            </div>

                            {{-- =================== AUTO POST TAB =================== --}}
                            <div class="tab-pane fade" id="autopost" role="tabpanel">

                                <div class="alert alert-info alert-dismissible">
                                    <h5><i class="icon fas fa-info"></i> Hướng dẫn</h5>
                                    <ul class="mb-0">
                                        <li>Tự động tạo bài viết blog cho store theo thứ tự từ cũ tới mới.</li>
                                        <li>Mỗi lần chạy sẽ tạo <strong>1 bài viết</strong> cho store chưa có bài viết (hoặc store có bài viết lâu nhất).</li>
                                    </ul>
                                </div>

                                {{-- Toggle --}}
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Bật tự động đăng bài</label>
                                    <div class="col-sm-9">
                                        <input type="checkbox" name="settings[ai_auto_post_enabled]"
                                               value="1"
                                               class="switch"
                                               {{ ($settings['ai_auto_post_enabled'] ?? 0) == 1 ? 'checked' : '' }}/>
                                    </div>
                                </div>

                                {{-- Interval --}}
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Thời gian giữa mỗi bài (phút)</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="settings[ai_auto_post_interval]"
                                               class="form-control"
                                               value="{{ $settings['ai_auto_post_interval'] ?? 30 }}"
                                               min="1" max="1440" style="max-width:200px"/>
                                        <small class="form-text text-muted">Mỗi lần scheduler chạy, nếu đủ thời gian chờ sẽ tạo 1 bài. Đặt 0 để tạo liên tục mỗi lần scheduler chạy.</small>
                                    </div>
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
        function addModel(modelKey) {
            var ta = document.getElementById('settings_gemini_models');
            var current = ta.value.trim();
            var lines = current ? current.split('\n').map(function (l) { return l.trim(); }) : [];
            if (lines.indexOf(modelKey) === -1) {
                lines.push(modelKey);
                ta.value = lines.join('\n');
            }
        }
    </script>
@endsection

@extends('frontend.index')

@section('content')
    <div class="page__content">
        <section class="section section--page py-0">
            <img class="section__bg" src="{{ asset('images/banner.jpg') }}" alt="banner">
            <div class="container">
                <div class="section__inner">
                    <div class="section__body"></div>
                </div>
            </div>
        </section>
    </div>

    <div class="md-login modal fade show" tabindex="-1" style="display: block" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="md-login__logo">
                        <a href="{{ route('home_page') }}">
                            <img src="{{ asset('images/logo.png') }}" alt="logo"/>
                        </a>
                    </div>
                    <div class="md-login__title">Chào mừng</div>
                    <div class="md-login__desc">Bạn đang đăng nhập với tư cách thành viên</div>
                    <form class="md-login__form" action="{{ route('member_login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input class="form-control" type="text" name="user_name" value="{{ old('user_name') }}"
                                   required>
                            @if($errors->any())
                                <div class="text-danger">
                                    {{ $errors->first('user_name') }}
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <div class="input-group md-login__password">
                                <input class="form-control" type="password" name="password" required>
                                <button class="input-group-text js-toggle-show-password" type="button">
                                    <i class="fal fa-eye"></i>
                                    <i class="fal fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row g-3 justify-content-between">
                                <div class="col-auto">
                                    <label class="checkbox-styled">
                                        <input class="checkbox-styled__input" type="checkbox" name="remember"/>
                                        <span class="checkbox-styled__icon"></span>
                                        <span>Lưu mật khẩu</span>
                                    </label>
                                </div>
                                <div class="col-auto"><a class="md-login__link fw-700" href="#!">Quên mật khẩu?</a>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="md-login__submit button--secondary button" type="submit">Đăng nhập</button>
                        </div>
                    </form>
                    <div class="md-login__copyright">Copyright © 2024</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>

@endsection

@push('bottom')

@endpush

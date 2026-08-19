@extends('frontend.index')

@section('content')
    <div class="page__content">
        <!-- main content-->
        <section class="section section--page">
            <img class="section__bg" src="{{ asset('images/rule-bg.jpg') }}" alt="rule background">
            <div class="section__backdrop"></div>
            <div class="container">
                <div class="section__inner">
                    <div class="section__header">
                        <div class="section__title text-white mb-0">THÔNG TIN NGƯỜI DÙNG</div>
                    </div>
                    <div class="section__body">
                        <div class="section__content">
                            <div class="pf-page">
                                <div class="pf-page__nav">
                                    <div class="pf-page__nav-btn active" data-target=".js-profile-personal">Thông tin cá
                                        nhân
                                    </div>
                                    <div class="pf-page__nav-btn" data-target=".js-profile-password">Đổi mật khẩu</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-8 pf-page__tab js-profile-personal active">
                                        <section class="section-2">
                                            <h2 class="section-2__title">Thông tin cá nhân</h2>
                                            <div class="section-2__body">
                                                <div class="profile__table">
                                                    <table class="table table-borderless">
                                                        <tbody>
                                                        <tr>
                                                            <td>Họ và tên</td>
                                                            <td>{{ $member->name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Mã nhân viên</td>
                                                            <td>NV{{ $member->id }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Giới tính</td>
                                                            <td>Nam</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Số điện thoại</td>
                                                            <td>{{ $member->phone }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Ngày sinh</td>
                                                            <td>01/12/1999</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Email</td>
                                                            <td>{{ $member->email }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Chức danh</td>
                                                            <td>Nhân viên quản trị hệ thống</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Đơn vị</td>
                                                            <td>{{ $member->name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Địa chỉ</td>
                                                            <td>{{ $member->address }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-lg-4 pf-page__tab js-profile-password">
                                        <section class="section-2">
                                            <h2 class="section-2__title">Đổi mật khẩu</h2>
                                            <div class="section-2__body">
                                                <form action="#!">
                                                    <div class="mb-3">
                                                        <label class="form-label">Mật khẩu cũ</label>
                                                        <input class="form-control" type="password"
                                                               name="current_password">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Mật khẩu mới</label>
                                                        <input class="form-control" type="password" name="new_password">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nhập lại mật khẩu mới</label>
                                                        <input class="form-control" type="password"
                                                               name="new_password_confirmation">
                                                    </div>
                                                    <div class="ajax_response"></div>
                                                    <button class="button button--secondary button--block" type="button"
                                                            id="buttonChangePassword"
                                                            data-url="{{ route('member_change_password') }}">
                                                        Đổi mật khẩu
                                                    </button>
                                                </form>

                                                <form class="mt-3" action="{{ route('member_logout') }}"
                                                      method="POST">
                                                    @csrf
                                                    <button class="button button--red button--block" type="submit">
                                                        <i class="fal fa-fw fa-sign-out me-2"></i>
                                                        <span>Đăng xuất</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('frontend.footer_2')

                </div>
            </div>
        </section>
    </div>
@endsection

@push('bottom')

@endpush

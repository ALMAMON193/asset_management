@extends('web.backend.app')

@section('title', 'Dashboard')

@section('content')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">

      <div class="row">
        <div class="col-lg-8 mb-4 order-0">
          <div class="card">
            <div class="d-flex align-items-end row">
              <div class="col-sm-7">
                <div class="card-body">
                  <h5 class="card-title text-primary">Hello, {{ Auth::user()->first_name ?? '' }} {{ Auth::user()->last_name ?? '' }}! 🎉</h5>
                  <p class="mb-4">
                    Hope you are having a nice day!
                  </p>

                </div>
              </div>
              <div class="col-sm-5 text-center text-sm-left">
                <div class="card-body pb-0 px-0 px-md-4">
                  <img
                    src="{{ asset('backend/assets/img/illustrations/man-with-laptop-light.png') }}"
                    height="140"
                    alt="View Badge User"
                    data-app-dark-img="illustrations/man-with-laptop-dark.png"
                    data-app-light-img="illustrations/man-with-laptop-light.png"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
   </div>
    <!-- / Content -->

    <div class="content-backdrop fade"></div>
  </div>
@endsection

@push('script')

@endpush

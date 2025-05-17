
<!DOCTYPE html>
,<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Profil</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="{{ asset('backend/assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('backend/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('backend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{ asset('backend/assets/css/style.css') }}" rel="stylesheet">

  {{-- fontawesome --}}
  <link rel="stylesheet" type="text/css" href="{{ asset('fontawesome/css/all.min.css') }}">

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="{{asset('backend/assets/img/pkk.png')}}" alt="">
        <span class="d-none d-lg-block">PKK NGANJUK</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  {{-- SIDEBAR --}}
  @include('backend.includes.sidebar')

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Profil</h1>
    </div>

    <section class="section">
      <div class="section profile">
        <div class="row">
          <div class="col-xl-8">
            <div class="card">
              <div class="card-body pt-3">
                <div class="tab-pane fade show active profile-overview">

                  <h5 class="card-title">Profile lengkap</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Nama lengkap</div>
                    <div class="col-lg-9 col-md-8">
                      {{ $userType == 'pengguna' ? $user->full_name : $user->name }}
                    </div>
                  </div>
                  
                  @if($userType == 'user')
            <div class="row">
            <div class="col-lg-3 col-md-4 label">Email</div>
            <div class="col-lg-9 col-md-8">{{ $user->email }}</div>
            </div>
          @endif
                  
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Nomor telepon</div>
                    <div class="col-lg-9 col-md-8">
                      {{ $userType == 'pengguna' ? $user->phone_number : $user->nomer_telepon }}
                    </div>
                  </div>
                  
                  @if($userType == 'user')
            <div class="row">
            <div class="col-lg-3 col-md-4 label">Alamat</div>
            <div class="col-lg-9 col-md-8">{{ $user->alamat }}</div>
            </div>
          @endif

                </div>

              </div>
            </div>
          </div>
        </div>
      </div>


    </section>




    <section class="section">

      <div class="section profile">
        <div class="row">
          <div class="col-xl-8">
            <div class="card">
              <div class="card-body pt-3">
                @if ($message = Session::get('success'))
          <div class="alert alert-success" role="alert">
            {{ $message }}
          </div>
        @endif

                <h5 class="card-title">Edit profil</h5>

                <!-- Profile Edit Form -->
                <form action="{{ route('profile.update', $user->id) }}" method="POST">
                  @method('PUT')
                  @csrf
                
                  <div class="row mb-3">
                    <label for="name" class="col-md-4 col-lg-3 col-form-label">Nama lengkap</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="name" type="text" class="form-control" id="name"
                        value="{{ $userType == 'pengguna' ? $user->full_name : $user->name }}" required
                        oninvalid="this.setCustomValidity('Harap lengkapi nama')" oninput="this.setCustomValidity('')">
                      @error('name')
              <div class="invalid-feedback">
              {{ $message }}
              </div>
            @enderror
                    </div>
                  </div>
                
                  @if($userType == 'user')
            <div class="row mb-3">
            <label for="email" class="col-md-4 col-lg-3 col-form-label">Email</label>
            <div class="col-md-8 col-lg-9">
              <input name="email" type="text" class="form-control" readonly value="{{ $user->email }}">
            </div>
            </div>
          @endif
                
                  <div class="row mb-3">
                    <label for="nomer_telepon" class="col-md-4 col-lg-3 col-form-label">Nomor telepon</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="nomer_telepon" type="tel" pattern="^08\d{10,13}$"
                        class="form-control @error('nomer_telepon') is-invalid @enderror"
                        value="{{ $userType == 'pengguna' ? $user->phone_number : $user->nomer_telepon }}" required
                        oninvalid="this.setCustomValidity('Nomor telepon tidak sesuai')" oninput="this.setCustomValidity('')">
                      <p class="mt-1">*Nomor telepon harus diawali dengan 08 dan diikuti 10-13 digit angka</p>
                      @error('nomer_telepon')
              <div class="invalid-feedback">
              {{ $message }}
              </div>
            @enderror
                    </div>
                  </div>
                
                  @if($userType == 'user')
            <div class="row mb-3">
            <label for="alamat" class="col-md-4 col-lg-3 col-form-label">Alamat</label>
            <div class="col-md-8 col-lg-9">
              <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" rows="6" required
              oninvalid="this.setCustomValidity('Harap lengkapi alamat')"
              oninput="this.setCustomValidity('')">{{ $user->alamat }}</textarea>
              @error('alamat')
          {{-- <div class="invalid-feedback">
          {{ $message }}
          </div> --}}
        @enderror
            </div>
            </div>
          @endif
                  <div class="text-end mt-4">
                    <button class="btn btn-primary mr-auto background-blue-1 mb-2 fw-semibold fs-5" type="submit">
                      Edit Data Profil
                    </button>
                  </div>
                </form><!-- End Profile Edit Form -->

                
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>

    <section class="section">
      <div class="section profile">
        <div class="row">
          <div class="col-xl-8">
            <div class="card">
              <div class="card-body pt-3">

                @if ($message = Session::get('successs'))
          <div class="alert alert-success" role="alert">
            {{ $message }}
          </div>
        @endif

                @if ($message = Session::get('error'))
          <div class="alert alert-danger" role="alert">
            {{ $message }}
          </div>
        @endif

                <h5 class="card-title">Ubah Kata Sandi</h5>

                <form action="{{ route('change_password.update', $user->id) }}" method="POST">

                  @method('PUT')
                  @csrf

                  <div class="row mb-3">
                    <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Kata sandi saat ini</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="currentPassword" type="password" minlength="8"
                        class="form-control @error('currentPassword') is-invalid @enderror" id="currentPassword"
                        required placeholder="Masukkan Kata Sandi Saat Ini">
                      @error('currentPassword')
              <div class="invalid-feedback">
              {{ $message }}
              </div>
            @enderror
                    </div>

                  </div>

                  <div class="row mb-3">
                    <label for="newpassword" class="col-md-4 col-lg-3 col-form-label">Kata sandi baru</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="newpassword" type="password" minlength="8"
                        class="form-control @error('newpassword') is-invalid @enderror" id="newPassword"
                        placeholder="Masukkan Kata Sandi Baru" required>
                      @error('newpassword')
              <div class="invalid-feedback">
              {{ $message }}
              </div>
            @enderror
                    </div>
                  </div>


                  <div class="row mb-3">
                    <label for="renewpassword" class="col-md-4 col-lg-3 col-form-label">Konfirmasi kata sandi baru
                    </label>
                    <div class="col-md-8 col-lg-9">
                      <input name="renewpassword" type="password" minlength="8"
                        class="form-control @error('renewpassword') is-invalid @enderror" id="renewPassword"
                        placeholder="Konfirmasi Kata Sandi Anda" required>
                      @error('renewpassword')
              <div class="invalid-feedback">
              {{ $message }}
              </div>
            @enderror
                    </div>
                  </div>

                  <div class="text-end mt-4">
                    <button class="btn btn-primary mr-auto background-blue-1 mb-2 fw-semibold fs-5" type="submit">Ubah
                      Kata Sandi</button>
                  </div>
                </form><!-- End Change Password Form -->

              </div>
            </div>
          </div>
        </div>
      </div>


    </section>

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('backend/assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/quill/quill.min.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/php-email-form/validate.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('backend/assets/js/main.js') }}"></script>

</body>

{{--

</html> --}}
{{-- @endsection --}}
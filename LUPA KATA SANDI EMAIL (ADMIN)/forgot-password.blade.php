<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Kata Sandi</title>

  <!-- Vendor CSS Files -->
  <link href="{{ asset('frontend/assets/vendor/aos/aos.css')}}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">

  <!-- style -->
  <link rel="stylesheet" href="frontend/assets/css/login.css">
</head>

<body>
  <section class="section vh-100 vw-100">
    <div class="row">
      <div class="col-4 mx-auto mt-md-5">
        <div class="card p-2 bg-light">
          <div class="card-body mt-1">

            @if ($message = Session::get('errors'))
        <div class="alert alert-danger" role="alert">
          {{ $message }}
        </div>
      @endif

            @if ($message = Session::get('success'))
        <div class="alert alert-success" role="alert">
          {{ $message }}
        </div>
      @endif

            @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
            @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
            </ul>
          </div>
      @endif

            <form action="{{ route('password.email') }}" method="POST" class="p-2">
              @csrf

              <h3 class="text-center text-primary">Lupa Kata Sandi</h3>
              <p>Lupa kata sandi Anda? Tidak masalah. Beri tahu kami alamat email Anda dan kami akan mengirimi Anda
                tautan setel ulang kata sandi melalui email yang anda inputkan.</p>
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" id="email" class="form-control" required
                oninvalid="this.setCustomValidity('Harap masukkan email anda')" oninput="this.setCustomValidity('')"
                placeholder="Masukkan Email Anda" />

              <div class="text-end pt-1 pb-1 mt-2">
                <button class="btn btn-primary ps-xxl-5 pe-xxl-5 mr-auto background-blue-1 mb-2 fw-semibold fs-5"
                  type="submit">Kirim</button>
              </div>
            </form>

            <div class="text-center mt-3">
              <a href="{{ url('/otp') }}" class="text-decoration-none text-primary fw-semibold">Atur
                sandi dengan nomor WhatsApp</a>
            </div>



          </div>


        </div>
      </div>
    </div>
  </section>
</body>

</html>
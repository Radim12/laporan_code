<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi</title>

        <!-- Vendor CSS Files -->
    <link href="{{ asset ('frontend/assets/vendor/aos/aos.css')}}" rel="stylesheet">
    <link href="{{ asset ('frontend/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ asset ('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link href="{{ asset ('frontend/assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
    <link href="{{ asset ('frontend/assets/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
    <link href="{{ asset ('frontend/assets/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">

        <!-- style -->
    <link rel="stylesheet" href="frontend/assets/css/login.css">

</head>

<body>
    <section class="section vh-100 vw-100">
        <div class="row">
          <div class="col-4 mx-auto mt-md-5">
            <div class="card p-2 bg-light">
              <div class="card-body mt-1">

              <form action="{{ route('password.store') }}" method="POST" class="p-2">
              @csrf

              <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <h3 class="text-center text-primary">Atur Ulang Kata Sandi</h3>

                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control mb-3"
                required oninvalid="this.setCustomValidity('Harap masukkan email anda')" 
                oninput="this.setCustomValidity('')" placeholder="Masukkan Email Anda" value="{{ $request->email }}"/>
                <div class="form-outline mb-3">
                  <label for="password" class="form-label">Kata Sandi</label>
                  <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror 
                  required autocomplete="password" oninvalid="this.setCustomValidity('Harap lengkapi kata sandi anda')" 
                  oninput="this.setCustomValidity('')"
                  placeholder="Kata Sandi Baru Anda"/>

                  @error('password')
                      <div class="invalid-feedback">
                        {{ $message }}
                      </div>
                  @enderror
                  </div>

                <div class="form-outline mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" 
                    required autocomplete="password" oninvalid="this.setCustomValidity('Harap lengkapi kata sandi anda')" 
                    oninput="this.setCustomValidity('')"
                    placeholder="Konfirmasi Kata Sandi Anda"/>
                    
                    @error('password_confirmation')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                    @enderror
                </div>
 
              <div class="text-end pt-1 pb-1 mt-2">
               <button class="btn btn-primary ps-xxl-5 pe-xxl-5 mr-auto background-blue-1 mb-2 fw-semibold fs-5" type="submit">Atur Ulang Kata Sandi</button>
              </div>
              </form>





              </div>
              

            </div>
          </div>
        </div>
      </section>
</body>

</html>
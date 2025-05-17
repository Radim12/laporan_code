<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Atur Ulang Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .form-control {
            border-radius: 12px;
        }

        .btn-success {
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <section class="vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6" data-aos="flip-up">
                    <div class="card p-4 bg-light">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif

                            <form action="{{ route('otp.reset') }}" method="POST">
                                @csrf
                                <h3 class="text-center text-success mb-3">Atur Ulang Password</h3>
                                <p class="text-muted text-center">Silakan buat sandi baru Anda.</p>

                                <input type="hidden" name="phone_number" value="{{ $phone_number }}">

                                <label for="password">Sandi Baru</label>
                                <input type="password" name="password" class="form-control mb-3" required>

                                <label for="password_confirmation">Konfirmasi Sandi</label>
                                <input type="password" name="password_confirmation" class="form-control mb-4" required>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-success w-100">Simpan Password</button>
                                </div>
                            </form>

                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}" class="text-decoration-none text-secondary">Kembali ke
                                    Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>
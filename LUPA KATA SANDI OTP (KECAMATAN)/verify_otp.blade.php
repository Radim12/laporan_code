<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi OTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & AOS -->
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
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .form-control {
            border-radius: 12px;
            text-align: center;
            font-size: 1.5rem;
            letter-spacing: 10px;
        }

        .btn-primary {
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .spinner-border {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <section class="vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6" data-aos="zoom-in">
                    <div class="card p-4 bg-light">
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif

                            <form action="{{ route('otp.verify') }}" method="POST" id="otpForm">
                                @csrf
                                <h3 class="text-center text-primary mb-3">Verifikasi OTP</h3>
                                <p class="text-muted text-center mb-4">
                                    Masukkan kode OTP yang dikirim ke nomor:
                                    <strong>{{ session('phone_number') ?? 'Nomor tidak ditemukan' }}</strong>
                                </p>

                                <input type="hidden" name="phone_number" value="{{ session('phone_number') }}">

                                <input type="text" name="otp" maxlength="4" pattern="[0-9]{4}" required
                                    inputmode="numeric" autocomplete="one-time-code"
                                    class="form-control text-center mb-3" placeholder="____"
                                    title="Masukkan 4 digit OTP"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                        <span id="submitText">Verifikasi</span>
                                        <span id="spinner" class="spinner-border spinner-border-sm d-none"
                                            role="status"></span>
                                    </button>
                                </div>
                            </form>

                            <div class="text-center mt-3">
                                <a href="{{ route('otp.form') }}" class="text-decoration-none text-secondary">
                                    Kirim ulang OTP
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init();</script>

    <!-- Loading saat submit -->
    <script>
        document.getElementById('otpForm').addEventListener('submit', function () {
            document.getElementById('submitText').classList.add('d-none');
            document.getElementById('spinner').classList.remove('d-none');
            document.getElementById('submitBtn').disabled = true;
        });
    </script>
</body>

</html>
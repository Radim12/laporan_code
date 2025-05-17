<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Reset Sandi via WhatsApp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap & AOS -->
  <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- Custom CSS -->
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
    }

    .btn-primary {
      background-color: #007bff;
      border: none;
      border-radius: 10px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #0056b3;
      transform: scale(1.05);
    }

    .whatsapp-icon {
      color: green;
      margin-right: 8px;
    }
  </style>
</head>

<body>
  <section class="vh-100 d-flex align-items-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6" data-aos="fade-up">
          <div class="card p-4 bg-light">
            <div class="card-body">
              @if (session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
              @endif
              @if ($errors->any())
              <div class="alert alert-danger">{{ $errors->first() }}</div>
              @endif

              <form action="{{ route('otp.send') }}" method="POST">
                @csrf
                <h3 class="text-center text-primary mb-3">
                  <i class="bi bi-whatsapp whatsapp-icon"></i>
                  Atur Sandi via WhatsApp
                </h3>
                <p class="text-muted text-center mb-4">Masukkan nomor WhatsApp Anda untuk menerima kode
                  OTP.</p>

                <label for="phone_number" class="form-label">Nomor WhatsApp</label>
                <input type="tel" name="phone_number" class="form-control mb-3" pattern="08[0-9]{9,12}"
                  title="Masukkan nomor WhatsApp yang valid (contoh: 081234567890)"
                  placeholder="08xxxxxxxxxx" required>

                <div class="text-end">
                  <button type="submit" class="btn btn-primary w-100 fw-semibold" id="submitBtn">
                    <span id="submitText">Kirim OTP</span>
                    <span id="spinner" class="spinner-border spinner-border-sm d-none"
                      role="status"></span>
                  </button>
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

  <!-- JS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init();

    document.querySelector('form').addEventListener('submit', function() {
      document.getElementById('submitText').classList.add('d-none');
      document.getElementById('spinner').classList.remove('d-none');
      document.getElementById('submitBtn').disabled = true;
    });
  </script>
</body>

</html>
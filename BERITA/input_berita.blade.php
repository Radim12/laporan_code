{{-- @extends('backend/layouts.template') --}}

{{-- @section('content1') --}}

<!DOCTYPE html>
,<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Input Berita</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="backend/assets/img/favicon.png" rel="icon">
  <link href="backend/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="backend/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="backend/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="backend/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="backend/assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="backend/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="backend/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="backend/assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="backend/assets/css/style.css" rel="stylesheet">

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

  <!-- ======= Sidebar ======= -->
  @include('backend.includes.sidebar')
  <!-- End Sidebar-->

  <main id="main" class="main">

    <section class="section">
      <div class="row">
        <div class="col-md-12 mx-auto mt-2">
          <div class="pagetitle">
            <h1>Input Berita</h1>
          </div><!-- End Page Title -->

          <div class="card">
            <div class="card-body mt-4">
              <form action="{{ route('input_berita.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-outline mb-4">
                  <label for="image" class="form-label">Gambar</label>
                  <input type="file" name="image" id="image" class="form-control" required
                    oninvalid="this.setCustomValidity('Harap unggah gambar')" oninput="this.setCustomValidity('')" />
                </div>

                <div class="form-outline mb-4">
                  <label for="judul" class="form-label">Judul</label>
                  <input type="text" name="judul" id="judul" class="form-control" required
                    oninvalid="this.setCustomValidity('Harap lengkapi judul')" oninput="this.setCustomValidity('')"
                    placeholder="Masukkan Judul" />
                </div>

                <div class="form-outline mb-4">
                  <label for="deskripsi" class="form-label">Deskripsi</label>
                  <textarea class="form-control" name="deskripsi" rows="6" id="deskripsi"
                    placeholder="Masukkan Deskripsi Berita" required
                    oninvalid="this.setCustomValidity('Harap lengkapi deskripsi')"
                    oninput="this.setCustomValidity('')"></textarea>
                </div>

                <div class="form-outline mb-4">
                  <label for="file" class="form-label">Upload Dokumen Pendukung (Opsional)</label>
                  <input type="file" name="file" id="file" class="form-control" class="dropzone"
                    oninput="this.setCustomValidity('')" />
                </div>

                <div class="text-end pt-1 pb-1 mt-4">
                  <button class="btn btn-primary ps-xxl-5 pe-xxl-5 mr-auto background-blue-1 mb-2 fw-semibold fs-5"
                    type="submit">Kirim</button>
                </div>
              </form>


            </div>
          </div>

          <div class="pagetitle">
            <h1>Daftar Berita</h1>
          </div><!-- End Page Title -->

          @if ($message = Session::get('success'))
        <div class="alert alert-success" role="alert">
        {{ $message }}
        </div>
      @endif

          <div class="card mt-2">
            <div class="card-body">

              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Gambar</th>
                    <th scope="col">Judul</th>
                    <th scope="col">Deskripsi</th>
                    <th scope="col">File</th>
                    <th scope="col">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @php
          $no = 1;
          @endphp
                  @forelse ($data as $berita)
            <tr>
            <th scope="row">{{ $no++ }}</th>
            <td><img src="{{ asset('storage/berita/' . $berita->image) }}" class="rounded" width="150"
              height="150">
            </td>
            <td>{{ Str::limit($berita->judul, 25) }}</td>
            <td>{{ Str::limit($berita->deskripsi, 25) }}</td>
            <td>{{ Str::limit($berita->file, 20) }}</td>
            <td>
              <a href="{{ route('input_berita.edit', $berita->id) }}" class="btn btn-sm btn-tambah">Edit</a>

              <form action="{{ route('input_berita.destroy', $berita->id)}}" method="POST"
              class="d-inline delete-form">
              @csrf
              @method('DELETE')
              <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(this)">Hapus</button>
              </form>
            </td>
            </tr>
          @empty
            <div class="alert alert-danger mt-4">
            Tidak ada data berita
            </div>
          @endforelse

                </tbody>
              </table>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="backend/assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="backend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="backend/assets/vendor/chart.js/chart.umd.js"></script>
  <script src="backend/assets/vendor/echarts/echarts.min.js"></script>
  <script src="backend/assets/vendor/quill/quill.min.js"></script>
  <script src="backend/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="backend/assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="backend/assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="backend/assets/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <script type="text/javascript">
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone('#pdf', {
      maxFilesize: 1,
      acceptedFiles: ".pdf",
      addRemoveLinks: true,
      autoProcessQueue: false,
      init: function () {
        $("button").click(function (e) {
          e.preventDefault();
          myDropzone.processQueue();
        });

        this.on('sending', function (file, xhr, formData) {
          var data = $('#pdf').serializeArray();
          $.each(data, function (key, el) {
            formData.append(el.name, el.value);
          });
        });
      }
    });
  </script>

  <script>
    function confirmDelete(button) {
      Swal.fire({
        title: 'Yakin hapus data?',
        text: "Data yang dihapus tidak bisa dikembalikan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          // Cari form terdekat dan submit
          button.closest('form').submit();
        }
      });
    }
  </script>

</body>

{{--

</html> --}}
{{-- @endsection --}}
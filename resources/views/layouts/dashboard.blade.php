<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f5f7fa;
        }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #1e293b;
        }
        .sidebar a {
            color: #cbd5e1;
            text-decoration: none;
        }
        .sidebar a:hover,
        .sidebar .active {
            background: #334155;
            color: #fff;
        }
        .content {
            margin-left: 250px;
        }
        .sidebar .active {
            background: #334155;
            color: #fff;
            font-weight: 500;
        }
    </style>
</head>
<body>

    @include('partials.sidebar')

    <div class="content">
        @include('partials.navbar')

        <main class="p-4">
            @yield('content')
        </main>
    </div>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    @if(session('info'))
        Swal.fire({
            icon: 'info',
            title: 'Info',
            text: '{{ session('info') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
</script>
<script>
function confirmDelete(form) {
    Swal.fire({
        title: 'Yakin hapus produk?',
        text: "Data produk akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>

</body>
</html>

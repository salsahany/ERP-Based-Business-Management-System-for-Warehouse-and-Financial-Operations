<div class="sidebar position-fixed p-3">
    <h4 class="text-white mb-4">Warehouse</h4>

    {{-- Wilayah Switcher for Finance/Owner --}}
    @if(auth()->check() && in_array(auth()->user()->role, ['finance', 'owner']))
    <div class="mb-3">
        <small class="text-white-50 text-uppercase fw-bold">Pilih Wilayah:</small>
        <select class="form-select form-select-sm mt-1" onchange="window.location.href=this.value">
            <option value="{{ route('wilayah.switch', 'all') }}" {{ !session('active_wilayah_id') ? 'selected' : '' }}>
                Semua Wilayah
            </option>
            @foreach(\App\Models\Wilayah::all() as $w)
            <option value="{{ route('wilayah.switch', $w->id) }}" {{ session('active_wilayah_id') == $w->id ? 'selected' : '' }}>
                {{ $w->nama_wilayah }}
            </option>
            @endforeach
        </select>
    </div>
    <hr class="text-white-50">
    @endif

    {{-- Active Wilayah switcher for Admin (only their assigned wilayahs) --}}
    @if(auth()->check() && auth()->user()->role === 'admin')
    <div class="mb-3">
        <small class="text-white-50 text-uppercase fw-bold">Wilayah Aktif:</small>
        <select class="form-select form-select-sm mt-1" onchange="window.location.href=this.value">
            @foreach(auth()->user()->wilayahs as $w)
            <option value="{{ route('wilayah.switch', $w->id) }}" {{ session('active_wilayah_id') == $w->id ? 'selected' : '' }}>
                {{ $w->nama_wilayah }}
            </option>
            @endforeach
        </select>
    </div>
    <hr class="text-white-50">
    @endif

    <ul class="nav flex-column gap-1">

        <li class="nav-item">
            <a href="/dashboard"
               class="nav-link px-3 py-2 rounded {{ request()->is('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('products.index') }}"
               class="nav-link px-3 py-2 rounded {{ request()->is('products*') ? 'active' : '' }}">
                Master Produk
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('sales-persons.index') }}"
               class="nav-link px-3 py-2 rounded {{ request()->is('sales-persons*') ? 'active' : '' }}">
                Master Sales
            </a>
        </li>

        @if(auth()->user()->role === 'owner')
        <li class="nav-item">
            <a href="{{ route('users.index') }}"
               class="nav-link px-3 py-2 rounded {{ request()->is('users*') ? 'active' : '' }}">
                Master User
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('wilayah.index') }}"
               class="nav-link px-3 py-2 rounded {{ request()->is('wilayah*') ? 'active' : '' }}">
                Manajemen Wilayah
            </a>
        </li>
        @endif

        <li class="nav-item">
            <a href="/barang-masuk"
               class="nav-link px-3 py-2 rounded {{ request()->is('barang-masuk*') ? 'active' : '' }}">
                Barang Masuk
            </a>
        </li>

        <li class="nav-item">
            <a href="/barang-keluar"
               class="nav-link px-3 py-2 rounded {{ request()->is('barang-keluar*') ? 'active' : '' }}">
                Barang Keluar
            </a>
        </li>

        <li class="nav-item">
            <a href="/laporan/stok"
               class="nav-link px-3 py-2 rounded {{ request()->is('laporan/stok') ? 'active' : '' }}">
                Laporan Stok
            </a>
        </li>

        <li class="nav-item">
            <a href="/laporan/transaksi"
               class="nav-link px-3 py-2 rounded {{ request()->is('laporan/transaksi*') ? 'active' : '' }}">
                Laporan Transaksi
            </a>
        </li>

        <li class="nav-item">
            <a href="/laporan/tagihan-sales"
               class="nav-link px-3 py-2 rounded {{ request()->is('laporan/tagihan-sales*') ? 'active' : '' }}">
                Laporan Tagihan Sales
            </a>
        </li>

        <li class="nav-item">
            <a href="/sales-payment"
               class="nav-link px-3 py-2 rounded {{ request()->is('sales-payment*') ? 'active' : '' }}">
                Tagihan Sales
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('purchase-orders.index') }}"
               class="nav-link px-3 py-2 rounded {{ request()->is('purchase-orders*') ? 'active' : '' }}">
                Purchase Order
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('petty-cash.index') }}"
               class="nav-link px-3 py-2 rounded {{ request()->is('petty-cash*') ? 'active' : '' }}">
                Petty Cash
            </a>
        </li>

    </ul>
</div>

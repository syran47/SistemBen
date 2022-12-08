<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title')</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Font Awesome -->
  {{-- <link rel="stylesheet" href="{{ asset('css/Template/all.min.css') }}"> --}}
  <script src="https://kit.fontawesome.com/d808726940.js" crossorigin="anonymous"></script>
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('css/Master/adminlte.min.css') }}">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{ asset('css/Template/tempusdominus-bootstrap-4.min.css') }}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ asset('css/Template/jqvmap.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('css/Template/OverlayScrollbars.min.css') }}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('css/Template/summernote-bs4.min.css') }}">
  <!-- loading spin -->
  <link rel="stylesheet" href="{{ asset('css/Template/loading.css') }}">
  <!-- ExtraCSS -->
  @yield('css')
  <!-- ExtraJS -->
  @yield('js_atas')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="{{ asset('img/ante.png') }}" alt="AnteMinangLogo" height="150" width="400">
    </div>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ route('home') }}" class="brand-link">
        {{-- <img src="{{ asset('img/ante.png') }}" alt="AnteMinangLogo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
        <span class="brand-text font-weight-light">Bina Equipindo Nusantara</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        {{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex @yield('profil_aktif')">
          <div class="image">
            <img src="{{auth()->user()->foto}}" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="{{ route('users.show', 1)}}" class="d-block">{{auth()->user()->name}}</a>
          </div>
        </div> --}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-item">
              {{-- <a href="{{ route('home')}}" class="nav-link @yield('home_aktif')">
                <i class="nav-icon fas fa-home"></i>
                <p>
                  Home
                </p>
              </a> --}}
            </li>

            {{-- <li class="nav-item @yield('mb_open')">
              <a href="#" class="nav-link ">
                <i class="nav-icon fas fa-edit"></i>
                <p>
                  Manajemen Barang
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('bahanbaku.index')}}" class="nav-link @yield('mb_bb_aktif')">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Bahan Baku</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('produk.index') }}" class="nav-link @yield('mb_bj_aktif')">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Produk</p>
                  </a>
                </li>
              </ul>
            </li> --}}
            
            <li class="nav-item">
              <a href="{{route('bahanbaku.index')}}" class="nav-link @yield('mb_bb_aktif')">
                <i class="far fa-circle nav-icon"></i>
                <p>Manajemen Properti</p>
              </a>
            </li>
            
            {{-- <li class="nav-header">History</li> --}}
            <li class="nav-item @yield('bahanbaku_open')">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cubes"></i>
                <p>
                  History Properti
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item ">
                  <a href="{{route('history.bahanbaku.masuk')}}" class="nav-link @yield('bbm_aktif')">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Properti Masuk</p>
                  </a>
                </li>
                <li class="nav-item ">
                  <a href="{{route('history.bahanbaku.keluar')}}" class="nav-link @yield('bbk_aktif')">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Properti Keluar</p>
                  </a>
                </li>
                @if(auth()->user()->role == 1)
                <li class="nav-item ">
                  <a href="{{route('history.bahanbaku.data')}}" class="nav-link @yield('dbb_aktif')">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Data Properti</p>
                  </a>
                </li>
                @endif
              </ul>
            </li>
            {{-- <li class="nav-item @yield('produk_open')">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-bacon"></i>
                <p>
                  Produk
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('history.produk.masuk')}}" class="nav-link @yield('pm_aktif')">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Produk Masuk</p>
                  </a>
                </li>
                <li class="nav-item ">
                  <a href="{{route('history.produk.keluar')}}" class="nav-link @yield('pk_aktif')">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Produk Keluar</p>
                  </a>
                </li>
                @if(auth()->user()->role == 1)
                <li class="nav-item ">
                  <a href="{{route('history.produk.data')}}" class="nav-link @yield('dp_aktif')">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Data Produk</p>
                  </a>
                </li>
                @endif
              </ul>
            </li> --}}
            @if(auth()->user()->role == 1)
            <li class="nav-header">Users</li>
            <li class="nav-item">
              <a href="{{route('users.index')}}" class="nav-link @yield('is_aktif')">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  List User
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{route('users.create')}}" class="nav-link @yield('mu_aktif')">
                <i class="nav-icon fas fa-user-edit"></i>
                <p>
                  Manajemen Users
                </p>
              </a>
            </li>
            @endif
            <li class="nav-item">
              <form action="{{route('logout')}}" method="POST">
                @csrf
                <button type="submit" class="nav-link" style="text-align: left;background-color: transparent;border:none;  !important">
                  <i class="nav-icon fas fa-sign-out-alt"></i>
                  <p>
                    Logout
                  </p>
                </button>
              </form>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">@yield('title')</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                @yield('page_aktif')
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      @yield('isi')
      <!-- /.content -->

    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
      <strong>Copyright &copy; 2022 <a href="#">Kerja Praktik di CV. Bina Equipindo Nusantara</a>.</strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0.1
      </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->
  <script>
    function preLoad(classReq, color , sizeReq) {
      console.log("preload")
      $(classReq).html(' ');
      $(classReq).addClass('d-flex justify-content-center');
      // $(classReq).css('color', color);
      $(classReq).html('<i class="fas fa-'+sizeReq+' '+color+' fa-sync-alt my-5 loading-spin"></i>');
    }
    function afterLoad(classReq) {
      $(classReq).removeClass('d-flex justify-content-center');
      $(classReq).html(' ');
    }
  </script>
  <!-- jQuery -->
  <script src="{{ asset('js/Template/jquery.min.js') }}"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="{{ asset('js/Template/jquery-ui.min.js') }}"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('js/Template/bootstrap.bundle.min.js') }}"></script>
  <!-- JQVMap -->
  <script src="{{ asset('js/Template/jquery.vmap.min.js') }}"></script>
  <script src="{{ asset('js/Template/jquery.vmap.usa.js') }}"></script>
  <!-- jQuery Knob Chart -->
  <script src="{{ asset('js/Template/jquery.knob.min.js') }}"></script>
  <!-- daterangepicker -->
  {{-- <script src="{{ asset('js/Template/moment.min.js') }}"></script>
  <script src="{{ asset('js/Template/daterangepicker.js') }}"></script> --}}
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="{{ asset('js/Template/tempusdominus-bootstrap-4.min.js') }}"></script>
  <!-- Summernote -->
  <script src="{{ asset('js/Template/summernote-bs4.min.js') }}"></script>
  <!-- overlayScrollbars -->
  <script src="{{ asset('js/Template/jquery.overlayScrollbars.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('js/Master/adminlte.js') }}"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="{{ asset('js/Master/demo.js') }}"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="{{ asset('js/Master/dashboard.js') }}"></script>
  @yield('js_bawah')
  @include('sweetalert::alert') 
</body>

</html>
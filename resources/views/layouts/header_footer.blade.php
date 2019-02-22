
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>CMMS</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="shortcut icon" href="./img/favicon.png">

</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper" id="app">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
      <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link text-teal" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
          </li>
      </ul>
    <!-- SEARCH FORM -->
    <div class="form-inline ml-3">
      <div class="input-group input-group-sm">
          <h4 class="text-teal">Classroom Management and Mapping System</h4>
      </div>
    </div>
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
          <i class="fa fa-cog"></i> Settings
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

          <router-link to="/profile" class="dropdown-item">
            <i class="fa fa-users mr-2 text-orange"></i>Profile
          </router-link>
          <div class="dropdown-divider"></div>

          <a class="dropdown-item" href="{{ route('logout') }}"
          onclick="event.preventDefault(); 
                      document.getElementById('logout-form').submit();">
            <i class="fas fa-power-off mr-2 text-red"></i>Logout
          </a>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>


        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->


    <!-- Sidebar -->
    <div class="sidebar">
        <a href="" class="brand-link">
            <img src="./img/cmms-logo.png" alt="AdminLTE Logo" class=" img-circle elevation-3"
                 style="opacity: .8">
        </a>
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="./img/people.png" class="img-circle elevation-3" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">
            {{ Auth::user()->name }}<br/>
            <small> <i class="fas fa-circle fa-sm text-green"></i> {{ Auth::user()->type}}</small>
          </a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <router-link to="/dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt text-white"></i>
              <p>
                Dashboard
              </p>
            </router-link>
          </li>

          @can('isAdmin')
          <li class="nav-item">
            <router-link to="/users" class="nav-link">
              <i class="nav-icon fas fa-users text-purple"></i>
              <p>
                Users Management
              </p>
            </router-link>
          </li>
          @endcan     

          <li class="nav-item has-treeview menu-close">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-building text-green"></i>
              <p>
                Classroom Management
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <li class="nav-item ml-2">
                <router-link to="/building" class="nav-link">
                  <i class="nav-icon fas fa-angle-right text-green"></i>
                  <p>
                    Buildings
                  </p>
                </router-link>
              </li>

              <li class="nav-item ml-2">
                <router-link to="/floor" class="nav-link">
                  <i class="nav-icon fas fa-angle-right text-green"></i>
                  <p>
                    Floors
                  </p>
                </router-link>
              </li>

                <li class="nav-item ml-2">
                  <router-link to="/classroom" class="nav-link">
                    <i class="nav-icon fas fa-angle-right text-green"></i>
                    <p>
                      Classrooms
                    </p>
                  </router-link>
                </li>

                <li class="nav-item ml-2">
                  <router-link to="/classroomtype" class="nav-link">
                    <i class="nav-icon fas fa-angle-right text-green"></i>
                    <p>
                      Classroom Types
                    </p>
                  </router-link>
                </li>
            </ul>
          </li>    

          <li class="nav-item has-treeview menu-close">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-book-open text-teal"></i>
              <p>
                Subject Management
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <li class="nav-item ml-2">
                <router-link to="/subject" class="nav-link">
                  <i class="nav-icon fas fa-angle-right text-teal"></i>
                  <p>
                    Subjects
                  </p>
                </router-link>
              </li>
            </ul>
          </li> 

          <li class="nav-item has-treeview menu-close">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-graduation-cap text-yellow"></i>
              <p>
                Course Management
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <li class="nav-item ml-2">
                <router-link to="/course" class="nav-link">
                  <i class="nav-icon fas fa-angle-right text-yellow"></i>
                  <p>
                    Courses
                  </p>
                </router-link>
              </li>

              <li class="nav-item ml-2">
                <router-link to="/section" class="nav-link">
                  <i class="nav-icon fas fa-angle-right text-yellow"></i>
                  <p>
                    Sections
                  </p>
                </router-link>
              </li>
            </ul>
          </li> 

          <li class="nav-item">
            <router-link to="/professor" class="nav-link">
              <i class="nav-icon fas fa-chalkboard-teacher text-blue"></i>
              <p>
                Professor Management
              </p>
            </router-link>
          </li>  

          <li class="nav-item">
            <router-link to="/subjecttagging" class="nav-link">
              <i class="nav-icon fas fa-tags text-orange"></i>
              <p>
                Tagged Subject & Schedule
              </p>
            </router-link>
          </li>          

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">

          <!-- This thing is to view all the vue js content -->
          <router-view></router-view>

          <!-- This thing is for progressbar -->
          <vue-progress-bar></vue-progress-bar>

        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <!-- Default to the left -->
    <strong>Copyright &copy; 2019-2020 <a href="#">Codetrap</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->



@auth
  <script>
    // to pass the data.user to Gate function
    window.user = @json(auth()->user())
</script>
@endauth

<!-- jQuery -->
<script src="/image-map-resizer/js/imageMapResizer.min.js"></script>
<script src="/js/app.js"></script>

</body>
</html>

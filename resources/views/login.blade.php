
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.108.0">
    <title>CPA Signin</title>

    <link href="{{asset('assets/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.1/sweetalert2.min.css">
    <style>
      body{
        padding: 40px 35%;
        background-image: url("{{asset('images/background.png')}}");
        background-repeat: no-repeat;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
      }
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="sign-in.css" rel="stylesheet">
  </head>
  <body class="text-center">
    
<main class="form-signin w-100 m-auto">
  <form method="POST" action="{{route('sign_in')}}">
    <img class="mb-4" src="{{asset('MBC_LOGO.jpg')}}" alt="" width="100" height="80">
    <h2 class="h2 mb-3 fw-normal">Certificate of Performance Automation</h2>
    <h1 class="h3 mb-3 fw-bold">Please sign in</h1>
    @csrf
    <div>
        <div class="form-floating">
          <input style="margin-bottom: 5px;" name = "username" type="username" class="form-control" id="floatingInput">
          <label for="floatingInput">Email address</label>
          @error('username')
            <small style="color: red">{{$message}}</small>
          @enderror
        </div>
        <div class="form-floating">
          <input style="margin-bottom: 5px;" name = "password" type="password" class="form-control" id="floatingPassword">
          <label for="floatingPassword">Password</label>
          @error('password')
            <small style="color: red">{{$message}}</small>
          @enderror
        </div>
        @error('invalid_login')
          <small style="color: red">{{$message}}</small>
        @enderror
        <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
    </div>
    <p class="mt-5 mb-3 text-muted">&copy; Manila Broadcasting Company 2023 @ Customer Relationship Management</p>
  </form>
</main>
  
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.1/sweetalert2.min.js"></script>
  <script type="text/javascript">
    var is_access = "{{session('message')}}";
    if(is_access){
      Swal.fire({
        position: 'top-end',
        icon: 'warning',
        title: is_access,
        showConfirmButton: false,
        timer: 1500
      })
    }
  </script>
    
  </body>
</html>

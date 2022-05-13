<?php
include_once'./admin/koneksi.php';
$koneksi = Koneksi();
//cek login terdaftar atau tidak
if(isset ($_POST['login'])){
    $username = mysqli_escape_string($koneksi,$_POST['username']);
    $password =mysqli_escape_string($koneksi,$_POST['password']) ;

    //ambil data pada database (query) dan simpan dalam variabel result
    $result = mysqli_query($koneksi, "SELECT * FROM admin WHERE username ='$username' && password ='$password'");
    $cek = mysqli_fetch_array($result);
    
    // cek username dan password
    if($cek>0){
        $_SESSION=[
            'iduser'=>$cek['id'],
            'email'=>$cek['username'],
            'password'=>$cek['password'],
        ];
        // setting session
        $_SESSION['log'] = True;
        echo "<script>window.location='admin/stock_kain/stock_kain.php';</script>";
    }
    $error = true;
}
// cek jika session sdh dibuat
if(isset($_SESSION['log'])){
    echo "<script>window.location='admin/stock_kain/stock_kain.php';</script>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">

  <!-- css mind -->
  <link rel="stylesheet" href="../../style.css">
  <!-- css bootsrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<body>
   <!-- Horizontal Form -->
  <div class="card card-info">
    <div class="card-header">
      <h3 class="card-title">Login</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form class="form-horizontal" method="POST">
      <!-- cek jika username atau password salah -->
      <?php if(isset($error)) : ?>
          <div class="alert alert-danger alert-dismissible">
              Password atau Email Anda Salah!
          </div>
      <?php endif; ?>
      <div class="card-body">
        <div class="form-group row">
          <label for="inputEmail3" class="col-sm-2 col-form-label">Username</label>
          <div class="col-sm-10">
            <input type="text" name="username" class="form-control" id="inputEmail3" placeholder="Username" required>
          </div>
        </div>
        <div class="form-group row">
          <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
          <div class="col-sm-10">
            <input type="password" name="password" class="form-control" id="inputPassword3" placeholder="Password" required>
          </div>
        </div>
      </div>
      <!-- /.card-body -->
      <div class="card-footer">
        <button type="submit" name="login" class="btn btn-info">Log in</button>
        <!-- <button type="submit" class="btn btn-default float-right">Cancel</button> -->
      </div>
      <!-- /.card-footer -->
    </form>
  </div>
</body>
</html>
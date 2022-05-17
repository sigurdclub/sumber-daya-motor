<?php
session_start();//menjalankan sessions

function Koneksi()//koneksi ke database
{
  $nameServer = "localhost";
  $username = "root";
  $password = "";
  $dbName = "dayamotor_db";

  $conn = mysqli_connect($nameServer, $username, $password, $dbName);
  
  return $conn;
}

function query($query)
{
  $koneksi = Koneksi();
  $result=mysqli_query($koneksi,$query) OR die (mysqli_error($koneksi));
  $rows=[];

  foreach($result as $row){
    $rows[]=$row;
  }
  return $rows;
}

function tambahAdmin($data)
{
  $koneksi = Koneksi();

  $username=$data["username"];
  $password=$data["password"];
  
  $query="INSERT INTO admin_motor VALUES ('','$username', '$password')";
  mysqli_query($koneksi,$query);//jalankan query
  return $query;

}

function updateAdmin($data)
{
  $koneksi = Koneksi();
  $id=mysqli_real_escape_string($koneksi, $_POST["id"]);
  $nama=mysqli_real_escape_string($koneksi, $data["username"]);
  $pwd=mysqli_real_escape_string($koneksi, $data["password"]);
 
  $query="UPDATE admin_motor SET username='$nama',password='$pwd' WHERE id=$id ";
// var_dump($query);die;
  
  mysqli_query($koneksi,$query) or die(mysqli_error($koneksi));
  return mysqli_affected_rows($koneksi);
}

function hapusAdmin($id)
{
  $koneksi = Koneksi();
  $id = htmlspecialchars($_GET["id"]);
 
  $query = "DELETE FROM admin_motor WHERE id=$id ";
  // var_dump($query);die;
  mysqli_query($koneksi,$query) or die(mysqli_error($koneksi));
  return mysqli_affected_rows($koneksi);
  
}

//FUNGSI TAMBAH STOK BARANG
function tambahstok($data)
{
  $koneksi = Koneksi();

  $namaBarang=$data["merek"];
  $tipe=$data["tipe"];
  $stok=$data["stok"];
  $sat=$data["sat"];
  $file_name      = $_FILES['gambar']['name'];
  $file_tmp_name  = $_FILES['gambar']['tmp_name'];

    move_uploaded_file($file_tmp_name,'../foto/'.$file_name );

  $query="INSERT INTO stok_motor VALUES ('','$namaBarang','$tipe','$stok', '$sat', '$file_name')";
  mysqli_query($koneksi,$query);//jalankan query
  return $query;

}

//HAPUS STOK BARANG
function hapus($id)
{
  $koneksi = Koneksi();
  $id = htmlspecialchars($_GET["id"]);
 
  $query = "DELETE FROM stok_motor WHERE id_motor=$id ";
  // var_dump($query);die;
  mysqli_query($koneksi,$query) or die(mysqli_error($koneksi));
  return mysqli_affected_rows($koneksi);
  
}

//UPDATE STOK BARANG
function update($data)
{
  $koneksi = Koneksi();
  $id=mysqli_real_escape_string($koneksi, $_POST["id_motor"]);
  $nama=mysqli_real_escape_string($koneksi, $data["merek_motor"]);
  $tipe=mysqli_real_escape_string($koneksi, $data["tipe"]);
  $stok=mysqli_real_escape_string($koneksi, $data["stok"]);
  $sat=mysqli_real_escape_string($koneksi, $data["satuan"]);
  $file_name      = $_FILES['gambar']['name'];
  $file_tmp_name  = $_FILES['gambar']['tmp_name'];
    move_uploaded_file($file_tmp_name,'../foto/'.$file_name );

  $query="UPDATE stok_motor SET merek_motor='$nama', tipe='$tipe',stok='$stok', satuan='$sat',gambar='$file_name' 
                            WHERE id_motor=$id ";
// var_dump($query);die;
  
  mysqli_query($koneksi,$query) or die(mysqli_error($koneksi));
  return mysqli_affected_rows($koneksi);
}

//TAMBAH BARANG MASUK

function barangMasuk($data)
{
  $koneksi = Koneksi();

  $motor = mysqli_real_escape_string($koneksi, $data["merek_motor"]);
  $tipe = mysqli_real_escape_string($koneksi, $data["tipe"]);
  $jlh = mysqli_real_escape_string($koneksi, $data["jlh"]);
  $penerima = mysqli_real_escape_string($koneksi, $data["penerima"]);

  //cek stock yang sekarang
   $cekstokskrg = mysqli_query($koneksi,"SELECT * FROM stok_motor WHERE id_motor='$motor'");
   $ambildata= mysqli_fetch_array($cekstokskrg);

   //ambil datanya stock dri variabel ambildata lalu simpan ke var stockskrg 
   $stockskrg = $ambildata["stok"];
   //tambahkan stok skrg dgn qty
   $tambahstock= $stockskrg + $jlh;
   //  query tambah barang masuk
   mysqli_query($koneksi,"INSERT INTO motor_masuk (id,id_motor,tipe, jumlah, penerima) VALUES ('','$motor','$tipe','$jlh','$penerima')");
   // ubah data ditabel stock dgn data yg baru ditambah
   mysqli_query($koneksi,"UPDATE stok_motor SET stok ='$tambahstock' WHERE id_motor= '$motor' ");
   
   return mysqli_affected_rows($koneksi);
}

//UPDATE BARANG MASUK

function updateMasuk($data)
{
  $koneksi = Koneksi();
   $idb= mysqli_real_escape_string($koneksi, $_POST["id_motor"]);
   $idm= mysqli_real_escape_string($koneksi, $_POST["idm"]);
   $tipe= mysqli_real_escape_string($koneksi, $data["tipe"]);
   $jlh= mysqli_real_escape_string($koneksi, $data["jlh"]);
   $penerima= mysqli_real_escape_string($koneksi, $data["penerima"]);

   $lihatstock = mysqli_query($koneksi,"SELECT * FROM stok_motor WHERE id_motor='$idb'");
   $stocknya=mysqli_fetch_assoc($lihatstock);
   $stokskrg= $stocknya["stok"];

   $qtyskrg= mysqli_query($koneksi,"SELECT * FROM motor_masuk WHERE id='$idm'");
   $qtynya= mysqli_fetch_assoc($qtyskrg);
   $qtyskrg= $qtynya["jumlah"];

   if($jlh > $qtyskrg){
      $selisih= $jlh - $qtyskrg;
      $tambahin = $stokskrg + $selisih;

      //update stok di tabel stok barang
      mysqli_query($koneksi,"UPDATE stok_motor SET stok='$tambahin' WHERE id_motor='$idb'");
      //update data di tabel stok masuk
      mysqli_query($koneksi,"UPDATE motor_masuk SET tipe='$tipe', jumlah='$jlh', penerima='$penerima' WHERE id='$idm'");
 
   }else{
      $selisih= $qtyskrg - $jlh;
      $kurangin = $stokskrg - $selisih;
      //update stok di tabel stok barang
      mysqli_query($koneksi,"UPDATE stok_motor SET stok='$kurangin' WHERE id_motor='$idb'");
      //update data di tabel stok masuk
      mysqli_query($koneksi,"UPDATE motor_masuk SET tipe='$tipe', jumlah='$jlh', penerima='$penerima' WHERE id='$idm'");

   }
   return mysqli_affected_rows($koneksi);
}
//HAPUS BARANG MASUK
function hapusMasuk($id)
{
  $koneksi = Koneksi();
  $id = htmlspecialchars($_GET["id"]);
  
  mysqli_query($koneksi,"DELETE FROM motor_masuk WHERE id=$id ");
 
  return mysqli_affected_rows($koneksi);
  
}

//Fungsi Tambah Barang Keluar

function barangKeluar($data)
{
  $koneksi = Koneksi();

  $motor = mysqli_real_escape_string($koneksi, $data["merek_motor"]);
  $tipe = mysqli_real_escape_string($koneksi, $data["tipe"]);
  $jlh = mysqli_real_escape_string($koneksi, $data["jlh"]);
  $ket = mysqli_real_escape_string($koneksi, $data["ket"]);

  //cek stock yang sekarang
   $cekstokskrg = mysqli_query($koneksi,"SELECT * FROM stok_motor WHERE id_motor='$motor'");
   $ambildata= mysqli_fetch_array($cekstokskrg);

   //ambil datanya stock dri variabel ambildata lalu simpan ke var stockskrg 
   $stockskrg = $ambildata["stok"];
   //tambahkan stok skrg dgn qty
   $tambahstock= $stockskrg - $jlh;
   //  query tambah barang masuk
   mysqli_query($koneksi,"INSERT INTO motor_keluar (id,id_motor, tipe, jumlah, keterangan) VALUES ('','$motor','$tipe','$jlh','$ket')");
   // ubah data ditabel stock dgn data yg baru ditambah
   mysqli_query($koneksi,"UPDATE stok_motor SET stok ='$tambahstock' WHERE id_motor= '$motor' ");
   
   return mysqli_affected_rows($koneksi);
}

//Fungsi upadte barang keluar
function updateKeluar($data)
{
  $koneksi = Koneksi();
   $id= mysqli_real_escape_string($koneksi, $_POST["id_motor"]);
   $idk= mysqli_real_escape_string($koneksi, $_POST["idk"]);
   $tipe= mysqli_real_escape_string($koneksi, $data["tipe"]);
   $jlh= mysqli_real_escape_string($koneksi, $data["jlh"]);
   $ket= mysqli_real_escape_string($koneksi, $data["ket"]);

   $lihatstock = mysqli_query($koneksi,"SELECT * FROM stok_motor WHERE id_motor='$id'");
   $stocknya=mysqli_fetch_assoc($lihatstock);
   $stokskrg= $stocknya["stok"];

   $qtyskrg= mysqli_query($koneksi,"SELECT * FROM motor_keluar WHERE id='$idk'");
   $qtynya= mysqli_fetch_assoc($qtyskrg);
   $qtyskrg= $qtynya["jumlah"];
   
   if($jlh > $qtyskrg){
    $selisih= $jlh - $qtyskrg;
    $tambahin = $stokskrg - $selisih;
    
    //update stok di tabel stok barang
    mysqli_query($koneksi,"UPDATE stok_motor SET stok='$tambahin' WHERE id_motor='$id'");
    //update data di tabel stok masuk
    mysqli_query($koneksi,"UPDATE motor_keluar SET tipe='$tipe', jumlah='$jlh', keterangan='$ket' WHERE id='$idk'");
  }else{
    $selisih= $qtyskrg - $jlh;
    $kurangin = $stokskrg + $selisih;
    //update stok di tabel stok barang
    mysqli_query($koneksi,"UPDATE stok_motor SET stok='$kurangin' WHERE id_motor='$id'");
    //update data di tabel stok masuk
    mysqli_query($koneksi,"UPDATE motor_keluar SET tipe='$tipe', jumlah='$jlh', keterangan='$ket' WHERE id='$idk'");
  }
   
   return mysqli_affected_rows($koneksi);
}

function hapusKeluar($id)
{
  $koneksi = Koneksi();
  $id = htmlspecialchars($_GET["id"]);

  mysqli_query($koneksi,"DELETE FROM motor_keluar WHERE id=$id ");
 
  return mysqli_affected_rows($koneksi);
  
}
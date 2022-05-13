<?php
session_start();//menjalankan sessions

function Koneksi()//koneksi ke database
{
  $nameServer = "localhost";
  $username = "root";
  $password = "";
  $dbName = "kain_db";

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
  
  $query="INSERT INTO admin VALUES ('','$username', '$password')";
  mysqli_query($koneksi,$query);//jalankan query
  return $query;

}

function updateAdmin($data)
{
  $koneksi = Koneksi();
  $id=mysqli_real_escape_string($koneksi, $_POST["id"]);
  $nama=mysqli_real_escape_string($koneksi, $data["username"]);
  $pwd=mysqli_real_escape_string($koneksi, $data["password"]);
 
  $query="UPDATE admin SET username='$nama',password='$pwd'WHERE id=$id ";
// var_dump($query);die;
  
  mysqli_query($koneksi,$query) or die(mysqli_error($koneksi));
  return mysqli_affected_rows($koneksi);
}

function hapusAdmin($id)
{
  $koneksi = Koneksi();
  $id = htmlspecialchars($_GET["id"]);
 
  $query = "DELETE FROM admin WHERE id=$id ";
  // var_dump($query);die;
  mysqli_query($koneksi,$query) or die(mysqli_error($koneksi));
  return mysqli_affected_rows($koneksi);
  
}

//FUNGSI TAMBAH STOK BARANG
function tambahstok($data)
{
  $koneksi = Koneksi();

  $namaBarang=$data["nama_kain"];
  $sat=$data["satuan"];
  
  $query="INSERT INTO stok_kain VALUES ('','$namaBarang','', '$sat')";
  mysqli_query($koneksi,$query);//jalankan query
  return $query;

}

//HAPUS STOK BARANG
function hapus($id)
{
  $koneksi = Koneksi();
  $id = htmlspecialchars($_GET["id"]);
 
  $query = "DELETE FROM stok_kain WHERE idkain=$id ";
  // var_dump($query);die;
  mysqli_query($koneksi,$query) or die(mysqli_error($koneksi));
  return mysqli_affected_rows($koneksi);
  
}

//UPDATE STOK BARANG
function update($data)
{
  $koneksi = Koneksi();
  $id=mysqli_real_escape_string($koneksi, $_POST["idkain"]);
  $nama=mysqli_real_escape_string($koneksi, $data["nama_kain"]);
  $stok=mysqli_real_escape_string($koneksi, $data["stok"]);
  $sat=mysqli_real_escape_string($koneksi, $data["satuan"]);
 
  
  $query="UPDATE stok_kain SET nama_kain='$nama',stok='$stok', satuan='$sat' 
                            WHERE idkain=$id ";
// var_dump($query);die;
  
  mysqli_query($koneksi,$query) or die(mysqli_error($koneksi));
  return mysqli_affected_rows($koneksi);
}

//TAMBAH BARANG MASUK

function barangMasuk($data)
{
  $koneksi = Koneksi();

  $barang = mysqli_real_escape_string($koneksi, $data["nama_kain"]);
  $jlh = mysqli_real_escape_string($koneksi, $data["jlh"]);
  $penerima = mysqli_real_escape_string($koneksi, $data["penerima"]);

  //cek stock yang sekarang
   $cekstokskrg = mysqli_query($koneksi,"SELECT * FROM stok_kain WHERE idkain='$barang'");
   $ambildata= mysqli_fetch_array($cekstokskrg);

   //ambil datanya stock dri variabel ambildata lalu simpan ke var stockskrg 
   $stockskrg = $ambildata["stok"];
   //tambahkan stok skrg dgn qty
   $tambahstock= $stockskrg + $jlh;
   //  query tambah barang masuk
   mysqli_query($koneksi,"INSERT INTO stok_masuk (id,idkain, jumlah, penerima) VALUES ('','$barang','$jlh','$penerima')");
   // ubah data ditabel stock dgn data yg baru ditambah
   mysqli_query($koneksi,"UPDATE stok_kain SET stok ='$tambahstock' WHERE idkain= '$barang' ");
   
   return mysqli_affected_rows($koneksi);
}

//UPDATE BARANG MASUK

function updateMasuk($data)
{
  $koneksi = Koneksi();
   $idb= mysqli_real_escape_string($koneksi, $_POST["idkain"]);
   $idm= mysqli_real_escape_string($koneksi, $_POST["idm"]);
   $jlh= mysqli_real_escape_string($koneksi, $data["jlh"]);
   $penerima= mysqli_real_escape_string($koneksi, $data["penerima"]);

   $lihatstock = mysqli_query($koneksi,"SELECT * FROM stok_kain WHERE idkain='$idb'");
   $stocknya=mysqli_fetch_assoc($lihatstock);
   $stokskrg= $stocknya["stok"];

   $qtyskrg= mysqli_query($koneksi,"SELECT * FROM stok_masuk WHERE id='$idm'");
   $qtynya= mysqli_fetch_assoc($qtyskrg);
   $qtyskrg= $qtynya["jumlah"];

   if($jlh > $qtyskrg){
      $selisih= $jlh - $qtyskrg;
      $tambahin = $stokskrg + $selisih;

      //update stok di tabel stok barang
      mysqli_query($koneksi,"UPDATE stok_kain SET stok='$tambahin' WHERE idkain='$idb'");
      //update data di tabel stok masuk
      mysqli_query($koneksi,"UPDATE stok_masuk SET jumlah='$jlh', penerima='$penerima' WHERE id='$idm'");
 
   }else{
      $selisih= $qtyskrg - $jlh;
      $kurangin = $stokskrg - $selisih;
      //update stok di tabel stok barang
      mysqli_query($koneksi,"UPDATE stok_kain SET stok='$kurangin' WHERE idkain='$idb'");
      //update data di tabel stok masuk
      mysqli_query($koneksi,"UPDATE stok_masuk SET jumlah='$jlh', penerima='$penerima'WHERE id='$idm'");

   }
   return mysqli_affected_rows($koneksi);
}
//HAPUS BARANG MASUK
function hapusMasuk($id)
{
  $koneksi = Koneksi();
  $id = htmlspecialchars($_GET["id"]);
  
  mysqli_query($koneksi,"DELETE FROM stok_masuk WHERE id=$id ");
 
  return mysqli_affected_rows($koneksi);
  
}

//Fungsi Tambah Barang Keluar

function barangKeluar($data)
{
  $koneksi = Koneksi();

  $barang = mysqli_real_escape_string($koneksi, $data["nama_kain"]);
  $jlh = mysqli_real_escape_string($koneksi, $data["jlh"]);
  $sat = mysqli_real_escape_string($koneksi, $data["sat"]);

  //cek stock yang sekarang
   $cekstokskrg = mysqli_query($koneksi,"SELECT * FROM stok_kain WHERE idkain='$barang'");
   $ambildata= mysqli_fetch_array($cekstokskrg);

   //ambil datanya stock dri variabel ambildata lalu simpan ke var stockskrg 
   $stockskrg = $ambildata["stok"];
   //tambahkan stok skrg dgn qty
   $tambahstock= $stockskrg - $jlh;
   //  query tambah barang masuk
   mysqli_query($koneksi,"INSERT INTO stok_keluar (id,idkain, jumlah, satuan) VALUES ('','$barang','$jlh','$sat')");
   // ubah data ditabel stock dgn data yg baru ditambah
   mysqli_query($koneksi,"UPDATE stok_kain SET stok ='$tambahstock' WHERE idkain= '$barang' ");
   
   return mysqli_affected_rows($koneksi);
}

//Fungsi upadte barang keluar
function updateKeluar($data)
{
  $koneksi = Koneksi();
   $idb= mysqli_real_escape_string($koneksi, $_POST["idkain"]);
   $idk= mysqli_real_escape_string($koneksi, $_POST["idk"]);
   $jlh= mysqli_real_escape_string($koneksi, $data["jlh"]);
   $ket= mysqli_real_escape_string($koneksi, $data["satuan"]);

   $lihatstock = mysqli_query($koneksi,"SELECT * FROM stok_kain WHERE idkain='$idb'");
   $stocknya=mysqli_fetch_assoc($lihatstock);
   $stokskrg= $stocknya["stok"];

   $qtyskrg= mysqli_query($koneksi,"SELECT * FROM stok_keluar WHERE id='$idk'");
   $qtynya= mysqli_fetch_assoc($qtyskrg);
   $qtyskrg= $qtynya["jumlah"];
   
   if($jlh > $qtyskrg){
    $selisih= $jlh - $qtyskrg;
    $tambahin = $stokskrg - $selisih;
    //update stok di tabel stok barang
    mysqli_query($koneksi,"UPDATE stok_kain SET stok='$tambahin' WHERE idkain='$idb'");
    //update data di tabel stok masuk
    mysqli_query($koneksi,"UPDATE stok_keluar SET jumlah='$jlh', satuan='$ket' WHERE id='$idk'");
  }else{
    $selisih= $qtyskrg - $jlh;
    $kurangin = $stokskrg + $selisih;
    //update stok di tabel stok barang
    mysqli_query($koneksi,"UPDATE stok_kain SET stok='$kurangin' WHERE idkain='$idb'");
    //update data di tabel stok masuk
    mysqli_query($koneksi,"UPDATE stok_keluar SET jumlah='$jlh', satuan='$ket' WHERE id='$idk'");
  }
   
   return mysqli_affected_rows($koneksi);
}

function hapusKeluar($id)
{
  $koneksi = Koneksi();
  $id = htmlspecialchars($_GET["id"]);

  mysqli_query($koneksi,"DELETE FROM stok_keluar WHERE id=$id ");
 
  return mysqli_affected_rows($koneksi);
  
}
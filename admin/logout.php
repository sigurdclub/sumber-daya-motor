<?php
session_start();
session_destroy();//menghapus sesion
session_unset();//utk memastikan sesion dihapus
header('Location:http://localhost:8080/stock_kain/index.php');
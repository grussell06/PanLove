<?php
function connectDB()
{
  $host = "localhost";
  $db = "panlovedb";
  $user = "phpUser";
  $pwd = "PhpUser@1234";


  $attr = "mysql:host=$host;dbname=$db";
  $opts=[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
  ];
  try {
    $pdo = new PDO($attr, $user, $pwd, $opts);
    return $pdo;
  } catch (PDOException $e) 
  {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
  }
}
?>


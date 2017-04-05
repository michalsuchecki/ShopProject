<?php
ob_start();

// NAZWA SKLEPU i MAIL
define('SHOPNAME','E-Sklep WSTI');
define('SHOPMAIL','sklepwsti@gmail.com');

// ADRES NASZEJ STRONY
define('FOLDER','sklep');
$URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] = 'on') ? 'https://' : 'http://';
$URL .= $_SERVER['HTTP_HOST'];
define('PAGE_URL',$URL."/".FOLDER."/");

$SUBDIR = "Sklep";

$FOLDER = $_SERVER['DOCUMENT_ROOT'];
$FOLDER.= $SUBDIR;
$FOLDER.= "/";

define('SHOP_DIR',$FOLDER);

// GALERIA
define('FOLDER_THUMB','images/thumb/');     // Katalog miniatur
define('FOLDER_GALLERY','images/gallery/'); // Katalog obrazków
define('FOLDER_TEMP','images/temp/');       // Katalog tymczasowy
define('MAX_IMAGE_SIZE',2000000);           // Maksymalny rozmiar pliku
define('JPG_QTY',100);                      // Jakość obrazu JPG

// BAZA DANYCH
define('DB_SERVER','localhost');            // Adres bazy danych
define('DB_USERNAME','sklep_user');         // Login do bazy danych
define('DB_PASSWORD','sklep_test');         // Hasło do bazy danych
define('DB_DATABASE','sklep');              // Nazwa bazy danych
define('SALT','da1sg53o');                  // Sól

// LADOWANIE NIEZDEFINIOWANYCH KLAS
function __autoload($className) { @include_once("/../cls/$className.php"); }

if(!isset($_SESSION)) session_start(); // Zrobić to inaczej ?

$is_admin = (isset($_SESSION['zalogowany']) && $_SESSION['admin'] && $_SESSION['zalogowany'] == true && $_SESSION['admin'] == true) ? true : false;

$show_count = Array(2 => '2 przedmioty', 5 => '5 przedmiotów', 10 => '10 przedmiotów');
$status_order = Array(1 => 'Nowe zamówienie', 2 => 'W trakcie realizacji', 3 => 'Czekamy na dostawę', 4 => 'Przesyłka wysłana', 5 => 'Przesyłka dostarczona');

function update_lastviewed($ID)
{
    $cookies = Array();

    $cookies[3] = (isset($_COOKIE['last_vieved3'])) ? $_COOKIE['last_vieved3'] : NULL;
    $cookies[2] = (isset($_COOKIE['last_vieved2'])) ? $_COOKIE['last_vieved2'] : NULL;
    $cookies[1] = (isset($_COOKIE['last_vieved1'])) ? $_COOKIE['last_vieved1'] : NULL;
    $cookies[0] = (isset($_COOKIE['last_vieved0'])) ? $_COOKIE['last_vieved0'] : NULL;
    
    $t1 = $ID;
    $t2 = 0;
    for($i = 0; $i < 4; $i++)
    {
        $t2 = $cookies[$i];
        if($ID == $cookies[$i])
        {
            $cookies[$i] = $t1;
            break;  
        } 
        else
        {
            $cookies[$i] = $t1;
            $t1 = $t2;
        } 
    }

    setcookie('last_vieved0',$cookies[0],time()+3600*30);
    setcookie('last_vieved1',$cookies[1],time()+3600*30);
    setcookie('last_vieved2',$cookies[2],time()+3600*30);
    setcookie('last_vieved3',$cookies[3],time()+3600*30);
}

?>
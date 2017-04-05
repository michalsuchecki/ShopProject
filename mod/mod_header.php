<?php

require_once("/../config/config.php");

echo('
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" ""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
'); 

// CSS
echo('
<link rel="stylesheet" type="text/css" href="'.PAGE_URL.'css/shop.css" />
<link rel="stylesheet" type="text/css" href="'.PAGE_URL.'css/product.css" />
<link rel="stylesheet" type="text/css" href="'.PAGE_URL.'css/lightbox.css"  media="screen" /> 
');

// JS
echo('
<script type="text/javascript" src="'.PAGE_URL.'js/page.js"></script>
<script type="text/javascript" src="'.PAGE_URL.'js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="'.PAGE_URL.'js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="'.PAGE_URL.'js/jquery.smooth-scroll.min.js"></script>
<script type="text/javascript" src="'.PAGE_URL.'js/lightbox.js"></script>
');

$basketmng = new basketmanager;

$basket_count = $basketmng->GetItemCount();
$basket_money = $basketmng->GetTotalMoney();

echo('
<title>'.SHOPNAME.'</title>
</head>
<body>
<div id="main_contener">
	<div id="main_top">
    		<div id="main_layout">
        		<div id="menu_container">
    	    			<div id="main_layout_menu">
                			<div class="lay_menu">
                        		<a href="'.PAGE_URL.'start"       class="link_underline"> E-Sklep </a> |
                        		<a href="'.PAGE_URL.'konto"       class="link_underline"> Twoje konto </a> |
                        		<a href="'.PAGE_URL.'dostawy"     class="link_underline"> Dostawy </a> |
                        		<a href="'.PAGE_URL.'regulamin"   class="link_underline"> Regulamin </a> |
                        		<a href="'.PAGE_URL.'kontakt"     class="link_underline"> Kontakt </a>
                    		</div>
	                </div>
		        <div id="main_layout_cart">
    				<div class="cart_info" style="display:inline-block">
     			        <table>
             			    <tr>
             			        <td rowspan="2" width="100"><a href="'.PAGE_URL.'koszyk"><img src="'.PAGE_URL.'Images/cart.png" /></a></td>
                  		        <td width="200">Ilość produktów : <span>'.$basket_count.'</span> </td>
     	        		    </tr>
                 			<tr>
             			        <td>Suma <span>'.$basket_money.'</span> zł</td>
                      		</tr>
    					</table>
    				</div>
                </div>
            </div>
        </div>
		<div id="main_bar">
        	<div id="bar_search" style="display:inline-block">
                	<form method="post" action="search">
                		<label for="text">SZUKAJ: &nbsp; </label>
                		<input type="text" size="30" name="searchtext" value="Poszukiwany produkt..." />
                		<input type="submit" value="Szukaj" />
                	</form>
            	</div>
		<div id="bar_login" style="display:inline-block">
 ');
 
 // Wyswietlamy informacje o zalogowanym użytkowniku lub opcje do logowania/rejestracji
 if(isset($_SESSION) && isset($_SESSION['zalogowany']))
 {
    $login = $_SESSION['nick'];
    echo('Witaj '.$login.', kliknij by się <a href="'.PAGE_URL.'wyloguj">wylogować</a>.');
 }
 else
 {
    echo('Witamy, <a href="'.PAGE_URL.'logowanie">zaloguj się</a> lub <a href="'.PAGE_URL.'rejestracja">zarejestruj</a>');
 }
 
 echo('
		</div>
	</div>      
</div>
	<div id="main_bottom">
');

?>
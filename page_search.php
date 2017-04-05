<?php

require_once("mod/mod_header.php");

echo '<div id="main_sidemenu" style="display:inline-block">';
	include("mod/mod_catalog.php");
echo '</div>';

echo '<div id="product_contener" style="display:inline-block">';

$searchtext = (isset($_POST['searchtext'])) ? $_POST['searchtext'] : false;

if($searchtext)
{
    $search_array = explode(" ",$searchtext);
    $warunki="(nazwa LIKE '%$search_array[0]%')"; 
    for ($i=1;$i<count($search_array);$i++) 
    { 
        $warunki.=" AND (nazwa LIKE '%$search_array[$i]%')"; 
    } 
    $SQL = "SELECT * FROM produkt WHERE ".$warunki; 
    $product_list = database::QUERY($SQL);
    //var_dump($result);
    
    if($product_list)
    {
        $prod_mng = new productmanager;
        //var_dump($product_list);
        foreach($product_list as $p)
        {
            $prod_mng->ShowCatalogProduct($p);
        }
    }
    else
    {
        echo "Nie znaleziono szukanego produktu";
    }
}
else
{
    echo "Nie wpisano nic w wyszukiwarce";
}


echo '</div>';
require_once("mod/mod_footer.php");
?>
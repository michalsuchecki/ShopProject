<?php

$result = database::SELECT('produkt',Array('*'),NULL,'=','AND','dodano','DESC',4);

// Wyświetla ostatnio dodane produkty
echo('
<div id="newest_div">
    <div class="label">Ostatnio dodane</div>
');
if($result)
{
    foreach($result as $newproduct)
    {
        $thumb = database::SELECT('galeria',Array('plik'),Array('produkt_id' => $newproduct['id']));
        $price = $newproduct['cena'];
        $price = sprintf("%.2f",$price);
    
        echo('<div class="newest_box">
                <div class="newest_label">'.$newproduct['nazwa'].'</div>
                <div class="newest_photo"><img src="'.PAGE_URL.FOLDER_THUMB.$thumb[0]['plik'].'"/></div>
                <div class="newest_button"><a href="'.PAGE_URL."produkt,".$newproduct['kategoria_id'].','.$newproduct['id'].'">Szczegóły</a></div>
                <div class="newest_button"><a href="'.PAGE_URL.'koszyk,dodaj,'.$newproduct['id'].'">Do koszyka</a></div>
                <div class="newest_price">'.$price.' zł</div>       
        </div>');
    }
}
else
{
    echo('Brak nowości w sklepie');
}


// Wyświetla ostatnio przeglądane produkty
echo('</div><div id="newest_div"><div class="label">Ostatnio przeglądane</div>');

$last_vieved = Array();
for($i = 0; $i<4;$i++)
{
    if($i == 0 && isset($_COOKIE['last_vieved0'])) $last_vieved[] = $_COOKIE['last_vieved0'];
    if($i == 1 && isset($_COOKIE['last_vieved1'])) $last_vieved[] = $_COOKIE['last_vieved1'];
    if($i == 2 && isset($_COOKIE['last_vieved2'])) $last_vieved[] = $_COOKIE['last_vieved2'];
    if($i == 3 && isset($_COOKIE['last_vieved3'])) $last_vieved[] = $_COOKIE['last_vieved3'];
}

for($i = 0; $i<count($last_vieved);$i++)
{
        $product = database::SELECT('produkt',Array('*'),Array('id'=> $last_vieved[$i]));
        if($product)
        {
            $product = $product[0];
            $thumb = database::SELECT('galeria',Array('plik'),Array('produkt_id' => $product['id']));
            $price = $product['cena'];
            $price = sprintf("%.2f",$price);   
            echo('<div class="newest_box">
                <div class="newest_label">'.$product['nazwa'].'</div>
                <div class="newest_photo"><img src="'.PAGE_URL.FOLDER_THUMB.$thumb[0]['plik'].'"/></div>
                <div class="newest_button"><a href="'.PAGE_URL."produkt,".$product['kategoria_id'].','.$product['id'].'">Szczegóły</a></div>
                <div class="newest_button"><a href="'.PAGE_URL.'koszyk,dodaj,'.$product['id'].'">Do koszyka</a></div>
                <div class="newest_price">'.$price.' zł</div>    
                </div>');              
        }
}    

echo('</div>');
?>
<?php

require_once("mod/mod_header.php");
echo ('<div id="cart_concent" style="display:inline-block">');

if(isset($_GET['action']))
{
    if(!isset($_SESSION['koszyk']))
    {
        $_SESSION['koszyk'] = Array();
    }
    $basket = new basketmanager;
    switch($_GET['action'])
    {
    case "dodaj":
        $basket->AddItem($_GET['prodid']);
        if($_SERVER['HTTP_REFERER'])
        {
            header("Location:".$_SERVER['HTTP_REFERER']);
        }
        break;
    case "usun":
        $basket->RemoveItem($_GET['prodid']);
        if($_SERVER['HTTP_REFERER'])
        {
            header("Location:".$_SERVER['HTTP_REFERER']);
        }
        break;
    case "przelicz":
        $basket->Recalculate();
        header("Location:".PAGE_URL.'koszyk');
        break;
    default:
        break;
    }
}
else
{
    echo('
    <div class="label" >Twój koszyk</div>
    <div class="cart_desc cart_font">
        <div class="cart_prodID">Nazwa przedmiotu</div>
        <div class="cart_priceID">Cena za sztukę</div>
        <div class="cart_amountID">Ilość</div>
        <div class="cart_priceID">Suma</div>
    </div>
    <form method="post">
    ');
    
    $total = 0;
    $basketmng  = new basketmanager;
    $productmng = new productmanager;
    $gallerymng = new gallerymanager;
    $items = $basketmng->GetItems();
    if($items)
    {
        foreach($items as $it)
        {
            $product = $productmng->GetProduct($it['id']);
            $image = $gallerymng->GetProductThumb($it['id']);
            $price = sprintf("%.2f",$product[0]['cena']);
            $amount = (int)$it['ilosc'];
            $sum = $amount * $price;
            $sum = sprintf("%.2f",$sum);
            $total += $sum;
             echo('
            <div class="cart_item" style="display: inline-block;">  
            <div class="cart_photo"><img src="'.PAGE_URL.FOLDER_THUMB.$image.'"/></div>
            <div class="cart_info2">
                <div class="cart_title"><a href="'.PAGE_URL.'produkt,'.$product[0]['kategoria_id'].','.$product[0]['id'].'">'.$product[0]['nazwa'].'</a></div>
                <div class="cart_code code_font">Kod: '.$product[0]['kod'].'</div>
                <div><a href="'.PAGE_URL.'koszyk,usun,'.$it['id'].'">Usuń produkt</a></div>                
            </div>
            <div class="cart_box price_font">'.$price.' zł</div>
            <div class="cart_amount"><input size="5" style="text-align:center" class="amountText" type="text" name="item[0]['.$it['id'].']" value="'.$amount.'" maxlength="3"></div>
            <div class="cart_box price_font">'.$sum.' zł</div> 
            </div>           
            ');
        }
            $total = sprintf("%.2f",$total);
            echo('
            <div class="cart_summary">
                <div class="cart_summarybox price_font">'.$total.' zł</div>
                <div class="cart_summarybox cart_font">Do zapłaty:</div>
            </div> 
            <div class="cart_buttons">
                <div class="cart_button_calc"><input type="image" formaction="'.PAGE_URL.'koszyk,przelicz" name="przelicz" src="'.PAGE_URL.'images/button_przelicz.png" style="border: 0px;" alt="Przelicz"></div>
                <div class="cart_button_next"><input type="image" formaction="'.PAGE_URL.'zamowienie,potwierdzenie" name="zamow" src="'.PAGE_URL.'images/button_zamow.png" style="border: 0px;" alt="Zamów"></div>
            </div>');
    }
    else
    {
        echo('Koszyk jest pusty');
    }

}

echo ('</div>');
require_once("mod/mod_footer.php");

?>

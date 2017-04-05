<?php
require_once("mod/mod_header.php");
echo ('<div id="cart_concent" style="display:inline-block">');

if(isset($_GET['action']))
{
    switch($_GET['action'])
    {
    case "confirm":
        if($_SESSION['zalogowany'] == true)
        {
            
            $order = Array();
            $order_mng = new ordermanager;
            $user_mng = new usermanager;
            $prod_mng = new productmanager;
            $clientid = (isset($_SESSION['clientid'])) ? $_SESSION['clientid'] : 0;
            $clienttoken = (isset($_SESSION['token'])) ? $_SESSION['token'] : 0;
            $ordernumber = $order_mng->GenerateOrderUserID($clientid);
            
            $order['id'] = $ordernumber;
            echo "<p> Potwierdzenie złożenia zamówienia </p>";
            echo "<p>Twój numer zamówenia: ".$ordernumber."</p>";
            echo "<p>Lista: </p>";
            $items = $_POST['item'];
            $items = $items[0];
            foreach($items as $index => $count)
            {
                $pos = Array();
                $pos['index']=$index;
                $pos['ilosc']=$count;
                $order['items'][] = $pos;
                $name = $prod_mng->GetProduct($index);
                $name = $name[0]['nazwa'];
                echo("<p>".$name." - sztuk x".$count."</p>");
            }
            
            echo("<p> Adres wysyłki: </p>");
            $id = $_SESSION['clientid'];
            $address = $user_mng->GetUserAddress($clientid,$clienttoken);
            
            $ship_address = "<p>".$address['imie']." ".$address['nazwisko']."</p>";
            $ship_address.= "<p>".$address['ulica']." ".$address['dom']."/".$address['lokal']."</p>";
            $ship_address.= "<p>".$address['kod']."-".$address['kod2']." ".$address['miasto'].", ".$address['wojewodztwo']."</p>";
            
            echo($ship_address);
            
            $order['clientid'] = $id;
            $order['adres'] = $ship_address;
            
            $_SESSION['zamowienie'] = $order;
            
            //echo('<a href="'.PAGE_URL.'koszyk"> Powrót </a> <a href="'.PAGE_URL.'zamowienie,wyslij">Potwierdź</a>');
            //ButtonPrev
            echo('<div class="ButtonContainer">');
            echo('<div class="ButtonNext ButtonFont"><a href="'.PAGE_URL.'zamowienie,wyslij">Potwierdź</a></div>');
            echo('<div class="ButtonPrev ButtonFont"><a href="'.PAGE_URL.'koszyk">Powrót</a></div>');
            echo('</div>');
            
        }
        else
        {
            header("Location: ".PAGE_URL."logowanie");
        }
        break;    
    case "send":
    {
        $order_mng = new ordermanager;
        $result = $order_mng->CreateOrder();
        if($result)
        {
            echo '<p>Dziękujemy za dokonanie zakupu</p>';
            echo 'Twoje zamówienie jest w trakcje realizacji. Kliknij <a href="'.PAGE_URL.'">tutaj</a> by powrócić na stronę główną';
        } 
        else
        {
            echo '<p>Wystąpił nieoczekiwany bład. Kliknij <a href="'.PAGE_URL.'">tutaj</a> by powrócić na stronę główną';           
        }
    }
    break;
    default:
    break;
    }
}

echo ('</div>');
require_once("mod/mod_footer.php");
?>
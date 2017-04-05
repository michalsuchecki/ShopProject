<?php

require_once("/../config/config.php");



class ordermanager
{
    /* Zwraca ID następnego elementu w tabeli */
    function GenNextOrderID()
    {
        $nextid = database::QUERY("SHOW TABLE STATUS LIKE 'zamowienie'");
        $nextid = $nextid[0]['Auto_increment'];
        return $nextid;
    }
    function GenerateOrderUserID($USERID)
    {
        // TODO: zrobić by nr zamówienia był zerowany co nowy dzień
        /*
            1-6  - ID KLIENTA
            7-14 - DATA
           15-16 - NRZAMOWIENIA
        */ 
        $result = database::SELECT('zamowienie',Array('count(*)'),Array('uzytkownicy_id' => $USERID));
        $number = $result[0]['count(*)'];
        $number = sprintf("%02d",$number);
        $order_number = sprintf("%06d",$USERID);
        $data = date("Ymd");
        $order_number.=$data;
        $order_number.=$number;
        return $order_number;
    }
    function CreateOrder()
    {  
        if(isset($_SESSION['zamowienie']))
        {
            $order = $_SESSION['zamowienie'];
            //var_Dump($order);
            // ==============================
            $clientid = $order['clientid'];
            $orderid = $order['id'];
            $address = $order['adres'];
            $data = date("Y-m-d");
            $status = 1;
            $items = $order['items'];
            $prod_mng = new productmanager;
            $price = 0;
            $total = 0;
            foreach($items as $i)
            {
                $price = $prod_mng->GetProductPrice($i['index']);
                $price = $price*$i['ilosc'];
                $total+= $price;
            }
            
            $nextorder_id = $this->GenNextOrderID();

            $result = database::INSERT('zamowienie',Array('uzytkownicy_id' => $clientid,'zam_nr' => $orderid,
                                  'zam_data' => $data,'zam_status' => $status,'cena' => $total));
            if($result)
            {
                foreach($items as $i)
                {
                    database::INSERT('pozycja',Array('produkt_id' => $i['index'],'zamowienie_id' => $orderid,
                                      'ilosc' => $i['ilosc']));
                }
                $mail_mng = new mailmanager;
                $mail_mng->SendOrderInfo("harukapl@gmail.com",$orderid,$items,$address); 
                $mail_mng->SendOrderStatusChange("harukapl@gmail.com",$orderid,$status);
            }  
               
            $_SESSION['koszyk'] = Array();
            unset($_SESSION['zamowienie']);
            return true;
        }
        else return false;
        
    }
    /* Zwraca wszystkie zamówienia które wymagają przetworzenia*/
    function GetOrdersToProcess()
    {
        return database::SELECT('zamowienie',Array('*'),Array('zam_status' => 3),"<=");
    }
    /*Zwraca nazwy i ilość pozycji dla danego zamówienia*/
    function GetPositionData($ORDERID)
    {
        $result = database::SELECT('pozycja',Array('produkt_id,ilosc'),Array('zamowienie_id' => $ORDERID));
        if($result)
        {
            $prod_table = Array();
            foreach($result as $r)
            {
                
                $product = database::SELECT('produkt',Array('nazwa,cena'),Array('id' => $r['produkt_id'])); 
                $product = $product[0];
                $item['nazwa'] = $product['nazwa'];
                $item['cena'] = $product['cena'];
                $item['ilosc'] = $r['ilosc'];
                $prod_table[] = $item;
            }
            return $prod_table;
        }
        else return false;
    }
    /* Zwraca wszystkie zamówienia klienta */
    function GetOrderByClientID($ID,$ACTIVE)
    {
        if($ACTIVE)
        {
            $result = database::SELECT('zamowienie',Array('*'),Array('uzytkownicy_id' => $ID));
            if($result)
            {
                foreach($result as $r)
                {
                    if($r['zam_status'] != 5)
                    {
                        $this->ShowUserListItem($r['uzytkownicy_id'],$r['zam_nr'],$r['zam_data'],$r['zam_status'],$r['cena']);
                    }
                }
            }
            else echo ("Brak aktualnie żadnych zamówień");
        }
        else
        {
            $result = database::SELECT('zamowienie',Array('*'),Array('uzytkownicy_id' => $ID, 'zam_status' => 5));
            if($result)
            {
                foreach($result as $r)
                {
                    $this->ShowUserListItem($r['uzytkownicy_id'],$r['zam_nr'],$r['zam_data'],$r['zam_status'],$r['cena']);
                }
            }
            else echo ("Brak aktualnie żadnych zamówień");            
        }
    }
    /* Zwraca zamówienia dla danego statusu */
    function GetOrderByStatus($STATUS)
    {
        $STATUS = (int) $STATUS; 
        $result = database::SELECT('zamowienie',Array('*'),Array('zam_status' => $STATUS));
        if($result)
        {
            foreach($result as $r)
            {
                $this->ShowOrderListItem($r['uzytkownicy_id'],$r['zam_nr'],$r['zam_data'],$r['zam_status'],$r['cena']);
            }
        }
    }
    /* Aktualizuje status zamówienia i zwraca id klienta */
    function UpdateOrderStatus($ORDERID,$STATUS)
    {
        $result = database::UPDATE('zamowienie',Array('zam_status' => $STATUS),array('zam_nr' => $ORDERID));
        if($result)
        {
            $id = database::SELECT('zamowienie',Array('uzytkownicy_id'),Array('zam_nr' => $ORDERID));
            return $id[0]['uzytkownicy_id'];
        }
        else return false;
    }
    /* Zwraca  tablicę - ilosc i id_produktu z pozycji dla danego zamówienia */
    function GetProductByOrder($ORDERID)
    {
        return database::SELECT('pozycja',Array('produkt_id','ilosc'),Array('zamowienie_id' => $ORDERID));
    }
    function ShowUserListItem($USERID,$ORDERID,$DATA,$STATUS,$CENA)
    {
        $token = database::SELECT('uzytkownicy',Array('token'),Array('id' => $USERID));
        $token = $token[0]['token'];
        $user_mng = new usermanager;
        $address = $user_mng->GetUserAddress($USERID,$token);
        $status_order = Array(1 => 'Nowe zamówienie', 2 => 'W trakcie realizacji', 3 => 'Czekamy na dostawę', 4 => 'Przesyłka wysłana', 5 => 'Przesyłka dostarczona');
        
        echo('<div class="listorder_div"><div class="listorder_header">Zamówienie nr: '.$ORDERID.' - data zamówienia: '.$DATA.'</div><div class="listorder_desc">');
        
        $prod_mng = new productmanager;
        
        $position = $this->GetProductByOrder($ORDERID);

        foreach($position as $p)
        {
            $name = $prod_mng->GetProductName($p['produkt_id']);
            $count = $p['ilosc'];
            echo('- '.$name.' - ilość: '.$count.'<br/>');
        }
        $wartosc = sprintf("%.2f",$CENA);
        echo('<br/>Do zapłaty: '.$wartosc.' zł<br/><br/>Adres wysyłki: <br/><br/>');
        
        $ship_address = $address['imie']." ".$address['nazwisko']."<br/>";
        $ship_address.= $address['ulica']." ".$address['dom']."/".$address['lokal']."<br/>";
        $ship_address.= $address['kod']."-".$address['kod2']." ".$address['miasto'].", ".$address['wojewodztwo']."<br/><br/>";
        
        echo($ship_address);        
        echo('</div><div class="listorder_status">');
        echo('<p>Status zamówienia:</p>');
        echo('<p>'.$status_order[$STATUS].'</p>');
       
        echo('
                </div>
            </div>');        
     
    }
    function ShowOrderListItem($USERID,$ORDERID,$DATA,$STATUS,$CENA)
    {
        $token = database::SELECT('uzytkownicy',Array('token'),Array('id' => $USERID));
        $token = $token[0]['token'];
        $user_mng = new usermanager;
        $address = $user_mng->GetUserAddress($USERID,$token);
         
        $status_order = Array(1 => 'Nowe zamówienie', 2 => 'W trakcie realizacji', 3 => 'Czekamy na dostawę', 4 => 'Przesyłka wysłana', 5 => 'Przesyłka dostarczona');
        
        echo('<div class="listorder_div"><div class="listorder_header">Zamówienie nr: '.$ORDERID.' - data zamówienia: '.$DATA.'</div><div class="listorder_desc">');
        
        $prod_mng = new productmanager;
        
        $position = $this->GetProductByOrder($ORDERID);

        foreach($position as $p)
        {
            $name = $prod_mng->GetProductName($p['produkt_id']);
            $count = $p['ilosc'];
            echo('- '.$name.' - ilość: '.$count.'<br/>');
        }
        $wartosc = sprintf("%.2f",$CENA);
        echo('<br/>Do zapłaty: '.$wartosc.' zł<br/><br/>Adres wysyłki: <br/><br/>');
        
        $ship_address = $address['imie']." ".$address['nazwisko']."<br/>";
        $ship_address.= $address['ulica']." ".$address['dom']."/".$address['lokal']."<br/>";
        $ship_address.= $address['kod']."-".$address['kod2']." ".$address['miasto'].", ".$address['wojewodztwo']."<br/><br/>";
        
        echo($ship_address);        
        echo('</div><div class="listorder_status"><form method="post" name="order"><label>Status zamówienia:</label>');
        echo('<select name="orderid['.$ORDERID.']">');
        
        foreach($status_order as $key => $val)
        {
            if($STATUS == $key)
            {
                echo('<option value="'.$key.'" selected>'.$val.'</options>');
            }
            else
            {
                echo('<option value="'.$key.'">'.$val.'</options>');            
            }
        }
       
        echo('
                        </select>
                        <input name="change_status" value="1" hidden="true"/>
                        <input type="submit" value="Zmień"/> 
                    </form>
                </div>
            </div>');
    }
}
<?php

require_once("/../config/config.php");

/*
 BUGI: Brak ?
*/

class basketmanager
{
    /* Dodaje przedmiot do koszyka */
    function AddItem($ID)
    {
        // jeœli nie istnieje tworzy zmienn¹ 
        
        if(!isset($_SESSION['koszyk']))
        {
            $_SESSION['koszyk'] = Array();
            $userid =  $_SESSION['clientid'];
            
            $order_mng = new ordermanager;
            $order_mng->GenerateOrderIDforUser($userid);
            
            $_SESSION['nr_zamowienia'] = 0;
        }
        
        if(array_key_exists($ID,$_SESSION['koszyk']))
        {
            $_SESSION['koszyk'][$ID]['ilosc']++;
        }
        else
        {
            $_SESSION['koszyk'][$ID] = Array();
            $_SESSION['koszyk'][$ID]['id'] = $ID;
            $_SESSION['koszyk'][$ID]['ilosc'] = 1;
        }
    }
    /* Usuwa wszystkie przedmioty z koszyka */ 
    function RemoveAllItems()
    {
        $_SESSION['koszyk'] = Array();
    }
    /* Usuwa przedmiot z koszyka o podanym ID */
    function RemoveItem($ID)
    {
        if(array_key_exists($ID,$_SESSION['koszyk']))
        {
            unset($_SESSION['koszyk'][$ID]);
        }
    }
    /* Przelicza zawartoœæ koszyka */
    function Recalculate()
    {
        if(isset($_POST['item']))
        {
            $items = $_POST['item'];
            foreach($_POST['item'][0] as $key => $item)
            {
                if(isset($_SESSION['koszyk']))
                {
                    if( $item == 0)
                    {
                        unset($_SESSION['koszyk'][$key]);
                    }
                    else
                    {
                        $_SESSION['koszyk'][$key]['ilosc'] = $item;
                    }                   
                }
            }
        }
    }
    /* Pobiera wszystkie przedmioty z koszyka */
    function GetItems()
    {
        if(isset($_SESSION['koszyk']))
        {
            return $_SESSION['koszyk'];
        }
        else return false;
    }
    /* Ustawia odpowiedni¹ iloœæ w koszyku */
    function SetAmount($ID,$AMOUNT)
    {
        if(array_key_exists($ID,$_SESSION['koszyk']))
        {
            $_SESSION['koszyk'][$ID]['ilosc'] = $AMOUNT;
        }
    }
    /* Zwraca iloœæ przedmiotów w koszyku */
    function GetItemCount()
    {
        if(isset($_SESSION['koszyk']))
        {
            return count($_SESSION['koszyk']);
        }
        else 
        {
            return 0;
        }
    }
    /* Zwraca wartoœæ koszyka */
    function GetTotalMoney()
    {
        $productmng = new productmanager;
        $total_price = 0;
        if(isset($_SESSION['koszyk']))
        {
            foreach($_SESSION['koszyk'] as $item)
            {
                $total_price += $productmng->GetProductPrice($item['id'])*$item['ilosc'];
            }
        }
        $total_price = sprintf("%.2f",$total_price);
        return $total_price;
    }
}

?>
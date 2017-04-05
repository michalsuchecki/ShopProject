<?php

require_once("/../config/config.php");

class productmanager
{
    function AddProduct($PRODUCTINFO,$CATEGORYID,$IMAGES)
    {
        $nazwa = (isset($_POST['nazwa'])) ? $_POST['nazwa'] : NULL;
        $kod   = (isset($_POST['kod'])) ? $_POST['kod'] : NULL;
        $opis1 = (isset($_POST['opis'])) ? $_POST['opis'] : NULL;
        $opis2 = (isset($_POST['krotki_opis'])) ? $_POST['krotki_opis'] : NULL;
        $cena  = (isset($_POST['cena'])) ? $_POST['cena'] : NULL;
        $cat   = (isset($_GET['catid'])) ? $_GET['catid'] : NULL;
            
        $nazwa_ok   = (strlen($nazwa) <= 100) ? true : false;
        $kod_ok     = (strlen($kod) <= 10) ? true : false;
        $cena_ok    = is_numeric((float)$cena);
        $cat_ok     = is_int((int)$cat); 
        $opis1_ok    = ((strlen($opis1) <= 2000) && !is_null($opis1)) ? true : false;
        $opis2_ok    = ((strlen($opis2) <= 600) && !is_null($opis2)) ? true : false;
            
        $gallery = new gallerymanager;

        // ID produktu
        $nextid = database::QUERY("SHOW TABLE STATUS LIKE 'produkt'");
        $nextid = $nextid[0]['Auto_increment'];
        /*
        var_dump($nazwa_ok);
        var_dump($kod_ok);
        var_dump($cena_ok);
        var_dump($cat_ok);
        var_dump($opis1_ok);
        var_dump($opis2_ok);
        */    
        if($nazwa_ok && $kod_ok && $cena_ok && $cat_ok && $opis1_ok && $opis2_ok) // czy wszystko ok
        {
            $cena = str_replace(',','.',$cena);
            $czas = date("Y-m-d");
            /*
            var_dump($opis1);
            var_dump($opis2);
            */
            $opis1 = str_replace("'","\'",$opis1);
            $opis2 = str_replace("'","\'",$opis2);
            /*
            var_dump($opis1);
            var_dump($opis2);            
            */
            
            $result = database::SELECT('produkt',Array('kod'),Array('kod' => $kod));
            
            var_dump($result);
            if(!$result)
            {
                database::INSERT('produkt',array('kategoria_id' => $cat,'nazwa' => $nazwa,
                            'opis' => $opis1,'krotki_opis' => $opis2, 'cena' => $cena,'poprzednia_cena' => 0,
                            'promocja' => false, 'kod' => $kod, 'ocena' => 0, 'dodano' => $czas));
                $gallery->UploadImage($_FILES,$nextid);
                return true;
            }
            else return false; // Produkt istnieje
            
        }            
        else return false;
    }
    /* Usuwa produkt, obrazki i komentarze z nim związane */
    function RemoveProduct($ID)
    {
        $g_mng = new gallerymanager;
        $g_mng->RemoveImage($ID);
        
        $c_mng = new commentmanager;
        $c_mng->RemoveProductComment($ID);
        
        return database::DELETE('produkt',Array('id' => $ID));    
    }
    /* Zmienia informacje o produkcie */
    function ModifyProduct($ID,$INFO)
    {
        $price = (isset($INFO['cena'])) ? $INFO['cena'] : NULL;
        $oldprice = database::SELECT('produkt',Array('cena'),Array('id' => $ID));
      
        if($oldprice && $price)
        {
            if($price != $oldprice)
            {
                $oldprice = sprintf("%.2f",$oldprice);
                $price = sprintf("%.2f",$price);
                $this->ChangePrice($ID,$price);
            }
        }
        
        $promotion = (isset($INFO['promocja'])) ? $INFO['promocja'] : 0;
        
        // TODO: Dodatkowe zmiany nt promocji.
        $this->SetPromotion($ID,$promotion);
        
        // Zmiana informacji
        $opis = (isset($INFO['opis'])) ? $INFO['opis'] : false;

        // Problem z ( ' ) w tekscie :/
        $opis = str_replace("'","\'",$opis);
     
        if($opis) database::UPDATE('produkt',Array('opis' => $opis),Array('id' => $ID)); 
        
        $tytul = (isset($INFO['title'])) ? $INFO['title'] : false;
        if($tytul) database::UPDATE('produkt',Array('nazwa' => $tytul),Array('id' => $ID));
        
        return true;       
    }
    /* Ustawienie zmiennej promocja */
    private function SetPromotion($ID, $VALUE)
    {
        if((int)$VALUE > 0) $VALUE = 1;
        $result = database::SELECT('produkt',Array('id'),Array('id' => $ID));
        if($result)
        {
            return database::UPDATE('produkt',Array('promocja' => $VALUE),Array('id' => $ID));
        }
        else return false;
    }
    /* Zmiana ceny */
    private function ChangePrice($ID, $NEWPRICE)
    {
        $oldprice = database::SELECT('produkt',Array('cena'),Array('id' => $ID));
        if($oldprice) $oldprice = $oldprice[0]['cena'];
        
        $result = database::UPDATE('produkt',Array('poprzednia_cena' => $oldprice),Array('id'=> $ID));
        if($result)
        {
            return database::UPDATE('produkt',Array('cena' => $NEWPRICE),Array('id'=> $ID));
        }
        else return false;
    }
    /* Aktualizacja ceny */
    private function UpdatePrice($ID,$COLUMN, $NEWPRICE)
    {
        return database::UPDATE('produkt',Array($COLUMN => $NEWPRICE), Array('id' => $ID));
    }
    /* Zwraca wszystkie informacje o produkcie */
    function GetProduct($ID)
    {
        return database::SELECT('produkt',Array('*'),Array('id' => $ID),'=','AND','id','ASC',1);
    }
    /* Zwraca cene danego produktu */
    function GetProductPrice($ID)
    {
        $price = 0;
        $result = database::SELECT('produkt',Array('*'),Array('id' => $ID),'=','AND','id','ASC',1);
        if($result)
        {
            $price = $result[0]['cena'];
        }
        else
        {
            $price = 0;
        }
        $price = sprintf("%.2f",$price);
        return $price;
    }
    /* Zwraca nazwe danego produktu */
    function GetProductName($ID)
    {
        $result = database::SELECT('produkt',Array('id,nazwa'),Array('id' => $ID),'=','AND','id','ASC',1);
        if($result)
        {
            $name = $result[0]['nazwa'];
        }
        else
        {
            $name = "";
        }
        return $name;        
    }
    /* Zwraca ocenę przedmiotu */
    function GetProductRate($ID)
    {
        $rate_result = database::SELECT('komentarze',Array('ocena'),Array('produkt_id' => $ID));
        if($rate_result)
        {
            $div = count($rate_result);
            $ratio = 0;
            foreach($rate_result as $rate)
            {
                $ratio+= $rate['ocena'];
               
            }
            $ratio = (float)$ratio/$div;
            $ratio = sprintf("%.1f",$ratio);
            return $ratio;
        }
        else return 0;
    }
    /* Wyświetla przedmiot w postaci bloku */ 
    function ShowCatalogProduct($PRODUCT)
    {
        $gallery_mng = new gallerymanager;
        $thumb = $gallery_mng->GetProductThumb($PRODUCT['id']);
        if($PRODUCT['poprzednia_cena'] > 0)
        {
            $old_price = $PRODUCT['poprzednia_cena'];
            $old_price =  sprintf("%.2f",$old_price);
            $old_price.= " zł";
        }
        else
        {
            $old_price = "";
        }        
        $price =  sprintf("%.2f",$PRODUCT['cena']);
        
        $rate = $this->GetProductRate($PRODUCT['id']);
         echo('
        <div class="productBox">
            <div class="productBoxL">
                <div class="productPhoto"><img src="'.PAGE_URL.FOLDER_THUMB.$thumb.'"></div>
                <div class="productRatio">Ocena: '.$rate.' / 5</div>
            </div>
            <div class="productBoxR">
                <div class="productHeader">
        ');
        
        if(usermanager::IsAdmin())
        {
            echo('<div class="productTitle"><a href="'.PAGE_URL.'produkt,usun,'.$PRODUCT['id'].'">[Usuń]</a> '.$PRODUCT['nazwa'].'</div>');
        }
        else
        {
            echo('<div class="productTitle">'.$PRODUCT['nazwa'].'</div>');
        }

        echo('<div class="ProductPromotion">');
        if($PRODUCT['promocja'])
        {
            echo "Promocja !";
        }
        echo('</div>');

        echo('                    
                    <div class="productCode">Kod produktu: '.$PRODUCT['kod'].'</div>
                </div>
                <div class="productDesc">'.$PRODUCT['krotki_opis'].'</div>
                <div class="productPriceBox">
                    <div class="productOldPrice">'.$old_price.'</div>
                    <div class="productPrice">'.$price.' zł</div>
                    <div class="productButtonBox">
                        <div class="productButton"><a href="'.PAGE_URL.'koszyk,dodaj,'.$PRODUCT['id'].'">Do koszyka</a></div>
                        <div class="productButton"><a href="'.PAGE_URL.'produkt,'.$PRODUCT['kategoria_id'].','.$PRODUCT['id'].'">Szczegóły</a></div>
                    </div>
                </div>
            </div>
        </div>    
    ');        
    }
}

?>

<?php

require_once("/../config/config.php");

class mailmanager
{
    private function GenerateHeader()
    {
        return "MIME-Versio: 1.0\n"."Content-type: text/html; charset=UTF-8\nFrom: ".SHOPMAIL."\n";
    }
    function SendActivationMail($MAIL,$CODE)
    {
        $header = $this->GenerateHeader();
        $title = SHOPNAME.' - link aktywacyjny';
        $text = '<html><head><title>Link aktywacyjny</title></head><body>';
        $text.= 'Dziekujemy za zarejestrowanie sie w sklepie '.SHOPNAME.'.<br/>';
        $text.= "W celu potwierdzenia rejestracji nalezy kliknac w podany link: <a href=\"".PAGE_URL."aktywuj/".$CODE."\">Aktywuj konto</a><br/><br/>";
        $text.= 'Zyczymy udanych zakupów !';
        $text.='</body></html>';
        //echo($text);
        mail($MAIL,$title,$text,$header);
    }
    function SendNewPassword($MAIL,$PASS)
    {
        $header = $this->GenerateHeader();
        $title = SHOPNAME.' - Nowe haslo';
        $text = '<html><head><title>Nowe haslo</title></head><body>';
        $text.= 'Sklep '.SHOPNAME.' - wygenerowalismy nowe haslo.<br/><br/>';
        $text.= 'Login: '.$MAIL.'<br/>';
        $text.= 'Haslo: '.$PASS.'<br/><br/>';
        $text.= 'Mozesz je zmienic po zalogowaniu sie w zakladce "ustawienia konta".';
        $text.="</body></html>";
        //echo($text);
        mail($MAIL,$title,$text,$header);                
    }
    function SendOrderInfo($MAIL,$ORDERNUMBER,$ITEMS,$ADDRESS)
    {
        $header = $this->GenerateHeader();
        $title = SHOPNAME.' - Zamówienie '.$ORDERNUMBER;
        $text = '<html><head><title>Zamówienie nr. '.$ORDERNUMBER.'</title></head><body>';
        
        $text.= '<p>Twój numer zamówenia: <b>'.$ORDERNUMBER.'</b></p>';
        $text.= '<p>Lista: </p>';
        
        // LISTA PRZEDMIOTÓW:
        $prod_mng = new productmanager;
        $total = 0;
        foreach($ITEMS as $i)
        {
            $count = $i['ilosc'];
            $price = $prod_mng->GetProductPrice($i['index']);
            $name  = $prod_mng->GetProductName($i['index']);
            $text.='<p>'.$count.' x '.$name.' - '.$price.' sztuk</p>';
            $total+=$price;
        }
        $total = sprintf("%.2f",$total);
        
        $text.='razem do zapłaty:'.$total.' zł<br/><br/>';
        
        // ADRES:
        
        $text.="<p><b>Adres wysyłki:</b></p>";
        $text.= $ADDRESS;
        $text.="</body></html>";
        
        mail($MAIL,$title,$text,$header);  
        
    }
    function SendOrderStatusChange($MAIL,$ORDERID,$STATUS)
    {
        $status_order = Array(1 => 'Nowe zamówienie', 2 => 'W trakcie realizacji', 3 => 'Czekamy na dostawę', 4 => 'Przesyłka wysłana', 5 => 'Przesyłka dostarczona');
        $header = $this->GenerateHeader();
        $title = SHOPNAME.' - Zamówienie '.$ORDERID.' - Zmiana statusu';
        $text = '<html><head><title>Zamówienie nr. '.$ORDERID.' - Zmiana statusu</title></head><body>';
        $text.='Informujemy że twoje zamówienie nr '.$ORDERID.' zmieniło status na <b>';
        $text.= $status_order[$STATUS]; 
        $text.="</b></body></html>";
        mail($MAIL,$title,$text,$header);         
    }
}

?>

<?php

require_once("/../config/config.php");

class commentmanager
{
    /* Sprawdza czy komentarz zosta³ dodany */
    function CommentIsAdded($PRODID,$MEMBERID)
    {
        $result = database::SELECT('komentarze',Array('*'),Array('produkt_id' => $PRODID,'uzytkownicy_id' => $MEMBERID));
        if($result)
        {
            return true;
        }
        else return false;
    }
    /* Dodaje komentarz */
    function AddCommment($PRODID,$MEMBERID,$TITLE,$TEXT,$RATE)
    {
        $result = $this->CommentIsAdded($PRODID,$MEMBERID);
        if(!$result)
        {
            //TODO: WALIDACJA

            // ------------
            $data = date("Y-m-d");
            $result = database::INSERT('komentarze',Array('produkt_id' => $PRODID,'uzytkownicy_id' => $MEMBERID,'tytul' => $TITLE,
                                  'tresc' => $TEXT, 'ocena' => $RATE,'czas' => $data));
            return $result;
        }
    }
    /* Usuwa komentarz */
    function RemoveComment($PRODID,$MEMBERID)
    {
        return database::DELETE('komentarze',Array('produkt_id' => $PRODID,'uzytkownicy_id' => $MEMBERID));
    }
    /* Usuwa wszystkie komentarze dla danego produktu */
    function RemoveProductComment($PRODID)
    {
        return database::DELETE('komentarze',Array('produkt_id' => $PRODID));
    }
    /* Zwraca tablicê komentarzy */
    function GetComments($PRODID)
    {
        return database::SELECT('komentarze',Array('*'),Array('produkt_id' => $PRODID),"=","AND",'czas',"DESC");
    }
}

?>
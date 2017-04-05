<?php

class usermanager
{
    /* Sprawdzamyczy jesteśmy zalogowani */
    public static function IsLogged()
    {
        return (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true);
    }
    /* Spradzamy czy użytkownik ma uprawnienia administratora */
    public static function IsAdmin()
    {
        session_regenerate_id();
        if(self::IsLogged())
        {
            if(isset($_SESSION['admin']) && $_SESSION['admin'] == true)
            {
                return true;
            }
            else return false;
        }
        else return false;
    }
    /* Sprawdza czy ID i TOKEN jest poprawny */
    public static function ValidateUser($ID,$TOKEN)
    {
        $result = database::SELECT('uzytkownicy',Array('id'),Array('id' => $ID,'token' => $TOKEN));
        if($result) return true;
        else return false;
    }
    public static function IsUserExist($MAIL)
    {
        $result = database::SELECT("uzytkownicy",Array("email"),Array('email' => $MAIL));
        if($result)
        {
            return true;   
        }
        else
        {
            return false;
        }
    }
    public static function IsUserActive($MAIL)
    {
        $result = database::SELECT('uzytkownicy',Array('aktywny'),Array('email' => $MAIL));
        $result = (int)$result[0]['aktywny'];
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function Login($LOGIN, $PASSWORD)
    {
        $pass_hash = md5($PASSWORD.SALT);
        
        $result = database::SELECT("uzytkownicy",Array("token","imie","uprawnienia","aktywny","id"),Array('email' => $LOGIN,'haslo' => $pass_hash));
    
        if($result)
        {
            if($result[0]['aktywny'])
            {
                if(isset($_SESSION))
                {
                    $_SESSION['zalogowany'] = true;
                    $_SESSION['clientid'] =  $result[0]['id'];
                    $_SESSION['token'] = $result[0]['token'];
                    $_SESSION['nick'] = $result[0]['imie'];
                    $_SESSION['admin'] = $result[0]['uprawnienia'];
                }
                return true;
            }
            else
            {
                echo 'Twoje konto nie jest aktywne';
                return false;
            }
        }
        else
        {
            return false;
        }                      
    }
    /* Wylogowanie użytkownika */
    public function Logout()
    {
     	if(self::IsLogged())
    	{
    		$_SESSION = array();
    	}
    	@session_destroy();       
    }
    /* Rejestracja użytkownika */
    public function RegisterUSER($INFO)
    {
        $error = false;
        
        // WALIDACJA
        if(isset($INFO['email']) && isset($INFO['pass1']) && isset($INFO['pass2']) && isset($INFO['imie']) && 
           isset($INFO['nazwisko']) && isset($INFO['ulica']) && isset($INFO['mieszkanie']) && isset($INFO['lokal']) &&
           isset($INFO['kod_1']) && isset($INFO['kod_2']) && isset($INFO['miasto']))
        {
            $var_email      = $INFO['email'];
            $var_pass1      = $INFO['pass1'];
            $var_pass2      = $INFO['pass2'];
            $var_imie       = $INFO['imie'];
            $var_nazwisko   = $INFO['nazwisko'];
            $var_ulica      = $INFO['ulica'];
            $var_mieszkanie = $INFO['mieszkanie'];
            $var_kod1       = $INFO['kod_1'];
            $var_kod2       = $INFO['kod_2'];
            $var_miasto     = $INFO['miasto'];
            $var_lokal      = $INFO['lokal'];
            $var_wojewodztwo= $INFO['woj'];
            
            // DANE LOGOWANIA
            $var_email= strtolower($var_email);
            if(!preg_match("/[a-z0-9.\-_]+@[a-z0-9\-.]+\.[a-z]{2,4}/",$var_email)) $error = true;
            if(!preg_match("/[a-zA-Z0-9]{6,16}/",$var_pass1)) $error = true;
            if(strcmp($var_pass1,$var_pass2)) $error = true;
            
            // DANE WYSYLKI
            $var_imie= strtolower($var_imie);
            if(!preg_match("/^[a-zęóąśłżźćń]{3,20}$/D",$var_imie)) $error = true;
            $var_imie = ucwords($var_imie);
            
            $var_nazwisko = strtolower($var_nazwisko);
            if(!preg_match("/^[a-zęóąśłżźćń]{3,20}$/D",$var_nazwisko)) $error = true;
            $var_nazwisko = ucwords($var_nazwisko);
            
            $var_ulica= strtolower($var_ulica);
            if(!preg_match("/^([a-ząśćęółń]{3,20}[ ]{0,1}){1,3}$/",$var_ulica)) $error = true;
            $var_ulica = ucwords($var_ulica);
        
            if(!preg_match("/^([0-9]{1}[a-zA-Z]{0,4}){1,5}$/",$var_mieszkanie)) $error = true;
            if(!preg_match("/^([0-9]){0,5}$/",$var_lokal)) $error = true;
        
            if(!preg_match("/^(\d{2})$/",$var_kod1)) $error = true;
            if(!preg_match("/^(\d{3})$/",$var_kod2)) $error = true;
        
            $var_miasto= strtolower($var_miasto);
            if(!preg_match("/^([a-ząśćęółń]{3,20}[ ]{0,1}){1,3}$/",$var_miasto)) $error = true;
            $var_miasto = ucwords($var_miasto);
            
            if($var_wojewodztwo < 1 && $var_wojewodztwo > 16) $error =true;
        }
        else // Zostanie wywołane gdy błędne dane się pojawią 
        {
            $error = true;   
        }
        
        // REJESTRACJA - Do poprawy
                
        if($error == true)
        {
            die("Wystapil nieoczekiwany blad :(");
        }
        else
        {
            if(!self::IsUserExist($var_email))
            {
                 // Klucz aktywacyjny
                $key = $var_email;
                $salt= getdate();
                $key.= $salt[0];
                $key = md5($key);
                
                // Haslo
                $haslo = $var_pass1.SALT;
                $haslo = md5($haslo);
                
                // UUID
                $token = $var_email.SALT;
                $token = md5($token);
                            
                 // Zapis do bazy
                $ret = database::INSERT("uzytkownicy",
                                    Array("email" => $var_email,"haslo" => $haslo, "token" => $token,
                                    "kod_aktywacyjny" => $key, "aktywny" => 0, "uprawnienia" => 0,
                                    "imie" => $var_imie,"nazwisko" => $var_nazwisko,
                                    "ulica" => $var_ulica, "dom" => $var_mieszkanie, "lokal" => $var_lokal,
                                    "kod" => $var_kod1,"kod2" => $var_kod2, "miasto" => $var_miasto,"wojewodztwo_id "=>$var_wojewodztwo));
                                    
                if($ret)
                {
                    $mailmng = new mailmanager;
                    $mailmng->SendActivationMail($var_email,$key);
                    echo('Użytkownik '.$var_email.' został zarejestrowany. W ciągu kilku minut powinien przyjść mail potwierdzający. Kliknij <a href="'.$INFO['referer'].'">tutaj</a> by powrócić na poprzednią stronę');
                }
                return $ret;          
            }
            /*
            $user_mng = new usermanager;
            $user_mng->Register(Array($var_email,$var_pass1,$var_imie,$var_nazwisko,$var_ulica,$var_mieszkanie,$var_lokal,$var_kod1,$var_kod2,$var_miasto,$var_wojewodztwo));
            
            */
        }        
    }
    /* Zwraca true jeśli przeszło walidacje */
    public function ValidatePASS($PASS)
    {
        $value = false;
        if(preg_match("/[a-zA-Z0-9]{6,16}/",$PASS)) $value = true;
        return $value;
    }
    public function Register($ACCOUNT = Array())
    {
        /*
        Array
        (
            [0] => mail
            [1] => haslo
            [2] => imie
            [3] => nazwisko
            [4] => ulica
            [5] => mieszkanie
            [6] => lokal
            [7] => kod1
            [8] => kod2
            [9] => miasto
            [10]=> wojewodztwo
        )
        */
        $result = $this->IsUserExist($ACCOUNT[0]);

        if(!$result)
        {
            // Klucz aktywacyjny
            $key = $ACCOUNT[0];
            $salt= getdate();
            $key.= $salt[0];
            $key = md5($key);
            
            // Haslo
            $haslo = $ACCOUNT[1].SALT;
            $haslo = md5($haslo);
            
            // UUID
            $token = $ACCOUNT[0].SALT;
            $token = md5($token);
            
            // Zapis do bazy
            $ret = database::INSERT("uzytkownicy",
                                Array("email" => $ACCOUNT[0],"haslo" => $haslo, "token" => $token,
                                "kod_aktywacyjny" => $key, "aktywny" => 0, "uprawnienia" => 0,
                                "imie" => $ACCOUNT[2],"nazwisko" => $ACCOUNT[3],
                                "ulica" => $ACCOUNT[4], "dom" => $ACCOUNT[5], "lokal" => $ACCOUNT[6],
                                "kod" => $ACCOUNT[7],"kod2" => $ACCOUNT[8], "miasto" => $ACCOUNT[9],"wojewodztwo_id "=>$ACCOUNT[10]));
            if($ret)
            {
                //echo("Senging Mail...");
                $mailmng = new mailmanager;
                $mailmng->SendActivationMail($ACCOUNT[0],$key);
            }
            return $ret;
        }
        else 
        {
            return false;  
        } 
    }
    public function UpdateUserInfo($INFO,$TOKEN)
    {
        /*
        Array
        (
            [0] => imie
            [1] => nazwisko
            [2] => ulica
            [3] => mieszkanie
            [4] => lokal
            [5] => kod1
            [6] => kod2
            [7] => miasto
            [8]=> wojewodztwo
        )
        */
        
        $error = false;
        if(isset($INFO['imie']) && isset($INFO['nazwisko']) && isset($INFO['ulica']) && isset($INFO['mieszkanie']) &&
           isset($INFO['lokal']) && isset($INFO['kod_1']) && isset($INFO['kod_2']) && isset($INFO['miasto']))
        {
            $var_imie       = $INFO['imie'];
            $var_nazwisko   = $INFO['nazwisko'];
            $var_ulica      = $INFO['ulica'];
            $var_mieszkanie = $INFO['mieszkanie'];
            $var_kod1       = $INFO['kod_1'];
            $var_kod2       = $INFO['kod_2'];
            $var_miasto     = $INFO['miasto'];
            $var_lokal      = $INFO['lokal'];
            $var_wojewodztwo= $INFO['woj'];
                   
            // DANE WYSYLKI
            $var_imie= strtolower($var_imie);
            if(!preg_match("/^[a-zęóąśłżźćń]{3,20}$/D",$var_imie)) $error = true;
            $var_imie = ucwords($var_imie);
            
            $var_nazwisko = strtolower($var_nazwisko);
            if(!preg_match("/^[a-zęóąśłżźćń]{3,20}$/D",$var_nazwisko)) $error = true;
            $var_nazwisko = ucwords($var_nazwisko);
            
            $var_ulica= strtolower($var_ulica);
            if(!preg_match("/^([a-ząśćęółń]{3,20}[ ]{0,1}){1,3}$/",$var_ulica)) $error = true;
            $var_ulica = ucwords($var_ulica);
        
            if(!preg_match("/^([0-9]{1}[a-zA-Z]{0,4}){1,5}$/",$var_mieszkanie)) $error = true;
            if(!preg_match("/^([0-9]){0,5}$/",$var_lokal)) $error = true;
        
            if(!preg_match("/^(\d{2})$/",$var_kod1)) $error = true;
            if(!preg_match("/^(\d{3})$/",$var_kod2)) $error = true;
        
            $var_miasto= strtolower($var_miasto);
            if(!preg_match("/^([a-ząśćęółń]{3,20}[ ]{0,1}){1,3}$/",$var_miasto)) $error = true;
            $var_miasto = ucwords($var_miasto);
            
            if($var_wojewodztwo < 1 && $var_wojewodztwo > 16) $error =true;
        }
        else // Zostanie wywołane gdy błędne dane się pojawią 
        {
            $error = true;   
        }
        if($error == true)
        {
            die("Wystapil nieoczekiwany blad :(");
        }
        else
        {
            return database::UPDATE("uzytkownicy",Array("imie" => $var_imie,"nazwisko" => $var_nazwisko,
                                "ulica" => $var_ulica, "dom" => $var_mieszkanie, "lokal" => $var_lokal,
                                "kod" => $var_kod1,"kod2" => $var_kod2, "miasto" => $var_miasto,"wojewodztwo_id "=>$var_wojewodztwo),Array('token' => $TOKEN));
        }
        
    }
    public function ChangePasswd($ID, $TOKEN, $NEWPASS)
    {
        session_regenerate_id(); // Potrzebne ?
        
        $haslo = $NEWPASS.SALT;
        $haslo = md5($haslo);
        return database::UPDATE('uzytkownicy',Array('haslo' => $haslo),Array('token' => $TOKEN,'id'=> $ID));      
        
    }
    public function AcivateAccount($KEY)
    {
        $login = database::SELECT('uzytkownicy',Array('email'),Array('kod_aktywacyjny' => $KEY));
        if($login)
        {
            $login = $login[0]['email'];
            $is_active = $this->IsUserActive($login);
            if(!$is_active)
            {
                $result = database::UPDATE('uzytkownicy',Array('aktywny' => 1),Array('email' => $login));
                if($result)
                {
                    return "Konto zostało aktywowane";
                }
                else
                {
                    return "Wystąpił nieoczekiwany błąd";
                }
            }
            else 
            {
                return "Użytkownik jest już aktywny";
            }
        }
        else
        {
            return "Użytkownik nie istnieje";
        }
    }
    public function ReactiveAccount($EMAIL)
    {
        $key = $this->GenerateNewActivationKey($EMAIL);
        if($key)
        {
            database::UPDATE('uzytkownicy',Array('kod_aktywacyjny' => $key),Array('email' => $EMAIL)); // Do poprawy
            $mailmng = new mailmanager;
            $mailmng->SendActivationMail($EMAIL,$key);
        }
    }
    /* Generauje klucz */
    public function GenerateNewActivationKey($LOGIN)
    {
        $key = $LOGIN;
        $salt= getdate();
        $key.= $salt[0];
        $key = md5($key);
        return $key;
    }
    /* Generuje nowe 8 literowe hasło */
    public function GenerateNewPassword($LOGIN)
    {
        $mail_mng = new mailmanager;
        $newpass=substr(md5(time()), 0, 8);
        $dbpass = md5($newpass.SALT);
        $result = database::UPDATE('uzytkownicy',Array('haslo' => $dbpass),Array('email' => $LOGIN));
        $mail_mng->SendNewPassword($LOGIN,$newpass);
        return $result;
    }
    /* Zwraca tablicę z adresem */
    public function GetUserAddress($ID,$TOKEN)
    {
        $result = database::SELECT('uzytkownicy',Array('imie,nazwisko,ulica,dom,lokal,kod,kod2,miasto,wojewodztwo_id'),Array('id' => $ID, 'token' => $TOKEN));
        if($result)
        {
            $result = $result[0];
            $woj = database::SELECT('wojewodztwo',Array('nazwa'),Array('id' => $result['wojewodztwo_id']));
            $result['wojewodztwo'] = $woj[0]['nazwa'];
            unset($result['wojewodztwo_id']);
        }
        return $result;
    }
    public function GetUserMail($ID)
    {
        $mail = database::SELECT('uzytkownicy',Array('email'),Array('id' => $ID));
        if($mail)
        {
            return $mail[0]['email'];
        }
        else return false;
    }
}

?>
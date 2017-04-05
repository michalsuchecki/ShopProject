<?php
	require_once("mod/mod_header.php");
	echo ('<div id="main_concent" style="display:inline-block">');

if(isset($_GET['subpage']))
{
	switch($_GET['subpage'])
	{
        case "login": // Regulamin
            if(isset( $_SESSION['zalogowany']))
            {
                die("Jestes juz zalogowny !");
            }
            else
            {
                echo('
                    <div id="contentdiv">
                        <div class="div_label">Panel logowania</div>
                        <div class="div_content">
                            <form id="logowanie_panel" method="post" action="loguj">
                                <label>Login: </label><input type="text" name="login"/><br /><br />
                                <label>Haslo: </label><input type="password" name="passwd"/><br /><br />   
                                <input type="hidden" name="referer" value="'.$_SERVER['HTTP_REFERER'].'"> 
                                <input type="submit" value="Zaloguj">
                            </form>
                            <p>Nie pamietasz hasla ? Wyslij na maila nowe kilkajac <a href="przypomnij">tutaj</a>. <br/>Wyslij ponownie link aktywacyjny: <a href="reaktywuj">Kliknij tutaj</a></p>
                        </div>
                    </div>
                ');
            }
            break;
        case "logmein":
            {
                if(isset($_POST['login']) && isset($_POST['passwd']))
                {
                    $user_mng = new usermanager;
                    $user_mng->Login($_POST['login'],$_POST['passwd']);
                }
                if(isset($_POST['referer']))
                {
                    header("Location: ".$_POST['referer']); // Zmienić na PAGE_URL.$_POST['referer'] ?
                }
                else
                {
                    header("Location: ".PAGE_URL);
                }
            }
            break;
            
        case "logmeout":
            $user_mng = new usermanager;
            $user_mng->Logout();
            header("Location: ".$_SERVER['HTTP_REFERER']);
            break;
            
        case "register": // Regulamin
            if(isset($_SESSION['zalogowany']))
            {
                die("Jestes juz zalogowny wiec po co sie rejestrowac ponownie ? :)");
            }
            else
            {
                echo('
                     <div id="contentdiv">
                        <div class="div_label">Rejestracja nowego użytkownika</div>
                        <div class="div_content">
                            <form id="register_user" name="register_user" method="post" action="rejestruj" onsubmit="return check_registration(this)">
            					<p>Dane podstawowe:</p> 
                                <div class="row">
            						<label class="registerlabel">Email: </label>
            						<input type="text" id="email" name="email" maxlength="64"/>
            					</div>
                                <div class="row">
            						<label class="registerlabel">Haslo: </label>
            						<input type="password" id="pass1" name="pass1" maxlength="16"/>
            					</div>
                                <div class="row">
            						<label class="registerlabel">Potwierdz haslo: </label>
            						<input type="password" id="pass2" name="pass2" maxlength="16"/>
            					</div>
            					<p>Dane do wysyłki:</p> 					
                                <div class="row">
            						<label class="registerlabel">Imie: </label>
            						<input type="text" id="imie" name="imie" maxlength="16"/>
            					</div>
                                <div class="row">
            						<label class="registerlabel">Nazwisko: </label>
            						<input type="text" id="nazwisko" name="nazwisko" maxlength="16"/>
            					</div>						
                                <div class="row">
            						<label class="registerlabel">Ulica: </label>
            						<input type="test" id="ulica" name="ulica" maxlength="40"/>
            					</div>
            					<div class="row">
            						<label class="registerlabel">Mieszkanie/Lokal: </label>
            						<input type="text" id="mieszkanie" name="mieszkanie" maxlength="5" size="4"/>
            						<label> / </label>
            						<input type="text" id="lokal" name="lokal" maxlength="5" size="4"/>
            					</div>				
            					<div class="row">
            						<label class="registerlabel">Kod pocztowy: </label>
            						<input type="text" id="kod_1" name="kod_1" maxlength="2" size="1"/>
            						<label> - </label>
            						<input type="text" id="kod_2" name="kod_2" maxlength="3" size="2"/>
            						</div>				
            					<div class="row">
            						<label class="registerlabel">Miasto: </label>
            						<input type="text" id="miasto" name="miasto" maxlength="64"/>
            					</div>	
            					<div class="row">
            						<label class="registerlabel">Województwo: </label>
            						<select id="woj" name="woj">
                                        <option value="0">Wybierz...</option>
                                        <option value="1">Dolnośląskie</option>
                                        <option value="2">Kujawsko-pomorskie</option>
                                        <option value="3">Lubelskie</option>
                                        <option value="4">Lubuskie</option>
                                        <option value="5">Łódzkie</option>
                                        <option value="6">Małopolskie</option>
                                        <option value="7">Mazowieckie</option>
                                        <option value="8">Opolskie</option>
                                        <option value="9">Podlaskie</option>
                                        <option value="10">Podkarpackie</option>
                                        <option value="11">Pomorskie</option>
                                        <option value="12">Śląskie</option>
                                        <option value="13">Świętokrzyskie</option>
                                        <option value="14">Warmińsko-mazurskie</option>
                                        <option value="15">Wielkopolskie</option>
                                        <option value="16">Zachodnio-pomorskie</option>
                                    </select>
            					</div>	                    
            					
            					<p id="error_message"></p> 
                                <div class="input_chechbox">
            						<label>Klikając przycisk rejestracja akceptujesz warunki <a href="regulamin" target="_blank">regulaminu</a> oraz zgadzasz się na przetwarzanie danych osobowych...</label>
            					</div>
                                <div class="row">
                                    <input type="hidden" name="referer" value="'.$_SERVER['HTTP_REFERER'].'"> 
            						<input type="submit" value="Rejestruj">
            					</div>
                            </form>
                        </div>
                    </div>   
                ');
            }
            break;
        case "regme":
        {
            $user_mng = new usermanager;
            $user_mng->RegisterUSER($_POST);
            break;
        }
        // NOWE HASŁO    
        case "remind":
            echo('<div id="main_concent" style="display:inline-block">
                        <div id="contentdiv">
                            <div class="div_label">Nowe hasło</div>
                                <div class="div_content">
                                    <form id="przypomnij_panel" method="post" action="nowehaslo">
                                        <label>Adres email: </label><input type="text" name="email"/><br /><br />
                                        <input type="submit" value="Wyslij">
                                    </form>
                                <p>Informacja: Na podany adres zostanie wysłane nowe hasło. Które należy zmienić po zalogowaniu się.</p>
                            </div>
                        </div>
                </div>'
            );
            break;
            
        case "newpass":
            $email = (isset($_POST['email'])) ? $_POST['email'] : false;
            if($email)
            {
                $user_mng = new usermanager;
                $exist = $user_mng->IsUserExist($email);
                if($exist)
                {
                    $result = $user_mng->GenerateNewPassword($email);
                    if($result)
                    {
                        echo 'Twoje nowe hasło zostało wysłane na maila.';
                    }
                    else
                    {
                        echo 'Wystąpił nieoczekiwany błąd';
                    }
                }
                else echo 'Użytkownik o podanym adresie nie istnieje';
            }
            break;
        // KONIEC - NOWE HASŁO
        
        // NOWY KLUCZ
        case "reactive":
            echo('<div id="main_concent" style="display:inline-block">
                        <div id="contentdiv">
                            <div class="div_label">Nowy kod aktywacyjny</div>
                                <div class="div_content">
                                    <form id="przypomnij_panel" method="post" action="nowykod">
                                        <label>Adres email: </label><input type="text" name="email"/><br /><br />
                                        <input type="submit" value="Wyslij">
                                    </form>
                                <p>Informacja: Jeśli do tej pory nie otrzymałeś/aś linku aktywacyjnego wyślij powownie nowy podając adres email.</p>
                            </div>
                        </div>
                </div>'
            );
            break;
            
        case "newkey":
        {
            $email = (isset($_POST['email'])) ? $_POST['email'] : false;
            if($email)
            {            
                $user_mng = new usermanager;
                $exist = $user_mng->IsUserExist($email);
                if($exist)
                {
                    $is_active = $user_mng->IsUserActive($email);
                    if(!$is_active)
                    {
                        $result = $user_mng->ReactiveAccount($email);
                         echo 'Na twój mail został wysłany nowy kod aktywacyjny.';
                    }
                    else
                    {
                        echo 'Konto jest już aktywne';
                    }
                }
                else echo 'Użytkownik o podanym adresie nie istnieje';
            }
        }
        break;
        
        case "activate":
        {
            $code = (isset($_GET['akey'])) ? $_GET['akey'] : false;
            if($code)
            {
                $user_mng = new usermanager;
                echo $user_mng->AcivateAccount($code);
            }
        }
        break;
        
        // KONIEC - NOWY KLUCZ
        
        default:
            header("Location: ".PAGE_URL);
            break;  
	}
}
else
{
	header("Location: ".PAGE_URL);

}


/*
<?php

?>
*/

	echo "</div>";
	require_once("mod/mod_footer.php");
?>
<?php

require_once("mod/mod_header.php");

if(usermanager::IsLogged())
{
    echo '<div id="main_sidemenu" style="display:inline-block">';
    	include("mod/mod_account.php");
    echo '</div>';
    
    echo '<div id="main_concent" style="display:inline-block">';
    
    if(usermanager::IsAdmin())
    {
        if(isset($_POST['change_status']) && isset($_POST['orderid']))
        {
            if($_POST['change_status'] == 1)
            {
                $order = $_POST['orderid'];
                foreach($order as $key => $val)
                {
                    $order_mng = new ordermanager;
                    $userid = $order_mng->UpdateOrderStatus($key,$val);
                    $user_mng = new usermanager;
                    $mail = $user_mng->GetUserMail($userid);
                    $mail_mng = new mailmanager;
                    $mail_mng->SendOrderStatusChange($mail,$key,$val);
                }
            }
        }
        
        if(isset($_GET['subpage']))
        {
            switch($_GET['subpage'])
            {
                case 1:
                {
                    echo('<p>Nowe zamówienia</p>');
                    $order_mng = new ordermanager;
                    $result = $order_mng->GetOrderByStatus(1);
                }
                break;
                case 2:
                {
                    echo('<p>W trakcje realizacji</p>');
                    $order_mng = new ordermanager;
                    $result = $order_mng->GetOrderByStatus(2);
                }
                break;
                case 3:
                {
                    echo('<p>Oczekujące na dostawę</p>');
                    $order_mng = new ordermanager;
                    $result = $order_mng->GetOrderByStatus(3);
                }
                break; 
                case 4:
                {
                    echo('<p>Wysłane zamówienia</p>');
                    $order_mng = new ordermanager;
                    $result = $order_mng->GetOrderByStatus(4);
                }
                break;
                default:
                    header('Location:'.PAGE_URL.'konto,1');                                   
            }
        }
        else
        {
            header('Location:'.PAGE_URL.'konto,1');
        }
    }
    else
    {
        
        if(isset($_POST['passchange']) && isset($_SESSION['token']))
        {
            $user_mng = new usermanager;
            
            $oldpass = $_POST['oldpass'];
            $newpass1= $_POST['newpass1'];
            $newpass2= $_POST['newpass2'];
            
            $pass1 = $user_mng->ValidatePASS($oldpass);
            $pass2 = $user_mng->ValidatePASS($newpass1);
            $pass3 = $user_mng->ValidatePASS($newpass2);
            
            $token = $_SESSION['token'];
            $id    = $_SESSION['clientid'];
            
            if($pass1 && $pass2 && $pass1 && !strcmp($newpass1,$newpass2)) 
            {
                $user_mng->ChangePasswd($id,$token,$newpass1);
            }
        }
        if(isset($_POST['addresschange']) && isset($_SESSION['token']))
        {
            $user_mng = new usermanager;
            $new_address = Array();
            $new_address[0] = (isset($_POST['imie'])) ? $_POST['imie'] : NULL;
            $new_address[1] = (isset($_POST['nazwisko'])) ? $_POST['nazwisko'] : NULL;
            $new_address[2] = (isset($_POST['ulica'])) ? $_POST['ulica'] : NULL;
            $new_address[3] = (isset($_POST['mieszkanie'])) ? $_POST['mieszkanie'] : NULL;
            $new_address[4] = (isset($_POST['lokal'])) ? $_POST['lokal'] : NULL;
            $new_address[5] = (isset($_POST['kod1'])) ? $_POST['kod1'] : NULL;
            $new_address[6] = (isset($_POST['kod2'])) ? $_POST['kod2'] : 0;
            $new_address[7] = (isset($_POST['miasto'])) ? $_POST['miasto'] : NULL;
            $new_address[8] = (isset($_POST['wojewodztwo'])) ? $_POST['wojewodztwo'] : NULL;
            $user_mng->UpdateUserInfo($_POST,$_SESSION['token']);
        }
        if(isset($_GET['subpage']))
        {
            switch($_GET['subpage'])
            {
                case 1:
                {
                    echo('<p>Zmiana danych osobowych</p>');
                    $token = $_SESSION['token'];
                    $id    = $_SESSION['clientid'];
                    $user_mng = new usermanager;
                    $acutal = $user_mng->GetUserAddress($id,$token);
                echo('
                     <div id="contentdiv">
                        <div class="div_content">
                            <form method="post" name="addresschange" onsubmit="return check_adress(this)">
                                <div class="row">
            						<label class="registerlabel">Imie: </label>
            						<input type="text" id="imie" name="imie" maxlength="16" value="'.$acutal['imie'].'"/>
            					</div>
                                <div class="row">
            						<label class="registerlabel">Nazwisko: </label>
            						<input type="text" id="nazwisko" name="nazwisko" maxlength="16" value="'.$acutal['nazwisko'].'"/>
            					</div>						
                                <div class="row">
            						<label class="registerlabel">Ulica: </label>
            						<input type="test" id="ulica" name="ulica" maxlength="40" value="'.$acutal['ulica'].'"/>
            					</div>
            					<div class="row">
            						<label class="registerlabel">Mieszkanie/Lokal: </label>
            						<input type="text" id="mieszkanie" name="mieszkanie" maxlength="5" size="4" value="'.$acutal['dom'].'"/>
            						<label> / </label>
            						<input type="text" id="lokal" name="lokal" maxlength="5" size="4" value="'.$acutal['lokal'].'"/>
            					</div>				
            					<div class="row">
            						<label class="registerlabel">Kod pocztowy: </label>
            						<input type="text" id="kod_1" name="kod_1" maxlength="2" size="1" value="'.$acutal['kod'].'"/>
            						<label> - </label>
            						<input type="text" id="kod_2" name="kod_2" maxlength="3" size="2" value="'.$acutal['kod2'].'"/>
            						</div>				
            					<div class="row">
            						<label class="registerlabel">Miasto: </label>
            						<input type="text" id="miasto" name="miasto" maxlength="64" value="'.$acutal['miasto'].'"/>
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
                                <input name="addresschange" hidden/>             
                                <div class="row"><input type="submit" value="Zmień dane"></div>
                            </form>
                        </div>
                    </div>   
                ');                    
                    break;
                }
                case 2:
                {
                    echo('<p>Zmiana hasła</p>');
                    echo('
                 <div id="contentdiv">
                    <div class="div_content">
                    <form method="post" name="passchange" onsubmit="return check_pass(this)">
                        <div class="row"><label class="registerlabel">Aktualne hasło</label><input id="oldpass" name="oldpass" type="password" maxlength="16"></div>
                        <div class="row"><label class="registerlabel">Nowe hasło</label><input id="newpass1" name="newpass1" type="password" maxlength="16"></div>
                        <div class="row"><label class="registerlabel">Powtórz hasło</label><input id="newpass2" name="newpass2" type="password" maxlength="16"></div>
                        <input name="passchange" hidden/>
                        <input type="submit" value="Zmień hasło" />
                    </form>
                    </div>
                </div>                   
                    ');
                    break;
                } 
                case 3:
                {
                    echo('<p>Aktualne zamówienia</p>');
                    $order_mng = new ordermanager;
                    if(usermanager::IsLogged() && isset($_SESSION['clientid']))
                    {
                        $clientid = $_SESSION['clientid'];
                        $order_mng->GetOrderByClientID($clientid,true);
                    }
                    break;
                } 
                case 4:
                {
                    echo('<p>Archiwum zamówień</p>');
                    $order_mng = new ordermanager;
                    if(usermanager::IsLogged() && isset($_SESSION['clientid']))
                    {
                        $clientid = $_SESSION['clientid'];
                        $order_mng->GetOrderByClientID($clientid,false);
                    }
                    break;
                }  
                default:
                    header('Location:'.PAGE_URL.'konto,1');                                                                   
            }
        }
        else
        {
            header('Location:'.PAGE_URL.'konto,1');
        }   
    }    
    echo "</div>";
}
else
{
    header('Location:'.PAGE_URL.'logowanie');
}
require_once("mod/mod_footer.php");

?>
function validate_input(value,reg)
{
    if(reg.test(value)) return true;
    return false;
}

function check_registration(f) 
{
    var email_ok = validate_input(f.email.value,/^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/);
    var pass1_ok = validate_input(f.pass1.value,/^[a-zA-Z0-9]{6,16}$/);
    var pass2_ok = (f.pass1.value == f.pass2.value && f.pass2.value.length > 0 ) ? true : false ;
    var imie_ok =  validate_input(f.imie.value ,/^[a-zA-ZęóąśłżźćńĘÓĄŚŁŻŹĆŃ]{3,20}$/);
    var nazwisko_ok = validate_input(f.nazwisko.value ,/^[a-zA-ZęóąśłżźćńĘÓĄŚŁŻŹĆŃ]{3,20}$/);
    var ulica_ok = validate_input(f.ulica.value ,/^([a-zA-Ząśćęółń]{3,20}[ ]{0,1}){1,3}$/);
    var miasto_ok = validate_input(f.miasto.value,/^([a-zA-Ząśćęółń]{3,20}[ ]{0,1}){1,3}$/);
    var mieszkanie_ok = validate_input(f.mieszkanie.value ,/^([0-9]{1}[a-zA-Z]{0,4}){1,5}$/);
    var lokal_ok = validate_input(f.lokal.value ,/^([0-9]){0,5}$/);
    var kod1_ok = validate_input(f.kod_1.value,/^\d{2}$/);
    var kod2_ok = validate_input(f.kod_2.value,/^\d{3}$/);
    var woj_ok = false;
    var woj = f.woj.value;
    if(woj > 0 && woj < 17) woj_ok = true; 
     
    // Aktualizacja wyglądu strony  
	(email_ok) ? $('#email').attr('class','input_good') : $('#email').attr('class','input_bad');
	(pass1_ok) ? $('#pass1').attr('class','input_good') : $('#pass1').attr('class','input_bad');
    (pass2_ok) ? $('#pass2').attr('class','input_good') : $('#pass2').attr('class','input_bad');
    (imie_ok) ? $('#imie').attr('class','input_good') : $('#imie').attr('class','input_bad');
    (nazwisko_ok) ? $('#nazwisko').attr('class','input_good') : $('#nazwisko').attr('class','input_bad');
	(ulica_ok) ? $('#ulica').attr('class','input_good') : $('#ulica').attr('class','input_bad');
    (miasto_ok) ? $('#miasto').attr('class','input_good') : $('#miasto').attr('class','input_bad');
    (mieszkanie_ok) ? $('#mieszkanie').attr('class','input_good') : $('#mieszkanie').attr('class','input_bad');
    (lokal_ok) ? $('#lokal').attr('class','input_good') : $('#lokal').attr('class','input_bad');
    (kod1_ok) ? $('#kod_1').attr('class','input_good') : $('#kod_1').attr('class','input_bad');
    (kod2_ok) ? $('#kod_2').attr('class','input_good') : $('#kod_2').attr('class','input_bad');
    (woj_ok) ? $('#woj').attr('class','input_good') : $('#woj').attr('class','input_bad');
    
    if(email_ok && pass1_ok && pass2_ok && imie_ok && nazwisko_ok && ulica_ok && miasto_ok && mieszkanie_ok && lokal_ok && kod1_ok && kod2_ok && woj_ok) return true;

	return false;
}

function set_image(val)
{
    $('div.image_container').html('<a href="'+val+'" rel="lightbox[product]"><img src="'+val+'" alt="'+'"" /></a>');
}

function check_adress(f)
{
    var imie_ok =  validate_input(f.imie.value ,/^[a-zA-ZęóąśłżźćńĘÓĄŚŁŻŹĆŃ]{3,20}$/);
    var nazwisko_ok = validate_input(f.nazwisko.value ,/^[a-zA-ZęóąśłżźćńĘÓĄŚŁŻŹĆŃ]{3,20}$/);
    var ulica_ok = validate_input(f.ulica.value ,/^([a-zA-Ząśćęółń]{3,20}[ ]{0,1}){1,3}$/);
    var miasto_ok = validate_input(f.miasto.value,/^([a-zA-Ząśćęółń]{3,20}[ ]{0,1}){1,3}$/);
    var mieszkanie_ok = validate_input(f.mieszkanie.value ,/^([0-9]{1}[a-zA-Z]{0,4}){1,5}$/);
    var lokal_ok = validate_input(f.lokal.value ,/^([0-9]){0,5}$/);
    var kod1_ok = validate_input(f.kod_1.value,/^\d{2}$/);
    var kod2_ok = validate_input(f.kod_2.value,/^\d{3}$/);
    var woj_ok = false;
    var woj = f.woj.value;
    if(woj > 0 && woj < 17) woj_ok = true;  
    
    (imie_ok) ? $('#imie').attr('class','input_good') : $('#imie').attr('class','input_bad');
    (nazwisko_ok) ? $('#nazwisko').attr('class','input_good') : $('#nazwisko').attr('class','input_bad');
	(ulica_ok) ? $('#ulica').attr('class','input_good') : $('#ulica').attr('class','input_bad');
    (miasto_ok) ? $('#miasto').attr('class','input_good') : $('#miasto').attr('class','input_bad');
    (mieszkanie_ok) ? $('#mieszkanie').attr('class','input_good') : $('#mieszkanie').attr('class','input_bad');
    (lokal_ok) ? $('#lokal').attr('class','input_good') : $('#lokal').attr('class','input_bad');
    (kod1_ok) ? $('#kod_1').attr('class','input_good') : $('#kod_1').attr('class','input_bad');
    (kod2_ok) ? $('#kod_2').attr('class','input_good') : $('#kod_2').attr('class','input_bad');
    (woj_ok) ? $('#woj').attr('class','input_good') : $('#woj').attr('class','input_bad');   
    
    if(imie_ok && nazwisko_ok && ulica_ok && miasto_ok && mieszkanie_ok && lokal_ok && kod1_ok && kod2_ok && woj_ok) return true;    
    return false;
}

function check_pass(f)
{
    //var pass0_ok = validate_input(f.oldpass.value,/^[a-zA-Z0-9]{6,16}$/);
    var pass1_ok = validate_input(f.newpass1.value,/^[a-zA-Z0-9]{6,16}$/);
    var pass2_ok = (f.newpass1.value == f.newpass2.value && f.newpass2.value.length > 0 ) ? true : false ;  
    
	//(pass0_ok) ? $('#oldpass').attr('class','input_good') : $('#oldpass').attr('class','input_bad');
    (pass1_ok) ? $('#newpass1').attr('class','input_good') : $('#newpass1').attr('class','input_bad');
    (pass2_ok) ? $('#newpass2').attr('class','input_good') : $('#newpass2').attr('class','input_bad');
    
    if(pass1_ok && pass1_ok) return true;
    return false;       
}
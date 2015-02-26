<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Salam Mobile <?php if(is_page()) echo '| '.get_the_title(); ?></title>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css" type="text/css" />
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/script.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.js"></script>
<script type="text/javascript" src="http://webdeveloperpost.com/js/jquery-1.4.2.min.js"></script>
<?php if(is_page('view-users')) { ?>

<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/table_rotate.js"></script>

<?php } ?>



<?php if(is_page('forgot-password')) { ?>
<script language="javascript">
function forg_pass(mob) {
	if ((mob==null)||(mob=="")){
		alert("Venligst indtast din mobil nummer");
		return false;
	}
	if (mob.length<8){
		alert("Nummer længde skal være på 8 tegn");
		return false;
	}
  if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); }
  else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
        if (xmlhttp.responseText == 'ok') {
           alert('Snart vil du modtage en SMS med dit password');
           document.form4.tel.value = "";
        }
        else
           alert(xmlhttp.responseText);
     }
  }
  xmlhttp.open("GET","<?php bloginfo(template_directory) ?>/forgot-password.php?tel="+mob,true);
  xmlhttp.send();
}
</script>
<?php } ?>

<?php if(is_page('get-my-new-pass')) { ?>
<script language="javascript">
function get_pass(mob) {
	if ((mob==null)||(mob=="")){
		alert("Venligst indtast din mobil nummer");
		return false;
	}
	if (mob.length<8){
		alert("Nummer længde skal være på 8 tegn");
		return false;
	}
  if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); }
  else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
        if (xmlhttp.responseText == 'ok') {
           alert('Din konto er blevet aktiveret. Snart vil du modtage en SMS med dit password');
           document.form4.tel.value = "";
        }
        else
           alert(xmlhttp.responseText);
     }
  }
  xmlhttp.open("GET","<?php bloginfo(template_directory) ?>/get-my-new-pass.php?tel="+mob,true);
  xmlhttp.send();
}
</script>
<?php } ?>


<script language="javascript">



	$(document).ready(function() {



		window.setTimeout(function() {



			$('.skype_pnh_container').html('');



			$('.skype_pnh_print_container').removeClass('skype_pnh_print_container');



		}, 800);



	});



</script>





<script type="text/javascript">

function echeck(str) {



		var at="@";

		var dot=".";

		var lat=str.indexOf(at);

		var lstr=str.length;

		var ldot=str.indexOf(dot);

		if (str.indexOf(at)==-1){

		   alert("Ugyldig E-mail");

		   return false;

		}



		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){

		   alert("Ugyldig E-mail");

		   return false;

		}



		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){

		    alert("Ugyldig E-mail");

		    return false;

		}



		 if (str.indexOf(at,(lat+1))!=-1){

		    alert("Ugyldig E-mail");

		    return false;

		 }



		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){

		    alert("Ugyldig E-mail");

		    return false;

		 }



		 if (str.indexOf(dot,(lat+2))==-1){

		    alert("Ugyldig E-mail");

		    return false;

		 }

		

		 if (str.indexOf(" ")!=-1){

		    alert("Ugyldig E-mail");

		    return false;

		 }



 		 return true					

	}



function add_subscribe(email) {

	if ((email==null)||(email=="")){

		alert("Venligst indtast din Email");

		return false;

	}

	if (echeck(email)==false){

		return false;

	}



  if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); }



  else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }



  xmlhttp.onreadystatechange=function() {



    if (xmlhttp.readyState==4 && xmlhttp.status==200) {

        if (xmlhttp.responseText == 'ok') {

           alert('Your email "'+email+'" has successyfull added');

           document.f.subscribe.value = "";

        }

        else

           alert(xmlhttp.responseText);

     }



  }



  xmlhttp.open("GET","<?php bloginfo(template_directory) ?>/subscribe.php?email="+email,true);



  xmlhttp.send();

}

function send_kund(name,tel,email,text) {
	if ((name==null)||(name=="")){
		alert("Indtast dit navn");
		return false;
	}

	if (name.length<3){
		alert("Navn længde skal være mindre end 3 tegn");
		return false;
	}

	if ((email==null)||(email=="")){
		alert("Indtast din email");
		return false;
	}

	if (echeck(email)==false){
		return false;
	}
	if ((text==null)||(text=="")){
		alert("Indtast venligst din besked");
		return false;
	}
  if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); }
  else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
        if (xmlhttp.responseText == 'ok') {
           alert('Tak for din email. Vi besvare din email snarest muligt.');
        }
        else
           alert(xmlhttp.responseText);
     }
  }

  xmlhttp.open("GET","<?php bloginfo(template_directory) ?>/kunderservice.php?name="+name+"&tel="+tel+"&email="+email+"&text="+text,true);
  xmlhttp.send();
}
</script>

<?php if(is_page('forbrug')) { ?>
<script type="text/javascript">
forbrug("<?php echo $_COOKIE['mobil']; ?>","<?php echo date("Y-m"); ?>");

function forbrug(tel,mon) {

  if (tel=="" || mon=="") { return; } 
  if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); }
  else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
        //Write data from ajax
        document.getElementById("forbrugs").innerHTML=xmlhttp.responseText;
				$.getScript("<?php bloginfo('template_directory'); ?>/js/table_rotate.js", function(){ });
				$.getScript("<?php bloginfo('template_directory'); ?>/js/table_rotate1.js", function(){ });
				$.getScript("<?php bloginfo('template_directory'); ?>/js/table_rotate2.js", function(){ });

        //JQuery functions for fastnet table

        $("#header_fastnet").click(function(){
          $("#body_fastnet").slideToggle();
          $("#img_fastnet").toggleClass("off");
          $("#top_fastnet").toggleClass("forbrug_top2");
          $("#center_fastnet").toggleClass("forbrug_center2");
          $("#bottom_fastnet").toggleClass("forbrug_bottom2");
        });
        //JQuery functions for sms table
        $("#header_sms").click(function(){
          $("#body_sms").slideToggle();
          ($('#img_sms').attr('class') == 'on') ? $("#img_sms").removeClass("on").addClass("off") : $("#img_sms").removeClass("off").addClass("on");
          ($('#top_sms').attr('class') == 'forbrug_top') ? $("#top_sms").removeClass("forbrug_top").addClass("forbrug_top2") : $("#top_sms").removeClass("forbrug_top2").addClass("forbrug_top");
          ($('#center_sms').attr('class') == 'forbrug_center') ? $("#center_sms").removeClass("forbrug_center").addClass("forbrug_center2") : $("#center_sms").removeClass("forbrug_center2").addClass("forbrug_center");
          ($('#bottom_sms').attr('class') == 'forbrug_bottom') ? $("#bottom_sms").removeClass("forbrug_bottom").addClass("forbrug_bottom2") : $("#bottom_sms").removeClass("forbrug_bottom2").addClass("forbrug_bottom");
        });
        //JQuery functions for gprs table
        $("#header_gprs").click(function(){
          $("#body_gprs").slideToggle();
          ($('#img_gprs').attr('class') == 'on') ? $("#img_gprs").removeClass("on").addClass("off") : $("#img_gprs").removeClass("off").addClass("on");
          ($('#top_gprs').attr('class') == 'forbrug_top') ? $("#top_gprs").removeClass("forbrug_top").addClass("forbrug_top2") : $("#top_gprs").removeClass("forbrug_top2").addClass("forbrug_top");
          ($('#center_gprs').attr('class') == 'forbrug_center') ? $("#center_gprs").removeClass("forbrug_center").addClass("forbrug_center2") : $("#center_gprs").removeClass("forbrug_center2").addClass("forbrug_center");
          ($('#bottom_gprs').attr('class') == 'forbrug_bottom') ? $("#bottom_gprs").removeClass("forbrug_bottom").addClass("forbrug_bottom2") : $("#bottom_gprs").removeClass("forbrug_bottom2").addClass("forbrug_bottom");
        });
        //JQuery functions for mms table
        $("#header_mms").click(function(){
          $("#body_mms").slideToggle();
          ($('#img_mms').attr('class') == 'on') ? $("#img_mms").removeClass("on").addClass("off") : $("#img_mms").removeClass("off").addClass("on");
          ($('#top_mms').attr('class') == 'forbrug_top') ? $("#top_mms").removeClass("forbrug_top").addClass("forbrug_top2") : $("#top_mms").removeClass("forbrug_top2").addClass("forbrug_top");
          ($('#center_mms').attr('class') == 'forbrug_center') ? $("#center_mms").removeClass("forbrug_center").addClass("forbrug_center2") : $("#center_mms").removeClass("forbrug_center2").addClass("forbrug_center");
          ($('#bottom_mms').attr('class') == 'forbrug_bottom') ? $("#bottom_mms").removeClass("forbrug_bottom").addClass("forbrug_bottom2") : $("#bottom_mms").removeClass("forbrug_bottom2").addClass("forbrug_bottom");
        });
        //JQuery functions for fastpris table
        $("#header_fastpris").click(function(){
          $("#body_fastpris").slideToggle();
          ($('#img_fastpris').attr('class') == 'on') ? $("#img_fastpris").removeClass("on").addClass("off") : $("#img_fastpris").removeClass("off").addClass("on");
					($('#top_fastpris').attr('class') == 'forbrug_top') ? $("#top_fastpris").removeClass("forbrug_top").addClass("forbrug_top2") : $("#top_fastpris").removeClass("forbrug_top2").addClass("forbrug_top");
          ($('#center_fastpris').attr('class') == 'forbrug_center') ? $("#center_fastpris").removeClass("forbrug_center").addClass("forbrug_center2") : $("#center_fastpris").removeClass("forbrug_center2").addClass("forbrug_center");
          ($('#bottom_fastpris').attr('class') == 'forbrug_bottom') ? $("#bottom_fastpris").removeClass("forbrug_bottom").addClass("forbrug_bottom2") : $("#bottom_fastpris").removeClass("forbrug_bottom2").addClass("forbrug_bottom");
        });
	$(document).ready(function() {
		window.setTimeout(function() {
			$('.skype_pnh_container').html('');
			$('.skype_pnh_print_container').removeClass('skype_pnh_print_container');
		}, 2000);
	});	
    }
  }
  xmlhttp.open("GET","<?php bloginfo(template_directory) ?>/forbrug.php?tel="+tel+"&mon="+mon,true);
  xmlhttp.send();
}
</script>
<?php } ?>

<?php if(is_page('betalinger')) { ?>

	<script type="text/javascript">

 	function getFee(cardno, acq){

		

		document.getElementById("div_transfee").innerHTML = 'Please wait...';

   

        if (cardno.length < 6) {

			document.forms['ePay'].submit.disabled = true;

          return false;

        }  	    

		

        cardno = cardno.substr(0,6);

   		

		var xmlHttpReq = false;

		var self = this;

		// Mozilla/Safari

		if (window.XMLHttpRequest) {

			self.xmlHttpReq = new XMLHttpRequest();

		}

		// IE

		else if (window.ActiveXObject) {

			self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");

		}

	

		self.xmlHttpReq.open('POST', "<?php echo home_url(); ?>/payments/webservice_fee.php?merchantnumber="+document.forms['ePay'].merchantnumber.value +"&cardno_prefix=" + cardno + "&acquirer=" + acq + "&amount="+document.forms['ePay'].amount.value +"&currency="+document.forms['ePay'].currency.value +"", true);

  		self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

		

		self.xmlHttpReq.onreadystatechange = function() 

	    {

			if (self.xmlHttpReq.readyState == 4) 

			{

				var returnvalues = self.xmlHttpReq.responseText.split(",");

				if (returnvalues.length == 3) 

				{

					var fee = returnvalues[0];

					var cardtype = returnvalues[1];

					var cardtext = returnvalues[2];

		

					document.getElementById("div_transfee").innerHTML = fee / 100 +'&nbsp;&nbsp;&nbsp;( '+ cardtext +' ) ';

		

					document.forms['ePay'].transfee.value = fee;

					

					document.forms['ePay'].submit.disabled = false;

	

				}else{

	

					var epayresponse = returnvalues[0];

					document.getElementById("div_transfee").innerHTML = 'Error (' + epayresponse + ')';

					document.forms['ePay'].submit.disabled = true;

			

				}

			}

		}

          

		self.xmlHttpReq.send();

			

 	}



function control_form() {

if (document.forms['ePay'].amount1.value==''){

alert("Beløb: der kræves felt");

return false;

}

if (document.forms['ePay'].cardno.value==''){

alert("Kortnummer: der kræves felt");

return false;

}

if (document.forms['ePay'].expmonth.value==''){

alert("Udløbsmåned: der kræves felt");

return false;

}

if (document.forms['ePay'].expyear.value==''){

alert("Udlobsar: der kræves felt");

return false;

}

if (document.forms['ePay'].cvc.value==''){

alert("Kontrolcifre: der kræves felt");

return false;

}

if (isNaN(document.forms['ePay'].amount1.value)){

alert("Beløb skal være numerisk");

return false;

}

if (document.forms['ePay'].amount1.value < 20){

alert("Minimum indbetaling 20 kr.");

return false;

}

if (isNaN(document.forms['ePay'].cardno.value)){

alert("Kortnummer skal være numerisk");

return false;

}

if (isNaN(document.forms['ePay'].expmonth.value)){

alert("Udløbsmåned skal være numerisk");

return false;

}

if (document.forms['ePay'].expmonth.value < 12){

alert("Minimum udløbsmåned 12");

return false;

}

if (isNaN(document.forms['ePay'].expyear.value)){

alert("Udlobsar skal være numerisk");

return false;

}

if (isNaN(document.forms['ePay'].cvc.value)){

alert("Kontrolcifre skal være numerisk");

return false;

}

document.forms['ePay'].amount.value = document.forms['ePay'].amount1.value * 100;

return true;

}

	</script>



<?php } ?>







<?php if(is_page('betaling-historien')) { ?>



<script type="text/javascript">



</script>



<script type="text/javascript">



forbrug("<?php echo $_COOKIE['mobil']; ?>","<?php echo date("Y-m"); ?>");



function forbrug(tel,mon) {



  if (tel=="" || mon=="") { return; } 



  if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); }



  else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }



  xmlhttp.onreadystatechange=function() {



    if (xmlhttp.readyState==4 && xmlhttp.status==200) {







        //Write data from ajax



        document.getElementById("forbrugs").innerHTML=xmlhttp.responseText;







$.getScript("<?php bloginfo('template_directory'); ?>/js/table_rotate.js", function(){







   // here you can use anything you defined in the loaded script







});



     }



  }



  xmlhttp.open("GET","<?php bloginfo(template_directory) ?>/historien.php?tel="+tel+"&mon="+mon,true);



  xmlhttp.send();



}



</script>



<?php } ?>











<?php if(is_page('ret-profil')) { ?>



<script type="text/javascript">



function opdater(sid,navn,adr,postnr,email,pass,pass1) {



  if (pass!=pass1) {

    document.getElementById("response_ret").innerHTML="Adgangskode matcher ikke"; return;

  }



  if (pass.length>0 && pass.length<6) {

    document.getElementById("response_ret").innerHTML="Minimum kodeord længde er 6 tegn"; return;

  }



  if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari

    xmlhttp=new XMLHttpRequest();

  }

  else {// code for IE6, IE5



    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");



  }



  xmlhttp.onreadystatechange=function() {



    if (xmlhttp.readyState==4 && xmlhttp.status==200) {



        document.getElementById("response_ret").innerHTML=xmlhttp.responseText;



        createCookie("navn",navn,1);



        createCookie("address",adr,1);



        createCookie("postnr",postnr,1);



        createCookie("email",email,1);



        createCookie("pass",pass,1);



    }



  }



  xmlhttp.open("GET","<?php bloginfo(template_directory) ?>/opdater.php?sid="+sid+"&navn="+navn+"&adr="+adr+"&postnr="+postnr+"&email="+email+"&pass="+pass,true);



  xmlhttp.send();



}



</script>



<?php } ?>







<?php if(is_page('priser')) { ?>



<script type="text/javascript">



function get_price(pid) {



                $.getJSON('<?php bloginfo(template_directory) ?>/get_price.php?id_p='+pid, function(data) {







                        $.each(data, function(key, val) {



                                if(val.landline_price != null) {



                                       var price = parseFloat(val.landline_price).toFixed(2);



                                       $('#landline').val(price+' DKK');



                                }



                                else   $('#landline').val('none');







                                if(val.mobile_price != null) {



                                       var price = parseFloat(val.mobile_price).toFixed(2);



                                       $('#mobile').val(price+' DKK');



                                }



                                else   $('#mobile').val('none');



                        });



                });











}



</script>



<?php } ?>







<script type="text/javascript">



function showUser(tel,pass) {



  if (tel=="" || pass=="") {



    document.getElementById("result_connect").innerHTML='<font color="#ff0000">Udfyld de obligatoriske felter!</font>';



    return;



  } 



  if (window.XMLHttpRequest) {



    xmlhttp=new XMLHttpRequest();



  }



  else {



    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");



  }



  xmlhttp.onreadystatechange=function() {



    if (xmlhttp.readyState==4 && xmlhttp.status==200) {



      if (xmlhttp.responseText == 'ko')



        document.getElementById("result_connect").innerHTML='<font color="#ff0000">Telefonnummer eller adgangskode forkert!</font>';



      else {



        document.getElementById("result_connect").innerHTML='<font color="#0000ff">OK!</font>';



        document.location.href='<?php  home_url(); ?>/mit-salam';



      }



    }



  }



  xmlhttp.open("GET","<?php bloginfo(template_directory) ?>/user_connect.php?tel="+tel+"&pass="+pass,true);



  xmlhttp.send();



}



function deleteCoockie() {



createCookie("sid","",-1);



createCookie("mobil","",-1);



createCookie("navn","",-1);



createCookie("kunde","",-1);



createCookie("email","",-1);



createCookie("postnr","",-1);



createCookie("address","",-1);



createCookie("balance","",-1);



      document.location.href='<?php  home_url(); ?>/';



}



</script>







</head>







<body onclose="deleteCoockie();">



 <div id="wrapper">



  <div class="body">



   <div class="body_inner">



    <!--navi-->



    <div class="navi_outer">



     <div class="navi">



      <ul>



       <li class="sm1 <?php if(is_home()) echo 'active'; ?>"><a href="<?php  home_url(); ?>/">Forside</a></li>



       <li class="sm2 <?php if(is_page('priser-1')||is_page('priser')) echo 'active'; ?>"><a href="<?php  home_url(); ?>/priser-1">Priser</a></li>

    <li class="sm3 <?php if(is_page('Bestil her')) echo 'active'; ?>"><a href="/bestil-her">Bestil her</a></li>


       <li class="sm4 <?php if(is_page('mit-salam') || is_page('mit-nummer') || is_page('betalinger') || is_page('forbrug') || is_page('ret-profil') || is_page('betaling-historien') || is_page('payment-accept')) echo 'active'; ?>"><a href="<?php  home_url(); ?>/mit-salam">Mit Salam</a></li>



       <li class="sm5 <?php if(is_page('kundeservice')||is_page('hvordan-gore-jeg')||is_page('vilkar')||is_page('kunderservice')||is_page('forhandlere')) echo 'active'; ?>"><a href="/kundeservice">Kundeservice</a></li>



   

      </ul>



     </div> <!-- navi -->



     <div class="language">

     <a href="http://www.facebook.com/pages/Salam-Mobile/192247977574947" target="_blanc"><div class="facebook"></div></a>



     <a href="http://twitter.com/SALAMmobil" target="_blanc"><div class="twitter"></div></a>



      <div class="img"><img src="<?php bloginfo('template_directory'); ?>/images/flag.jpg" alt=""/></div>



      <div class="language_bar">



        <select class="input">



	 <option value="1">Dansk</option>



	</select>



      </div> <!-- language_bar -->



     </div> <!-- language -->



    </div> <!-- navi_outer -->







     <!--Header-->



     <div class="top_bg"></div>



     <div class="center_bg">



      <div class="header">



       <div class="logo"><a href="<?php  home_url(); ?>/"><img src="http://salammobile.com/wp-content/uploads/2013/07/salammobile_logo.jpg" alt="Logo"/></a></div>



<?php if ($_COOKIE["sid"] == "") { ?>



       <!--login-->	



       <div class="logoin">



	<div class="logoin_left"></div>



	<div class="logoin_center">



         <div id="forgot_pass" style="float:right; padding-top:8px; font-size: 9px;">
               <!--<a href="<?php  home_url(); ?>/get-my-new-pass" style="text-decoration:none;">Get my new pass</a> &nbsp;-->
               <a href="<?php  home_url(); ?>/forgot-password" style="text-decoration:none;">Glemt din adgangskode?</a>
         </div>



	 <h1><img src="<?php bloginfo('template_directory'); ?>/images/login_here.jpg" alt=""/> Login her</h1>



         <form name="form1">
	  <div class="login_area">
	   <div class="formlogin">
	    <div class="formloginL" style="width: 35px;">Mobil:</div>
	    <div class="formloginR"><input name="tel" class="textfield" type="text" /></div>
           </div><!--formlogin-->
	   <div class="formlogin">
	    <div class="formloginL">Password:</div>
	    <div class="formloginR"><input name="pass" class="textfield" type="password" /></div>
	   </div><!--formlogin-->



          </div><!--loginarea-->



	  <div class="login_area">



           <div id="result_connect" style="float:left; padding-top:4px;"></div>



           <img src="<?php bloginfo('template_directory'); ?>/images/login_btn.png" alt="" onclick="showUser(document.form1.tel.value,document.form1.pass.value);"/>



          </div><!--loginarea-->



         </form>



        </div> <!-- logoin_center -->



	<div class="logoin_right"></div>



       </div><!--logoin-->



<?php } ?>



      </div> <!-- header -->



<!--newsletter-->



	<div class="newsletter_bg">



<?php if ($_COOKIE["sid"] != "")



   include 'newsletter.php'; 



   else include 'newsletter_n.php'; 



?>



	</div>
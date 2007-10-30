
// YAV rules
var rulesUser = new Array();
rulesUser[0]='data[User][userid]:User|required';
rulesUser[1]='data[User][realname]:Real name|required';
rulesUser[2]='checkGroups()|custom';

function setRulesNewUser() {
		rulesUser[3]='data[User][passwd-new]:Nuova Password|required';
		rulesUser[4]='data[User][passwd-confirm]:Conferma Nuova Password|required';
		rulesUser[5]='data[User][passwd-confirm]:Password di conferma|equal|'+$("#newPass").val()+'| Le password non coincidono';
		rulesUser[6]='data[User][passwd-new]:Nuova Password|minlength|8';
		rulesUser[7]='checkPassword()|custom';
}


function checkGroups() {
/*	var chk=false;
	$("input").each(function(){
	  if(this.checked == "checked")
	  	chk=true;
	});
	if(!chk) 
		return "E' obbligatorio selezionare almeno un gruppo";
*/		
	return null;
}

function checkPassword() {
	var val = $("#newPass").val();
   	if(val != null) {
	   	if (!(val.match("[^a-zA-Z0-9]+")))
			return "La password deve contenere almeno un carattere non alfanumerico come [](){}.,-*;:?!_<>'\"@+=/~|";
		if (!(val.match("[0-9]+")))
			return "La password deve contenere almeno un carattere numerico";
	}
	return null;
}


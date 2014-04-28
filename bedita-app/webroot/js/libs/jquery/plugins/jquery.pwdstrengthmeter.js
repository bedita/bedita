// Password strength meter
// This jQuery plugin is written by firas kassem [2007.04.05]
// Firas Kassem  phiras.wordpress.com || phiras at gmail {dot} com
// for more information : http://phiras.wordpress.com/2007/04/08/password-strength-meter-a-jquery-plugin/

var shortPass = 'Too short'
var badPass = 'Bad'
var goodPass = 'Good'
var strongPass = 'Strong'

/*************************************************************/
/*///////////////////// - BEGIN patch ///////////////////////*/
/* @added by dante (d.didomenico@channelweb.it)              */
/*************************************************************/
function passwordScore(password,username)
{
    score = 0 
    
    //password empty
    if(password.length == 0) {return -1;}
    
    //password < 4
    if (password.length < 4 ) { return 0 }
    
    //password == username
    if (password.toLowerCase()==username.toLowerCase()) return 0
    
    //password length
    score += password.length * 4
    score += ( checkRepetition(1,password).length - password.length ) * 1
    score += ( checkRepetition(2,password).length - password.length ) * 1
    score += ( checkRepetition(3,password).length - password.length ) * 1
    score += ( checkRepetition(4,password).length - password.length ) * 1

    //password has 3 numbers
    if (password.match(/(.*[0-9].*[0-9].*[0-9])/))  score += 5 
    
    //password has 2 sybols
    if (password.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)) score += 5 
    
    //password has Upper and Lower chars
    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))  score += 10 
    
    //password has number and chars
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))  score += 15 
    //
    //password has number and symbol
    if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([0-9])/))  score += 15 
    
    //password has char and symbol
    if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([a-zA-Z])/))  score += 15 
    
    //password is just a nubers or chars
    if (password.match(/^\w+$/) || password.match(/^\d+$/) )  score -= 10 
    
    //verifing 0 < score < 100
    if ( score < 0 )  score = 0 
    if ( score > 100 )  score = 100 
    
    return score
}

function pwdStrenFeedback(user,pass) {
	var description = new Array();
	description[0] = "<table><tr><td><table><tr><td style=\"height:4px;width:160px;background-color:tan\"><\/td><\/tr><\/table><\/td><td>&#160;<\/td><\/tr><\/table>";
	description[1] = "<table><tr><td><table cellpadding=0 cellspacing=2><tr><td style=\"height:4px;width:40px;background-color:#ff0000\"><\/td><td style=\"height:4px;width:120px;background-color:tan\"><\/td><\/tr><\/table><\/td><td>&#160;<\/td><\/tr><\/table>";
	description[2] = "<table><tr><td><table cellpadding=0 cellspacing=2><tr><td style=\"height:4px;width:80px;background-color:#990000\"><\/td><td style=\"height:4px;width:80px;background-color:tan\"><\/td><\/tr><\/table><\/td><td>&#160;<\/td><\/tr><\/table>";
	description[2] = "<table><tr><td><table cellpadding=0 cellspacing=2><tr><td style=\"height:4px;width:120px;background-color:#990099\"><\/td><td style=\"height:4px;width:40px;background-color:tan\"><\/td><\/tr><\/table><\/td><td>&#160;<\/td><\/tr><\/table>";
	description[4] = "<table><tr><td><table><tr><td style=\"height:4px;width:160px;background-color:#0000FF\"><\/td><\/tr><\/table><\/td><td>&#160;<\/td><\/tr><\/table>";
	
	var strVerdict = 0;
	var intScore = passwordScore(user,pass);
	if(intScore == -1) { // Too short
	   strVerdict = description[0];
	} else if(intScore > -1 && intScore < 25) { // Bad
	   strVerdict = description[1];
	} else if (intScore > 25 && intScore < 50) { // Bad
	   strVerdict = description[2];
	} else if (intScore > 50 && intScore < 75) { // Bad
	   strVerdict = description[3];
	} else if (intScore > 75 && intScore < 100) { // Strong
	   strVerdict = description[4];
	} else if (intScore == 100) {
	  strVerdict = description[4];
	}
	return strVerdict;
}
/*************************************************************/
/*///////////////////// - END patch /////////////////////////*/
/*************************************************************/

function passwordStrength(password,username)
{
    score = 0 
    
    //password < 4
    if (password.length < 4 ) { return shortPass }
    
    //password == username
    if (password.toLowerCase()==username.toLowerCase()) return badPass
    
    //password length
    score += password.length * 4
    score += ( checkRepetition(1,password).length - password.length ) * 1
    score += ( checkRepetition(2,password).length - password.length ) * 1
    score += ( checkRepetition(3,password).length - password.length ) * 1
    score += ( checkRepetition(4,password).length - password.length ) * 1

    //password has 3 numbers
    if (password.match(/(.*[0-9].*[0-9].*[0-9])/))  score += 5 
    
    //password has 2 sybols
    if (password.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)) score += 5 
    
    //password has Upper and Lower chars
    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))  score += 10 
    
    //password has number and chars
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))  score += 15 
    //
    //password has number and symbol
    if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([0-9])/))  score += 15 
    
    //password has char and symbol
    if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([a-zA-Z])/))  score += 15 
    
    //password is just a nubers or chars
    if (password.match(/^\w+$/) || password.match(/^\d+$/) )  score -= 10 
    
    //verifing 0 < score < 100
    if ( score < 0 )  score = 0 
    if ( score > 100 )  score = 100 
    
    if (score < 34 )  return badPass 
    if (score < 68 )  return goodPass
    return strongPass
}


// checkRepetition(1,'aaaaaaabcbc')   = 'abcbc'
// checkRepetition(2,'aaaaaaabcbc')   = 'aabc'
// checkRepetition(2,'aaaaaaabcdbcd') = 'aabcd'

function checkRepetition(pLen,str) {
    res = ""
    for ( i=0; i<str.length ; i++ ) {
        repeated=true
        for (j=0;j < pLen && (j+i+pLen) < str.length;j++)
            repeated=repeated && (str.charAt(j+i)==str.charAt(j+i+pLen))
        if (j<pLen) repeated=false
        if (repeated) {
            i+=pLen-1
            repeated=false
        }
        else {
            res+=str.charAt(i)
        }
    }
    return res
}
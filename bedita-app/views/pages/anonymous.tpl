<script type="text/javascript">
{literal}
<!--
var rules = new Array();
rules[0]='loginUserid:Login|required';
rules[1]='loginPasswd:Password|minlength|6';
-->
{/literal}
</script>

</head>
<body>

<div id="container">

<div id="content">

<div id="errorsDiv">{if ($session->check('Message.flash'))}{$session->flash()}{/if}</div>

{if ($errorMessage)}<h2>{$errorMessage}</h2>{/if}

{formHelper fnc="create" args="'login', array('action' => '/users/login', 'type' => 'POST', 'id' => 'loginForm', 'name' => 'loginForm')"}

{assign var="URL" value=$beurl->here()}

{formHelper fnc="hidden" args="'login/URLOK', array('value' => '$URL')"}
{formHelper fnc="hidden" args="'login/URLERROR', array('value' => '$URL')"}
<fieldset>
    <legend>User Login</legend>
    
        <label for="username">Username: </label>
		{formHelper fnc="text" args="'login/userid', array('style' => 'width: 150px')"}
    
        <label for="password">Password: </label>
		{formHelper fnc="password" args="'login/passwd', array('style' => 'width: 150px')"}
    
        <label for="submit">&nbsp;</label><br />
		{formHelper fnc="submit" args="'entra', array('onclick' => 'if(!checkOnSubmit(\'loginForm\',rules)) return false;')"}
</fieldset>
</form>

</div>

</div>
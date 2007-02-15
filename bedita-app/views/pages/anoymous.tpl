{if ($errorMessage)}<h2>{$errorMessage}</h2>{/if}

{formHelper fnc="create" args="'login', array('action' => '/users/login', 'type' => 'POST')"}

{assign var="URL" value=$html->here}
{formHelper fnc="hidden" args="'login/URLOK', array('value' => '$URL')"}
{formHelper fnc="hidden" args="'login/URLERROR', array('value' => '$URL')"}

<fieldset>
    <legend>User Login</legend>
    
        <label for="username">Username: </label>
		{formHelper fnc="text" args="'login/userid', array('style' => 'width: 150px')"}
    
        <label for="password">Password: </label>
		{formHelper fnc="password" args="'login/passwd', array('style' => 'width: 150px')"}
    
        <label for="submit">&nbsp;</label><br />
		{formHelper fnc="submit" args="'entra'"}
</fieldset>
</form>

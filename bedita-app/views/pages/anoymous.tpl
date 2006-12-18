{if ($errorMessage)}<h2>{$errorMessage}</h2>{/if}

{htmlHelper fnc="formTag" args="'/users/login'"}

{assign var="URL" value=$html->here}
{htmlHelper fnc="hidden" args="'login/URLOK', array('value' => '$URL')"}
{htmlHelper fnc="hidden" args="'login/URLERROR', array('value' => '$URL')"}

<fieldset>
    <legend>User Login</legend>
    
        <label for="username">Username: </label>
		{htmlHelper fnc="input" args="'login/userid', array('style' => 'width: 150px')"}
    
        <label for="password">Password: </label>
		{htmlHelper fnc="password" args="'login/passwd', array('style' => 'width: 150px')"}
    
        <label for="submit">&nbsp;</label><br />
		{htmlHelper fnc="submit" args="'entra'"}
</fieldset>
</form>

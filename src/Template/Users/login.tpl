{$this->Form->create()}
    <fieldset>
        <legend>{__('Please enter your username and password')}</legend>
        {$this->Form->input('userid')}
        {$this->Form->input('passwd')}
    </fieldset>
    {$this->Form->button(__('Login'))}
{$this->Form->end()}

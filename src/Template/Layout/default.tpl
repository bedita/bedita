<!DOCTYPE html>
<html>
<head>
    {$this->Html->charset()}
    <title>
        {$this->fetch('title')}
    </title>
    {$this->Html->meta('icon')}

    {$this->Html->css('cake.generic')}

    {$this->fetch('meta')}
    {$this->fetch('css')}
    {$this->fetch('script')}
</head>
<body>
    <div id="container">
        <div id="header">
            <h1>{$this->Html->link('BEdita', 'http://bedita.com')}</h1>
        </div>
        <div id="content">
            {$this->Flash->render()}
            {$this->Flash->render('auth')}

            {$this->fetch('content')}
        </div>
        <div id="footer">
            {$this->Html->link(
                $this->Html->image('cake.power.gif', ['alt' => $cakeDescription|default:'', 'border' => '0']),
                'http://www.cakephp.org/',
                ['target' => '_blank', 'escape' => false]
            )}
        </div>
    </div>
</body>
</html>

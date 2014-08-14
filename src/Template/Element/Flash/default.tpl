{$class = 'message'}
{if !empty($params['class'])}
    {$class = $class|cat:' '|cat:$params.class}
{/if}
<div class="{$class}">{$message}></div>

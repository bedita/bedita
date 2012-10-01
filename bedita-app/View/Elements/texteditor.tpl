{if ($conf->mce|default:false)}
	
	{$this->Html->script("tiny_mce/tiny_mce", false)}
	{$this->Html->script("tiny_mce/tiny_mce_default_init", false)}

{elseif ($conf->wymeditor|default:false)}

	{$this->Html->script("wymeditor/jquery.wymeditor.pack", false)}
	{$this->Html->script("wymeditor/wymeditor_default_init", false)}

{elseif ($conf->ckeditor|default:false)}

	{$this->Html->script("ckeditor/ckeditor", false)}
	{$this->Html->script("ckeditor/adapters/jquery", false)}
	{$this->Html->script("ckeditor/ckeditor_default_init", false)}
	
{/if}
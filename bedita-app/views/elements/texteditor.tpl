{if ($conf->mce|default:false)}
	
	{$html->script("tiny_mce/tiny_mce", false)}
	{$html->script("tiny_mce/tiny_mce_default_init", false)}

{elseif ($conf->wymeditor|default:false)}

	{$html->script("wymeditor/jquery.wymeditor.pack", false)}
	{$html->script("wymeditor/wymeditor_default_init", false)}

{elseif ($conf->ckeditor|default:false)}

	{$html->script("ckeditor/ckeditor", false)}
	{$html->script("ckeditor/adapters/jquery", false)}
	{$html->script("ckeditor/ckeditor_default_init", false)}
	
{elseif ($conf->ckeditor4|default:false)}

	{$html->script("ckeditor4/ckeditor", false)}
	{$html->script("ckeditor4/adapters/jquery", false)}
	{$html->script("ckeditor4/ckeditor_default_init", false)}
	
{/if}
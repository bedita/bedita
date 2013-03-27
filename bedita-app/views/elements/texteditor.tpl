{if $conf->richtexteditor|default:false}
	{if $conf->richtexteditor.name == "tinymce"}

		{$html->script("tiny_mce/tiny_mce", false)}

	{elseif $conf->richtexteditor.name == "ckeditor"}

		{$html->script("ckeditor/ckeditor", false)}
		{$html->script("ckeditor/adapters/jquery", false)}

	{/if}

	{if !empty($conf->richtexteditor.conf)}
		{$html->script($conf->richtexteditor.conf, false)}
	{/if}
{/if}
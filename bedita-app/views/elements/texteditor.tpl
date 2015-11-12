{if $conf->richtexteditor|default:false}
	{if $conf->richtexteditor.name == "tinymce"}

		{$html->script("libs/richtexteditors/tiny_mce/tiny_mce", false)}

	{elseif $conf->richtexteditor.name == "ckeditor"}

		{$html->script("libs/richtexteditors/ckeditor/ckeditor", false)}
		{$html->script("libs/richtexteditors/ckeditor/adapters/jquery", false)}

	{/if}

	{if !empty($conf->richtexteditor.conf)}
		{$html->script("libs/richtexteditors/conf/"|cat:$conf->richtexteditor.conf, false)}
	{/if}

	{if !empty($conf->richtexteditor.local)}
		{$html->script("libs/richtexteditors/conf/local/"|cat:$conf->richtexteditor.local, false)}
	{/if}
{/if}
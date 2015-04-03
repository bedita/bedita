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

	<script type="text/javascript">
		BEDITA.richtextConf = BEDITA.richtextConf || {};
		BEDITA.richtextConf.attachMedia = BEDITA.richtextConf.attachMedia || {};
		BEDITA.richtextConf.attachMedia.title = '{t}connect new items{/t}';
		BEDITA.richtextConf.attachMedia.page = '{$html->url('/pages/showObjects/')}0/attach/{$object.object_type_id}';
	</script>
{/if}
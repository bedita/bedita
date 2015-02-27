<form action="{$html->url('/files/upload')}" method="post" enctype="multipart/form-data">
    {$beForm->csrf()}
	{$form->file("Filedata")}
	<input type="submit" value="{t}Upload{/t}" />
</form>
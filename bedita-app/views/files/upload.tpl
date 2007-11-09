<form action="{$html->url('/files/upload')}" method="post" enctype="multipart/form-data">
	{$form->file("Filedata")}
	<input type="submit" value="{t}Upload{/t}" />
</form>
<form action="{$this->Html->url('/files/upload')}" method="post" enctype="multipart/form-data">
	{$this->Form->file("Filedata")}
	<input type="submit" value="{t}Upload{/t}" />
</form>
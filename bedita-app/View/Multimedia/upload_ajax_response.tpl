{if !empty($errorMsg)}Error: {$errorMsg}<br/>{/if}
{if !empty($errorFileExist)}
<input type="radio" name="data[upload_choice]" value="new_file_new_obj" checked="checked" />{t}create new (create new multimedia object and upload file){/t}
 <br/>{t}or{/t}<br/>
<input type="radio" name="data[upload_choice]" value="new_file_old_obj"/>{t}overwrite (don't create new multimedia object, substitute file for existing multimedia object){/t}
<br/>{t}or{/t}<br/>
<input type="hidden" name="data[upload_other_obj_id]" value="{$objectId}" />
{t}Go to{/t} <a href="{$this->Html->url('/multimedia/view/')}{$objectId}">{$objectTitle}</a>
{/if}
{if !empty($redirUrl)}
REDIRECTING...
<script type="text/javascript">
	$(document).ready(function() { 
		document.location = "{$this->Html->url($redirUrl)}";
	} );
</script>
{/if}
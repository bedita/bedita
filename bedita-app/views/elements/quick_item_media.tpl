{$html->script('fragments/quick_item_media')}

<form id='dropzone-modal' action="{$html->url('/files/upload')}" class="dropzone" method="post">
    {$beForm->csrf()}
    <div class="fallback">
        <input name="Filedata" type="file" multiple />
    </div>
</form>

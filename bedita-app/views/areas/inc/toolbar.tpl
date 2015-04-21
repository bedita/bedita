<div class="toolbar sans" style="text-align:right; padding-left:30px; float:right;">
    {$beToolbar->first('page','','page')}   
        {$beToolbar->current()} 
        &nbsp;{t}of{/t}&nbsp;
        {if ($beToolbar->pages()) > 0}
        {$beToolbar->last($beToolbar->pages(),'',$beToolbar->pages())}
        {else}1{/if}
    {if ($beToolbar->pages()) > 1}
        &nbsp;
        {$beToolbar->prev('‹ prev','','‹ prev')}
        &nbsp;
        {$beToolbar->next('next ›','','next ›')}
    {/if}
</div>

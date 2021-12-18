<div class="head">
    
    <div class="toolbar" style="white-space:nowrap">
        
        <h2>
            {if !empty($sectionSel)}
                
                {t}{$moduleName}{/t} in “ <span style="color:white" class="evidence">{$sectionSel.title}</span> ”
        
            {elseif !empty($pubSel)}
                
                {t}{$moduleName}{/t} in “ <span style="color:white" class="evidence">{$pubSel.title}</span> ”
            
            {else}
            
                {t}all {$moduleName}{/t}
            
            {/if}
        </h2>
        
        <ul>
    
            <li>
            <span class="evidence">{$numTags}&nbsp;</span> {t}{$moduleName}{/t}
            </li>

        </ul>


    </div>

</div> 
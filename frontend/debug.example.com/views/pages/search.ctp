<div><?php e($this->element('form_search'));?></div>
<div>
	<?php e($beToolbar->init($searchResult["toolbar"]));?>
	
	<?php e($beToolbar->first());?>&nbsp;&nbsp;
	<?php e($beToolbar->prev());?>&nbsp;&nbsp;
	
	<?php e($beToolbar->current() . " / " . $beToolbar->pages());?>&nbsp;&nbsp;
	
	<?php e($beToolbar->next());?>&nbsp;&nbsp;
	<?php e($beToolbar->last());?>&nbsp;&nbsp;
	
	<ul>
	<?php foreach ($searchResult["items"] as $object): ?>
		<li><a href="<?php e($html->url('/') . $object["nickname"]);?>"><?php e($object["title"]);?></a></li>
	<?php endforeach;?>
	</ul>	
</div>	




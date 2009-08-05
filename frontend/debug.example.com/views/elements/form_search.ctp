<form action="<?php e($html->url('/search')); ?>" method="post">
	<input type="text" name="searchstring" value="<?php if(!empty($stringSearched)) e($stringSearched);?>"/>
	<input type="submit" value="<?php __("search", false)?>"/>
</form>
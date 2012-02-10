
$(function()
{
	
	$( '.mce' ).ckeditor( function() {
	}, {
		 customConfig : './custom_ckeditor_config.js',
		}
	);
	
});



/*
CKEDITOR.replaceAll( function( 'mce', config )
    {
        // Custom code to evaluate the replace, returning false
        // if it must not be done.
        // It also passes the "config" parameter, so the
        // developer can customize the instance.
    } );
*/
	
/*	
	$( '.mce' ).ckeditor( function() {
	}, {
		 customConfig : './custom_ckeditor_config.js' }
	);
  
  $( '.mceSimple' ).ckeditor( function() {
	}, {
		 customConfig : './custom_ckeditor_config_simple.js' }
	); 
*/


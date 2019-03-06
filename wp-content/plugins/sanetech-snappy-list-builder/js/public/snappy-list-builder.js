// wait until the page and jQuery have loaded before running the code below
jQuery(document).ready(function($){
	
	// setup our wp ajax URL
	var wpajax_url = document.location.protocol + '//' + document.location.host + '/wp-admin/admin-ajax.php';
	
	// email capture action url
	var email_capture_url = wpajax_url += '?action=slb_save_subscription';
	
	$('form.slb-form').bind('submit',function(){
		
		// get the jquery form object
		$form = $(this);
		
		// setup our form data for our ajax post
		var form_data = $form.serialize();
		
		// submit our form data with ajax
		$.ajax({
			'method':'post',
			'url':email_capture_url,
			'data':form_data,
			'dataType':'json',
			'cache':false,
			'success': function( data, textStatus ) {
				if( data.status == 1 ) {
					// success
					// reset the form
					$form[0].reset();
					// notify the user of success
					alert(data.message);
				} else {
					// error
					// begin building our error message text
					var msg = data.message + '\r' + data.error + '\r';
					// loop over the errors
					$.each(data.errors,function(key,value){
						// append each error on a new line
						msg += '\r';
						msg += '- '+ value;
					});
					// notify the user of the error
					alert( msg );
				}
			},
			'error': function( jqXHR, textStatus, errorThrown ) {
				// ajax didn't work
			}
			
		});
		
		// stop the form from submitting normally
		return false;
		
	});
	
});
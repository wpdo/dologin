var dologin_can_submit_user = '';

document.addEventListener( 'DOMContentLoaded', function() { jQuery( document ).ready( function( $ ) {
	function dologin_cb( e )
	{
		if ( dologin_can_submit_user && dologin_can_submit_user == $( '#user_login' ).val() ) {
			return true;
		}

		e.preventDefault();

		$( '#dologin-process' ).show();
		$( '#dologin-process-msg' ).attr( 'class', 'dologin-spinner' ).html( '' );

		var that = this;

		$.ajax( {
			url: dologin.login_url,
			type: 'POST',
			data: {
				action: 'dologin_send_sms',
				user: $( '#user_login' ).val(),
				pswd: $( '#user_pass' ).val()
			},
			dataType: 'json',
			success: function( res ) {
				if ( res._res !== 'ok' ) {
					$( '#dologin-process-msg' ).attr( 'class', 'dologin-err' ).html( res._msg );
					$( '#dologin-two_factor_code' ).attr( 'required', false );
					$( '#dologin-dynamic_code' ).hide();
				} else {
					// If no phone set in profile
					if ( 'bypassed' in res ) {
						$( that ).off().submit();
						return;
					}
					$( '#dologin-process-msg' ).attr( 'class', 'dologin-success' ).html( res.info );
					$( '#dologin-dynamic_code' ).show();
					$( '#dologin-two_factor_code' ).attr( 'required', true );
					dologin_can_submit_user = $( '#user_login' ).val();
				}

			}
		} );
	}

	$('#wp-submit').parents('form[name!="resetpassform"]:first').not('.tml-login form[name="loginform"], .tml-login form[name="login"]').submit( dologin_cb );
	// $('.tml-login form[name="loginform"], .tml-login form[name="login"], #wpmem_login form, form#ihc_login_form').submit( dologin_cb );

} ); } );
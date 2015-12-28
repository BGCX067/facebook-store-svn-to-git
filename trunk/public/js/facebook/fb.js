/**
 * method 
 */
function fbShare(share_url)
{
	var url = 'http://www.facebook.com/sharer/sharer.php?u=' + share_url;
	window.open(
		url,
		'popUpWindow',
		'width=650,height=320,resizable=no,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=no'
	);
	return false;
}

function fbInviteFriends(invite_message, invite_data, callback) {
    FB.ui(
        {
            method: 'apprequests',
            message: invite_message,
            data: invite_data,
            invite: true
        }
        , function(response){
            if (response != null)
            {
                callback();
            }
        }
    );
}

function getPermissionsFriends(redirect_url)
{
    var msg = '';
    var permission = "read_friendlists, manage_friendlists";
    getPermissions(msg, permission, function(){
        FB.api('/me/friends', function(response) {
            if (response.data != null)
            {
                top.location = redirect_url;
            }
        });
    });
}

function getPermissions(msg, permission, callback)
{
	FB.getLoginStatus(function(response) {
		FB.login(
			function(response) {
				if (response.authResponse) {
                    callback();
				} else {
					alert(msg)
				}
			}
			, {
				scope : permission
			});
	});
}

function showDialog(dialog_content)
{
    var dialog = FB.Dialog.create({
        content: dialog_content,
        closeIcon: true,
        onClose: function() {
                FB.Dialog.remove(dialog);
        },
        visible: true
    });
}

function hideDialog(obj)
{
    FB.Dialog.remove(obj)
}

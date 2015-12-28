function postToWall(id, title, desc)
{
   FB.ui(
   {
     method: 'feed',
     app_id: social_app_id,
     name: title,
     link: social_app_url + '/survey/survey/view/id/' + id + '/id_ref/' + social_user_id,
     picture: 'http://demo.modules2buy.com/fbsurvey/public/img/logo.jpg',
     caption: 'Advanced Survey on Facebook',
     description: desc
   },
   function(response) {
	   top.location.href = 'http://apps.facebook.com/advanced-survey/survey/survey/wall/id/' + id;
   }
 );
}

var id_time_out = [];

function confirmInstall(id_page, callback)
{
    FB.api('/' + id_page, function(response) {
        if (response.has_added_app)
        {
        	clearTimeout(id_time_out[id_page]);
            windowInstallPage.close();
            alert("You have installed RSS Reader application on fanpage successfully.");
        	callback();
        }
        else{
        	clearTimeout(id_time_out[id_page]);
        	id_time_out[id_page] = setTimeout(function(){
                	confirmInstall(id_page,callback);
            },2000); 
        }
    });
}

function isPageInstalled(id_page, callback)
{
	result = FB.api('/' + id_page, function(response) {
		if (response.has_added_app == undefined)
		{
			if (confirm("Please install RSS Reader application on this fanpage. Click OK"))
                {
                    windowInstallPage = window.open("http://www.facebook.com/add.php?api_key=" + social_app_id + "&pages=1&page=" + id_page, 
                    '_blank',
                    'width=650,height=320,resizable=no,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=no');

                    id_time_out[id_page] = setTimeout(function(){
                        confirmInstall(id_page,callback);
                    },2000);
                }
            else
                {
                    $("#id_page").val(current_page);
                }
		}
		else
		{
			callback();
			return true;
		}
	});
}

function permissionInfor(callback)
{
	FB.login(
		function(response) {
			eval(callback());
		}
		, { scope : 'manage_pages' }
	);
}

function permissionAutopost(callback)
{
    FB.getLoginStatus(function(response) {
		FB.login(
			function(response) {
				if (response.authResponse) {
					$("#is_autopost").attr("checked", "checked");
                    callback();
				} else {
                    $("#is_autopost").removeAttr("checked");
					alert('You need to authorize the publish_stream and offline_access permission for auto post on your fanpages.');
                    callback();
				}
			}
			, {
				scope : 'publish_stream'
			});
	});
}

function addCommentTimeLine ($id, $href)
{
    $href = encodeURI($href);
	$("#btn_comment_button_" + $id).addClass("hidden");
	$("#btn_comment_hidden_" + $id).removeClass("hidden");
	$("#box_comment_" + $id).removeClass("hidden");
	$("#box_comment_" + $id).html('<fb:comments href="' + $href + '" num_posts="2" width="810"></fb:comments>');
	FB.XFBML.parse();
}

function hiddenComment ($id)
{
	$("#btn_comment_button_" + $id).removeClass("hidden");
	$("#btn_comment_hidden_" + $id).addClass("hidden");
	$("#box_comment_" + $id).addClass("hidden");
	FB.XFBML.parse();
}

function buttonComment ($id, $href)
{
    $href = encodeURI($href);
	$("#btn_comment_button_" + $id).html('comments: <fb:comments-count href="' + $href + '"/></fb:comments-count>');
	$("#btn_comment_hidden_" + $id).html('comments: <fb:comments-count href="' + $href + '"/></fb:comments-count>');
}

function postToFanpageWall($messages)
{
	FB.ui($messages, function(response) {});
}

function share(share_url)
{
	window.open(
		'http://www.facebook.com/sharer/sharer.php?u=' + share_url,
		'popUpWindow',
		'width=650,height=320,resizable=no,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=no'
		);
	return false;
}

function parse()
{
    FB.XFBML.parse();
}

function getPageUrl(page_name, page_id){
    var expr = /[^a-zA-Z 0-9]+/g;
    page_name = page_name.replace(expr, "");
	var page_addr = 'http://facebook.com/pages/'+page_name+'/'+page_id+'?sk=app_'+ social_app_id ;
	return page_addr;
}

function goToPage (page_name, page_id)
{
	var url = getPageUrl(page_name, page_id);    
    window.open(url, '_blank');
}

function manageFanpages(redirect_url, scope)
{
    var msg = 'You need to authorize the manage page permission before you can load your fanpages.';
    if (scope != "") {
        scope = scope + ', ' + "manage_pages, email";
    } else {
        scope = "manage_pages, email";
    }
	
    authorizeBefore(redirect_url, msg, scope);	
}


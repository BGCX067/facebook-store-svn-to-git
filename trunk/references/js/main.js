function selectTab(id)
{
	$('#' + id).addClass('selected');
}

function removeTabSelected()
{
    $(".tab_container li a.selected").removeClass('selected');
}

function addTab(id, caption, url)
{
    removeTabSelected();
	$('.tab_container').append('<li class="last"><a target="_top" id="' + id + '" href="' + url + '">' + caption + '</a></li>');
    selectTab(id);
}

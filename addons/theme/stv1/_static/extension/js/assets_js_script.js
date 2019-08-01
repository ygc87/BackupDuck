$(function(){
	
	var note = $('#note'),
		ts = new Date(2016, 8, 20, 23, 59, 59),
		newYear = true;
	
	if((new Date()) > ts){
		// The new year is here! Count towards something else.
		// Notice the *1000 at the end - time must be in milliseconds
		ts = (new Date()).getTime() + 1*24*60*60*1000;
		newYear = true;
	}
		
	$('#countdown').countdown({
		timestamp	: ts,
		callback	: function(days, hours, minutes, seconds){
			
			var message = "团购活动进行中,<a href='http://demo.thinksns.com/ts4/index.php?app=Event&mod=Info&act=index&id=68'>点击</a>快速报名";
			note.html(message);
		}
	});
	
});

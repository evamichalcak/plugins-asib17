// function viewmeaddvote(postId)

// {

// 	jQuery.ajax({

// 	type: 'POST',

// 	url: viewmeajax.ajaxurl,

// 	data: {

// 	action: 'viewme_addvote',

// 	postid: postId

// },



// success:function(data, textStatus, XMLHttpRequest){



// 	var linkid = '#viewme-' + postId;

// 	jQuery(linkid).html('');

// 	jQuery(linkid).append(data);

// 	},

// 	error: function(MLHttpRequest, textStatus, errorThrown){

// 		alert(errorThrown);

// 		}

// 	});

// }



function viewmeresetvotes(postId, viewArray)

{

	jQuery.ajax({

	type: 'POST',

	url: viewmeajax.ajaxurl,

	data: {

	action: 'viewme_resetvote',

	postid: postId,

	'vvi': viewArray

},

success:function(data, textStatus, XMLHttpRequest){

	console.log('reset votes: all');

	},

	error: function(MLHttpRequest, textStatus, errorThrown){

		alert(errorThrown);

		}

	}); 

}



function viewmeresetview(postId, viewArray)

{

	jQuery.ajax({

	type: 'POST',

	url: viewmeajax.ajaxurl,

	data: {

	action: 'viewme_resetview',

	postid: postId,

	'vvi': viewArray,

},

success:function(data, textStatus, XMLHttpRequest){

	console.log('reset views: all');

	},

	error: function(MLHttpRequest, textStatus, errorThrown){

		alert(errorThrown);

		}

	});

}



function viewmesave(viewArray, voteArray)

//function viewmesave()

{

	jQuery.ajax({

	type: 'POST',

	url: viewmeajax.ajaxurl,

	data: {

	action: 'viewme_save',

	'vvo': voteArray,

	'vvi': viewArray,

},

success:function(data, textStatus, XMLHttpRequest){

	console.log('saved!');

	jQuery(document).trigger('asi.saved');

	},

	error: function(MLHttpRequest, textStatus, errorThrown){

		alert(errorThrown);

		}

	});

}





function viewmeusersave(viewArray, voteArray)

//function viewmesave()

{

	jQuery.ajax({

	type: 'POST',

	url: viewmeajax.ajaxurl,

	data: {

	action: 'viewme_usersave',

	'vvo': voteArray,

	'vvi': viewArray

},

success:function(data, textStatus, XMLHttpRequest){

	console.log('user saved!');

	},

	error: function(MLHttpRequest, textStatus, errorThrown){

		alert(errorThrown);

		}

	});

}





function viewmegetposts()

//function viewmesave()

{

	jQuery.ajax({

	type: 'GET',

	url: viewmeajax.ajaxurl,

	data: {

	action: 'viewme_getposts',

},

success:function(data, textStatus, XMLHttpRequest){

	console.log(data);

	},

	error: function(MLHttpRequest, textStatus, errorThrown){

		alert(errorThrown);

		}

	});

}







function viewmeviewvotestore(postId, vote)

{

	console.log('llll');

	jQuery.ajax({

	type: 'POST',

	url: viewmeajax.ajaxurl,

	data: {

		action: 'viewme_viewvotestore',

		postid: postId,

		vote: vote,

	},



	success:function(data, textStatus, XMLHttpRequest){

		console.log('comment added, post view updated, user meta updated: '+postId);

		},

	error: function(MLHttpRequest, textStatus, errorThrown){

		alert(errorThrown);

		}

	});

}
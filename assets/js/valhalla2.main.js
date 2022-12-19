$(document).ready(function() 
{

	//infite scrolling v2


	//Button for live search to expand
	$('#search_text_input').focus(function()
	{
		if(window.matchMedia( "(min-width: 800px)" ).matches)
		{
			$(this).animate({width: '250px'}, 500);
		}
		//For mobile styling
		else if(window.matchMedia( "(min-width: 320px)" ).matches)
		{
			$(this).animate({width: '250px'}, 500);
		}
	});

	//Live search submit
	$('.button_holder').on('click', function() 
	{
		document.search_form.submit();
	});

	//Button for post (index.php)
	$('#submit_post').click(function()
	{
		var formData = new FormData($("form.post_form")[0]);
		
		$.ajax(
		{
			type: "POST",
			url: "includes/handlers/ajax_submit_post.php",
			data: formData,
			processData: false,
			contentType: false,
			success: function(msg) 
			{
				//$("#post_form").modal('hide');
				location.reload();
			},
			error: function() 
			{
				alert('Failure');
			}
		});
	});

	//Button for profile post
	$('#submit_profile_post').click(function()
	{
		var formData = new FormData($("form.profile_post")[0]);
		
		$.ajax(
		{
			type: "POST",
			url: "includes/handlers/ajax_submit_profile_post.php",
			data: formData,
			processData: false,
			contentType: false,
			success: function(msg) 
			{
				$("#post_form").modal('hide');
				location.reload();
			},
			error: function() 
			{
				alert('Failure');
			}
		});
	});
});

/****************************************************************
 * Hide live search and getDropdownData when not selected 
 ****************************************************************/
$(document).click(function(e)
{
	if(e.target.className != "search_results" && e.target.id != "search_text_input")
	{
		$(".search_results").html("");
		$('.search_results_footer').html("");
		$('.search_results_footer').toggleClass("search_results_footer_empty");
		$('.search_results_footer').toggleClass("search_results_footer");
	}

	if(e.target.className != "dropdown_data_window_message")
	{
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height" : "0px"});
		$('.dropdown_data_window_message').toggleClass("dropdown_data_window_message_empty");
		$('.dropdown_data_window_message').toggleClass("dropdown_data_window_message");
		//Add unclick button code below
		//Otherwise user has to click icon twice to bring menu backup again
	}

	if(e.target.className != "dropdown_data_window_notification") 
	{
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height" : "0px"});
		$('.dropdown_data_window_notification').toggleClass("dropdown_data_window_notification_empty");
		$('.dropdown_data_window_notification').toggleClass("dropdown_data_window_notification");
		//Add unclick button code below
		//Otherwise user has to click icon twice to bring menu backup again
	}
});


function getUsers(value, user) 
{
	$.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data) 
	{
		$(".results").html(data);
	});
}


/****************************************************************
 * Dropdown Data function for Messages (navbar)
 ****************************************************************/
function getDropdownData(user, type) 
{
	if($(".dropdown_data_window").css("height") == "0px") 
	{
		var pageName;

		if(type == 'notification') 
		{
			pageName = "ajax_load_notifications.php";
			$("span").remove("#unread_notification");
		}
		else if (type == 'message') 
		{
			pageName = "ajax_load_messages.php";
			$("span").remove("#unread_message");
		}

		var ajaxreq = $.ajax(
		{
			url: "includes/handlers/" + pageName,
			type: "POST",
			data: "page=1&userLoggedIn=" + user,
			cache: false,

			success: function(response) 
			{
				$(".dropdown_data_window").html(response);
				$(".dropdown_data_window").css({"padding" : "0px", "height": "280px", "border" : "1px solid #DADADA"});
				$("#dropdown_data_type").val(type);
			}
		});
	}
	//if height != 0px -- dropdown_data_window is already open
	else 
	{
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height": "0px", "border" : "none"});
	}
}

/****************************************************************
 * Live Search function
 ****************************************************************/

 function getLiveSearchUsers(value, user)
 {
 	$.post("includes/handlers/ajax_search.php", {query:value, userLoggedIn: user}, function(data)
 	{
 		if($(".search_results_footer_empty")[0])
 		{
 			$(".search_results_footer_empty").toggleClass("search_results_footer");
			$(".search_results_footer_empty").toggleClass("search_results_footer_empty");
 		}

 		$('.search_results').html(data);
		$('.search_results_footer').html("<a href='search.php?q=" + value + "'>See All Results</a>");

		if(data == "") 
		{
			$('.search_results_footer').html("");
			$('.search_results_footer').toggleClass("search_results_footer_empty");
			$('.search_results_footer').toggleClass("search_results_footer");
		}
 	});
 }

 /****************************************************************
 * Message Delete function
 ****************************************************************/
function deleteMessage(messageId, element) 
{

	$.post("includes/handlers/ajax_delete_message.php", {id:messageId}, function(data) 
	{
		$(element).closest(".message").text("Message deleted");
	});	
}

 /****************************************************************
 * Message Preview (show more) function
 ****************************************************************/
function showMore($id) 
{
	var preview = document.getElementById("showMore_preview" + $id);
	var full = document.getElementById("showMore_full" + $id);
	if (full.style.display === "none") 
	{
		full.style.display = "block";
		preview.style.display = "none";
	} 
}

 /****************************************************************
 * Infinite Scrolling function
 ****************************************************************/
function loadPosts(userLoggedIn) 
{
	if(inProgress) 
	{ //If it is already in the process of loading some posts, just return
		return;
	}
	
	inProgress = true;
	$('#loading').show();

	var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

	$.ajax(
	{
		url: "includes/handlers/ajax_load_posts.php",
		type: "POST",
		data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
		cache:false,

		success: function(response) 
		{
			$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
			$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
			$('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 

			$('#loading').hide();
			$(".posts_area").append(response);

			inProgress = false;
		}
	});
}

function isElementInView (el) 
{
	var rect = el.getBoundingClientRect();

	return (
		rect.top >= 0 &&
		rect.left >= 0 &&
		rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
		rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
	);
}

// Infinite Scrolling v2
function infiniteScrolling()
{
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';
	var inProgress = false;

	loadPosts(); //Load first posts

    $(window).scroll(function() 
    {
    	var bottomElement = $(".status_post").last();
    	var noMorePosts = $('.posts_area').find('.noMorePosts').val();

        // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
        if (isElementInView(bottomElement[0]) && noMorePosts == 'false') 
        {
            loadPosts(userLoggedIn);
        }
    });

    // function loadPosts() 
    // {
    //     if(inProgress) 
    //     { //If it is already in the process of loading some posts, just return
	// 		return;
	// 	}
		
	// 	inProgress = true;
	// 	$('#loading').show();

	// 	var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

	// 	$.ajax(
	// 	{
	// 		url: "includes/handlers/ajax_load_posts.php",
	// 		type: "POST",
	// 		data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
	// 		cache:false,

	// 		success: function(response) 
	// 		{
	// 			$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
	// 			$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
	// 			$('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 

	// 			$('#loading').hide();
	// 			$(".posts_area").append(response);

	// 			inProgress = false;
	// 		}
	// 	});
    // }

    //Check if the element is in view
    // function isElementInView (el) 
    // {
    //     var rect = el.getBoundingClientRect();

    //     return (
    //         rect.top >= 0 &&
    //         rect.left >= 0 &&
    //         rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
    //         rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
    //     );
    // }
};

// Infinite scrolling v1 from index.php
// $(document).ready(function()
// {
// 	var userLoggedIn = '<?php echo $userLoggedIn ?>';

// 	$('#loading').show();	

// 	//Original ajax request for loading first posts
// 	$.ajax
// 	({
// 		url: "includes/handlers/ajax_load_posts.php",
// 		type: "POST",
// 		data: "page=1&userLoggedIn=" + userLoggedIn,
// 		cache:false,

// 		success: function(data)
// 		{	
// 			$('#loading').hide(); //don't show the loading sign once posts have been returned
// 			$('.posts_area').html(data);
// 		}
// 	});

// 	$(window).scroll(function() 
// 	{
// 		var height = $('.posts_area').height(); //Div containing posts
// 		var scroll_top = $(this).scrollTop();
// 		var page = $('.posts_area').find('.nextPage').val();
// 		var noMorePosts = $('.posts_area').find('.noMorePosts').val();

// 		//Note that document.body.scrollTop is deprecated in Chrome
// 		//if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') 
// 		if ((document.body.scrollHeight == window.scrollY + window.innerHeight) && noMorePosts == 'false')
// 		{
// 			$('#loading').show();
// 			//alert("Hello, scroll!");

// 			var ajaxReq = $.ajax(
// 			{
// 				url: "includes/handlers/ajax_load_posts.php",
// 				type: "POST",
// 				data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
// 				cache:false,

// 				success: function(response) {
// 					$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
// 					$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

// 					$('#loading').hide();
// 					$('.posts_area').append(response);
// 				}
// 			});

// 		} //End if 

// 		return false;

// 	}); //End (window).scroll(function())

// });
$(document).ready(function()
{
	//On click signup, hide login and show register form
	$("#signup").click(function()
		{
			$("#first").slideUp("medium", function()
			{
				$("#second").slideDown("medium");
			});

		});

		//On click signup, hide register and show login form
	$("#signin").click(function()
		{
			$("#second").slideUp("medium", function()
			{
				$("#first").slideDown("medium");
			});

		});
});
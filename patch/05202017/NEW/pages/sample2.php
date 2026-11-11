<h2>Welcome this is the homepage!</h2>
Sample2 page

Sample Button: <button id="change_page">Change page</button>


<script>
var handler = 'handler/handler.php';

$('#change_page').click(function(){ change_page('homepage.php'); });

function change_page(page){
	$.ajax({
		type	: "POST",
		dataType: "json",
		data	: "action=encode&page="+page,
		url		: handler,
		success	: function(result){
			window.location.href = "index.php?param="+result;
		},error	: function(){
			alert('error handler');
		}
	});
}
</script>
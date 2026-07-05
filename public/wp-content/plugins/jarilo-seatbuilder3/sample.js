console.log("App was loaded!");


const sb3_test_args = {
	"action": "sb3_add_to_basket",
	"price": "29.99",
	"fields": {
		"sideColor": "white (+$9.99)",
		"topColor": "white",
		"model": "Renegade",
		"ribMode": "single",
		"ribColor": "Grey"
	}
};


window.sb3_test = function() {
	jQuery.post(sb3_ajax_url, sb3_test_args, function(r) {
		console.log(r);
		if (r.success) {
			// redirect to cart.
			window.location.href = sb3_basket_url;
		}
	});
}


const app_div = document.getElementById('my-app');


app_div.innerHTML = '<button onclick="sb3_test()">Click to run add to basket test</button>';
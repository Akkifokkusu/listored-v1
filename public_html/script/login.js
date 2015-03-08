$(document).ready(function() {

	$("#loginForm").submit(login);
	$("#regForm").submit(register);
});

var listBullet = "<li style=\"position: relative;\"><img src=\"res/bullet-nochild.png\" style=\"position: absolute; top: 8px; left: -10px; z-index: 1; width: 6px; max-height: 6px;\" />";

function login(event) {
	event.preventDefault();
	$.post("script/loginproc.php", {
		username : $("#login-username").val(),
		password : $("#login-password").val(),
		remember : $("#rememberMe").prop('checked')
	}).done(function (data) {
		$("#status").hide();
		if (data == "1") {
			window.location.reload();
		} else if (data == "0") {
			$("#status").html("<ul>" + listBullet + "Wrong username/password!</li></ul>").show();
		}
		else if (data == "2") {
			$("#status").html("<ul>" + listBullet + "Missing username/password!</li></ul>").show();
		}
	});
}

function register(e) {
	$.post("script/regproc.php",{
		username : $("#reg-username").val(),
		password : $("#reg-password").val(),
		email : $("#email").val()
	}, function(data) {
		$("#status").css('display', 'block');
		if (data == "1") {
			$("#regForm").css('display', 'none');
			$("#status")
					.html(
							"<a href=\"index.php\">Registration successful! Click here to login!</a>");
			$("#link").css('display', 'none');
		} else if (data == "0") {
			$("#status").html("<ul>" + listBullet + "Username taken!</li></ul>");
		} else {
			var err = Number(data);
			var html = "<ul>";
			if (err >= 16) {
				html += listBullet + "Username must be between 4 and 32 characters!</li>";
				err -= 16;
			}
			if (err >= 8) {
				html += listBullet + "Password must be more than 4 characters!</li>";
				err -= 8;
			}
			if (err >= 4) {
				html += listBullet + "Username contains invalid characters!</li>";
				err -= 4;
			}
			if (err >= 2) {
				html += listBullet + "Invalid email address!</li>";
				err -= 2;
			}
			html += "</ul>";
			$("#status").html(html);
		}
	});
	return false;
}

function changeLink(link) {
	if (link == "login") {
		$("#link").html("<a href=\"javascript:changeLink('reg')\">Not registered yet? Click here to register!</a>");
		$("#loginForm").css('display', 'block');
		$("#regForm").css('display', 'none');
		$("#status").css('display', 'none');
	} else if (link == "reg") {
		$("#link").html("<a href=\"javascript:changeLink('login')\">Already registered? Click here to log in!</a>");
		$("#regForm").css('display', 'block');
		$("#loginForm").css('display', 'none');
		$("#status").css('display', 'none');
	}
}

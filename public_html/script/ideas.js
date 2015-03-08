currentVersion = "0.5 Beta";
var reloadTimeout;
var xmlhttp;
var clickToReload = "<br /><a onclick=\"getBag();\">Click here to reload.</a>";
var activeAjax = 0;
var showTimeout;
var bagHtml;
var block = false;
var lastTypedId = 0;

$(document).ready(function() {
	$(window).load(function() {
		if (document.getElementsByTagName("iframe").length === 0) {
			$("#desktopad").css('display','none');
			$("#mobilead").css('display','none');
		}
	});

	$("#listMenuButtons").css('left', "-98px");

	$("#version").html(currentVersion);

	$(document).keyup(function(e) {
		if (e.keyCode == 78) {
			if (e.altKey) {
				$("#newitembox").focus();
			}
		}
	});

	$("<div />", {id: "resizer"}).addClass("itembox").addClass("color0").css({'white-space':'normal','font-size':'50px'}).hide().appendTo(document.body);

	$("#content").css("min-height", ($(window).height()-$("#footer").outerHeight()-$("#floatingmenu").outerHeight()-10));

	$(window).resize(function() {
		$("#content").css("min-height", ($(window).height()-$("#footer").outerHeight()-$("#floatingmenu").outerHeight()-10));
	});
});

function showLoading(order) {
	$("." + order).css('display', 'none');
	$("#loading" + order).css('display', 'inline');
}

function hideLoading(order) {
	cancelClicked(order);
	$("#loading" + order).css('display', 'none');
	$("." + order).not(".hidden").css('display', 'inline');
}

function idleReset() {
	clearTimeout(reloadTimeout);
	reloadTimeout = setTimeout("reloadBag()", 60000);
}

function reloadBag() {
	if ($.active) {
		getBag();
	} else {
		clearTimeout(reloadTimeout);
		reloadTimeout = setTimeout("reloadBag()", 60000);
	}
}

var list;
var editActive = false;

function getBag(p) {
	activeAjax++;
	$.ajax(
		{
			type: "get",
			url: "script/getbag.php",
			dataTpye: "json"
		}).done(function (data) {
			list = "<ul>";
			$.each($.parseJSON(data).listData, function (key, val) {recurse(key, val, 0)});
			list += "</ul>";
			$("#listored").html($.parseHTML(list));
			parseEx();
			$("#resizer").show();
			$("#listored div").each(formatItem);
			$("#resizer").hide();
			$(".childlist").each(formatChildList);
			$('<div><textarea class="newitem" id="input0" /></div>').appendTo("#listored > ul").addClass("itembox").addClass("color0").css({'word-wrap':'break-word','word-break':'break-all'}).height(100).children("textarea").addClass("editbox").css('font-size',34).width(96).keyup(function(e) {
				if (e.keyCode == 13) {
					e.stopPropagation();
					$(this).each(addItem);
				}
			}).show().autosize({append: ""});
			$(".editbox").keypress(function(event) {
				if(event.which == '13') {
					return false;
				}
			}).not(".newitem").keyup(function(e) {
					if (e.keyCode == 27) {
						$(this).toggle(function() {
							$(this).parent().children("li").toggle();
						});
						editActive = false;
						$(this).parent().children(".edt").css('background-image', 'url(../res/edt.png)');
					}
					else if (e.keyCode == 13) {
						e.stopPropagation();
						$(this).each(editItem);
						editActive = false;
					}
			}).autosize({append: ""});
			if (typeof p != "undefined") {
				$("#item" + p).parents(".childlist").show();
				$("html, body").scrollTop(($("#item" + p).offset().top) - ($(window).height()/2));
			}
		});
}

function recurse(key, val, depth) {
			if (val instanceof Object) {
				if (val['order']) {
					list += '<div id="item' + val['order'] + '" data-depth="' + depth + '" data-order="' + val['order'] + '" data-checked="' + val['checked'] + '" data-children="' + ((typeof(val['children']) == 'undefined') ? "false" : "true")	+ '"><li>' + val['item']
						+ '</li><textarea class="editbox" id="edit' + val['order'] + '">' + val['item'] + '</textarea></div>';
				}
				if (val['children']) {
					list += '<ul id="list' + val['order'] + '" class="childlist" data-order="' + val['order'] + '">';
					$.each(val['children'], function(key, value) {recurse(key, value, depth+1)});
					list += "</ul>";
				}
				else {
					list += '<ul id="list' + val['order'] + '" class="childlist"></ul>';
				}
			}
}

function showBag() {
	$("#listored").html(JSON.stringify($.parseJSON(bagHtml), null, 4));
	clearTimeout(reloadTimeout);
	reloadTimeout = setTimeout("reloadBag()", 60000);
	parseEx();
	if (lastTypedId == 0) {
		$("#newitembox").focus();
	}
	else {
		addClicked(lastTypedId);
	}
	if ($("#content").height()<445) {
		$("#extraad").css('display','none');
	}
	else {
		$("#extraad").css('display','block');
	}
}
function getLists() {
	activeAjax++;
	$.get("script/getlists.php", function(data) {
		activeAjax--;
		$("#lists").html(data);
		numLists = $("#lists").children().length;
	});
}

function formatItem() {
	$(this).addClass("item");
	$(this).addClass("itembox");
	$(this).addClass("color" + ($(this).data('depth')%5));
	if ($(this).data('depth') == 0) {
		$(this).css({'height':'100px','white-space':'normal'});
		$(this).children("li").css({'display':'table-cell','vertical-align':'middle'});
		if ($(this).children().text().trim().split(" ").length > 1) {
			$(this).css({'word-wrap':'break-word','word-break':'break-all'});
			$("#resizer").css({'word-wrap':'break-word','word-break':'break-all'});
		}
		else {
			$(this).css({'word-wrap':'normal','word-break':'normal'});
			$("#resizer").css({'word-wrap':'normal','word-break':'normal'});
		}
		var size;
		$("#resizer").html($(this).html());
		while ($("#resizer").outerHeight() > $(this).outerWidth() || $("#resizer").outerWidth() > $(this).outerHeight()) {
			size = parseInt($("#resizer").css("font-size"), 10);
		$("#resizer").css("font-size", size - 1);
		}
		size = parseInt($("#resizer").css("font-size"), 10);
		$(this).css('font-size', size);
		$("#resizer").css('font-size','50px');
	}
	$(this).children(".editbox").css('font-size', $(this).css('font-size')).width($(this).width()-4);
	if ($(this).data('checked')) {
		$(this).addClass('checked');
	}
	if ($(this).data('children')) {
		$(this).addClass('parent');
		$(this).click(function() {
			showHideList($(this).data('order'));
		});
	}
	$("<span></span>").appendTo($(this)).addClass("corner").addClass("del").data('order',$(this).data('order')).click(function(e) {
		e.stopPropagation();
		deleteItem($(this).data('order'));
	});
	$("<span></span>").appendTo($(this)).addClass("corner").addClass("edt").data('order',$(this).data('order')).click(function(e) {
		e.stopPropagation();
		editActive = !editActive;
		if (editActive) {
			$(this).parent().children("li").toggle();
			$(this).parent().children(".editbox").prop('disabled', false).toggle(function() {
				$(this).trigger("autosize.resize").select();
			});
		}
		else {
			$(this).parent().children(".editbox").toggle(function() {
				$(this).parent().children("li").toggle();
			});
		}
		$(this).css('background-image', (editActive ? 'url(../res/noedt.png)' : 'url(../res/edt.png)'));
	});
	$("<span></span>").appendTo($(this)).addClass("corner").addClass("chk").data('order',$(this).data('order')).data('checked',$(this).data('checked')).click(function(e) {
		e.stopPropagation();
		checkItem($(this).data('order'));
	});
	$("<span></span>").appendTo($(this)).addClass("corner").addClass("cancel").data('order',$(this).data('order')).click(function(e) {
		e.stopPropagation();
		o = $(this).data('order');
		$(this).hide();
		$(this).siblings(".add").show();
		$("#input" + o).trigger({type:"keyup", keyCode:27});
	});
	$("<span></span>").appendTo($(this)).addClass("corner").addClass("add").data('order',$(this).data('order')).click(function(e) {
		e.stopPropagation();
		o = $(this).data('order');
		if (!$("#list" + o).is(":visible")) {
			showHideList(o);
		}
		$(this).hide();
		$(this).siblings(".cancel").show();
		$("#input" + o).parent().parent().show();
		$("#input" + o).focus();
	});
	$(this).hover(function() {
		if (!editActive && ($(this).children(".del,.chk").is(":visible") == $(this).children(".edt").is(":visible")))
			$(this).children(".edt").fadeToggle();
		$(this).children(".del,.chk").fadeToggle();
	});
 }

 function formatChildList() {
	$('<div><li><textarea rows="1" class="newitem" id="input' + $(this).prev().data('order') + '"></textarea></li></div>').appendTo($(this)).addClass("itembox").addClass("color" + (($(this).prev().data('depth')+1)%5)).hide().find("textarea").keyup(function(e) {
		if (e.keyCode == 27) {
			parent = $(this).parent().parent().parent().closest(".itembox");
			$(parent).find(".cancel").hide();
			$(parent).find(".add").show();
			$(this).val("").parent().parent().hide();
		}
		else if (e.keyCode == 13) {
			e.stopPropagation();
			$(this).each(addItem);
		}
	}).addClass("editbox").width(96).show().autosize({append: ""});
	if ($.inArray(($(this).data('order')), ex) > -1) {
		$(this).show();
	}
}

function addItem() {
	newItem = $(this)
	itemParent = parseInt($(this).attr('id').substr(5));
	if (itemParent != 0) {
	}
	if ($(newItem).val() == "")
		return false;
	$(newItem).prop('disabled', true);
	activeAjax++;
	$.ajax({url:"script/additem.php", data:{
		i : encodeURIComponent($.trim($(newItem).val())),
		p : itemParent
	}, type:"post", dataType:"json"}).success(function(data) {
		activeAjax--;
		if (data['status'] == "1") {
			if (itemParent == 0) {
				$('<div id="item' + data['insertedOrder'] + '" data-depth="0" data-order="' + data['insertedOrder'] + '" data-checked="0" data-children="false"><li>' + data['insertedItem'] + '</li><textarea class="editbox" id="edit' + data['insertedOrder'] + '">' + data['insertedItem'] + '</textarea></div><ul id="list' + data['insertedOrder'] + '" class="childlist"></ul>').insertBefore($(newItem).parent());
				$("#resizer").show();
			}
			else
				$('<div id="item' + data['insertedOrder'] + '" data-depth="' + ($("#item" + itemParent).data("depth")+1) + '" data-order="' + data['insertedOrder'] + '" data-checked="0" data-children="false"><li>' + data['insertedItem'] + '</li><textarea class="editbox" id="edit' + data['insertedOrder'] + '">' + data['insertedItem'] + '</textarea></div><ul id="list' + data['insertedOrder'] + '" class="childlist"></ul>').insertBefore($(newItem).parent().parent());
			$("#item" + data['insertedOrder']).each(formatItem);
			$("#list" + data['insertedOrder']).each(formatChildList);
			$("#resizer").hide();
			$("#item" + data['insertedOrder'] + ",#list" + data['insertedOrder']).find(".editbox").keypress(function(event) {
				if(event.which == '13') {
					return false;
				}
			}).not(".newitem").keyup(function(e) {
					if (e.keyCode == 27) {
						$(this).toggle(function() {
							$(this).parent().children("li").toggle();
						});
						editActive = false;
						$(this).parent().children(".edt").css('background-image', 'url(../res/edt.png)');
					}
					else if (e.keyCode == 13) {
						e.stopPropagation();
						$(this).each(editItem);
						editActive = false;
					}
			}).autosize({append: ""});
			$("#item" + itemParent).addClass("parent").data("children", true).off("click").click(function() {
				showHideList($(this).data('order'));
			});
			ex.push(parent);
			saveEx();
			$(newItem).val("").trigger("autosize.resize").prop('disabled', false).focus();
		} else
			$("#listored").html("Error adding item." + clickToReload);
	});
	lastTypedId = itemParent;
}
function editItem() {
	if ($(this).val() == "")
		return false;
	$(this).prop('disabled', true);
	activeAjax++;
	$.ajax({url:"script/edititem.php", data:{
		o : $(this).attr('id').substr(4),
		t : encodeURIComponent($.trim($(this).val()))
	}, type:"post", dataType:"json"}).success(function(data) {
		activeAjax--;
		if (data['status'] == "1") {
			$("#edit" + data['editedOrder']).toggle(function() {
				$(this).parent().children("li").text(data['editedItem']).parent().each(function() {
					if ($(this).data('depth') == 0) {
						$("#resizer").show();
						$(this).css({'height':'100px','white-space':'normal'});
						$(this).children("li").css({'display':'table-cell','vertical-align':'middle'});
						if ($(this).children().text().trim().split(" ").length > 1) {
							$(this).css({'word-wrap':'break-word','word-break':'break-all'});
							$("#resizer").css({'word-wrap':'break-word','word-break':'break-all'});
						}
						else {
							$(this).css({'word-wrap':'normal','word-break':'normal'});
							$("#resizer").css({'word-wrap':'normal','word-break':'normal'});
						}
						var size;
						$("#resizer").html($(this).html());
						while ($("#resizer").outerHeight() > $(this).outerWidth() || $("#resizer").outerWidth() > $(this).outerHeight()) {
							size = parseInt($("#resizer").css("font-size"), 10);
						$("#resizer").css("font-size", size - 1);
						}
						size = parseInt($("#resizer").css("font-size"), 10);
						$(this).css('font-size', size);
						$("#resizer").css('font-size','50px');
						$("#resizer").hide();
					}
					else
						$(this).children("li").show();
				});
				editActive = false;
				$(this).parent().children(".edt").css('background-image', 'url(../res/edt.png)').hide();
			});
		} else
			$("#listored").html("Error editing item." + clickToReload);
	});
	lastTypedId = 0;
}
function deleteItem(order) {
	isParent = $("#item" + order).data('children');
	if (isParent == true) {
		if ($("#list" + order).is(":hidden")) {
				showHideList(order);
			}
			if (!confirm("You are about to delete an item which has at least one sub-item. All sub-items will be deleted. Are you sure you want to proceed?")) {
				return false;
			}
		}
		$.ajax({url: "script/deleteitem.php", data: {
			o : order
		}, type:"post", dataType:"json"}).success(function(data) {
			activeAjax--;
			if (data['status'] == "1") {
			if ($("#item" + order).parent().children(".item").length < 2 && $("#item" + order).data('depth') > 0)
				$("#item" + $("#item" + order).parent().attr("id").substr(4)).removeClass("parent").data("children", false).off("click");
			$("#item" + order).remove();
			$("#list" + order).remove();
		}
			else
				$("#listored").html("Error deleting idea." + clickToReload);
		});
}
function checkItem(order) {
	isChecked = $("#item" + order).data('checked');
	$.ajax({url: "script/checkitem.php", data: {
			o : order,
			c : isChecked
		}, type:"post", dataType:"json"}).success(function(data) {
			activeAjax--;
			if (data['status'] == "1")
				isChecked ? $("#item" + order).data('checked', 0).removeClass("checked") : $("#item" + order).data('checked', 1).addClass("checked");
			else
				$("#listored").html("Error checking item." + clickToReload);
		});
}
function deleteCheckedItems(order) {
	if ($("#list" + order).css('display') == "none") {
		showHideList(order);
	}
	showLoading(order);
	activeAjax++;
	$.get("script/deletecheckeditems.php", {
		o : order,
	},
			function(data) {
				activeAjax--;
				if (data == "1")
					getBag();
				else if (data == "0")
					$("#listored").html(
							"Error deleting checked ideas." + clickToReload);
				else if (data == "-1") {
					hideLoading(order);
					alert("No ideas deleted.");
				}
			});
	lastTypedId = 0;
}
function getChangelog() {
	$.get("script/getchangelog.php", function(data) {
		$("#listored").html(data);
	});
}
function showHideList(listId) {
	if (!$("#list" + listId).is(":visible")) {
		$("#list" + listId).show().children("div").each(function() {
			$(this).children(".editbox").width($(this).children("li").width());
		});
		ex = (typeof ex != 'undefined' && ex instanceof Array) ? ex : [];
		ex.push(listId);
	}
	else {
		$("#list" + listId).hide();
		ex = (typeof ex != 'undefined' && ex instanceof Array) ? ex : [];
		while ($.inArray(listId, ex) > -1) {
			ex.splice(ex.indexOf(listId), 1);
		}
	}
	saveEx();
}
function parseEx() {
	ex = new Array();
	if (readCookie("listoredEx") != null) {
		ex = unescape(readCookie("listoredEx")).split("|");
		for ( var i = 0; i < ex.length; i++) {
			ex[i] = parseInt(ex[i]);
		}
	}
}
function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for ( var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ')
			c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0)
			return c.substring(nameEQ.length, c.length);
	}
	return null;
}
function saveEx() {
	ex = (typeof ex != 'undefined' && ex instanceof Array) ? ex : [];
	if (document.cookie.indexOf("listoredRemember") != null) {
		var expDate = new Date();
		expDate.setDate(expDate.getDate() + 7);
		var newCookie = "listoredEx=" + ex.join("|") + "; expires=" + expDate.toUTCString() + "; path=/";
		document.cookie = newCookie;
	}
}
function editClicked(o) {
	$("#menu" + o).css('display', 'none');
	$("#menuButtons" + o).css('position', 'static');
	$("#ideaText" + o).css('display', 'none');
	$("#ideaEdit" + o)
			.attr('value', unescape($("#ideaEdit" + o).attr('value')));
	$("#ideaEdit" + o).width($("#ideaText" + o).width() + 10).fadeIn().focus();
	$("#check" + o).css('display', 'none');
	$("#delete" + o).css('display', 'none');
	$("#add" + o).css('display', 'none');
	$("#edit" + o).css('display', 'none');
	$("#deletechecked" + o).css('display', 'none');
	$("#exportaslist" + o).css('display', 'none');
	$("#noedit" + o).fadeIn();
}
function noeditClicked(o) {
	$("#ideaEdit" + o).fadeOut(function() {
		$("#ideaText" + o).css('display', 'inline');
	});
	$("#noedit" + o).fadeOut(function() {
		$("#edit" + o).css('display', 'inline');
		$("#check" + o).css('display', 'inline');
		$("#delete" + o).css('display', 'inline');
		$("#add" + o).css('display', 'inline');
		$("#menu" + o).css('display', 'inline');
		$("#deletechecked" + o).css('display', 'inline');
		$("#exportaslist" + o).css('display', 'inline');
		$("#menuButtons" + o).css('position', 'absolute');
	});
}
function addClicked(o) {
	if ($("#list" + o).css('display') == "none") {
		showHideList(o);
	}
	$("#child" + o).css('display', 'list-item');
	$("#cancel" + o).css('display', 'inline');
	$("#add" + o).css('display', 'none');
	$("#input" + o).focus();
	$("#input" + o).keyup(function(e) {
		if (e.keyCode == 27) {
			cancelClicked(o);
		}
	});
}
function cancelClicked(o) {
	$("#child" + o).css('display', 'none');
	$("#add" + o).css('display', 'inline');
	$("#cancel" + o).css('display', 'none');
}
function showHideMenu(o) {
	var $slider = $("#menuButtons" + o);
	$slider.parent().width($slider.width());
	$slider.parent().height($slider.height());
	if (parseInt($slider.css('left')) == 0) {
		$slider.css('z-index', '-1');
		$("#menu" + o).fadeOut(
				'fast',
				function() {
					$("#menu" + o).attr('alt', 'An elipsis.').attr('title',
							'Open Menu').attr('src', 'res/menu.png').fadeIn(
							'fast');
				});
	} else {
		$("#menu" + o).fadeOut(
				'fast',
				function() {
					$("#menu" + o).attr('alt',
							'An elipsis with a no symbol over it.').attr(
							'title', 'Close Menu')
							.attr('src', 'res/nomenu.png').fadeIn('fast');
				});
	}
	$slider.animate({
		left : parseInt($slider.css('left')) == 0 ? -$slider.width() : 0,
		opacity : parseInt($slider.css('opacity')) == 100 ? 0 : 100
	}, function() {
		if (parseInt($slider.css('left')) == 0) {
			$slider.css('z-index', '1');
		}
	});
	return false;
}
function changeList(list) {
	$.get("script/changelist.php", {
		l : list
	}, function(data) {
		var listLoc = "/list/" + list;
		var base = document.getElementsByTagName('base');
		if (base && base[0] && base[0].href) {
			if (base[0].href.substr(base[0].href.length-1) == '/' && listLoc.charAt(0) == '/')
				listLoc = listLoc.substr(1);
				listLoc = base[0].href + listLoc;
			}
		document.location.href = listLoc;
	});
}
function showBagLoading() {
	$("#listored")
			.html(
					"Now loading<img src=\"res/load.gif\" alt=\"A small spinning black circle.\" title=\"Loading\" style=\"margin-left: 10px;\" />");
}
function listEditClicked() {
	$("#listmenu").css('display', 'none');
	$("#listMenuButtons").css('position', 'static');
	$("#listName").css('display', 'none');
	$("#listEdit").attr('value', ($("#listName").html())).width(
			$("#listName").width()).fadeIn().focus();
	$("#editlist").css('display', 'none');
	$("#deleteList").css('display', 'none');
	$("#deletecheckedList").css('display', 'none');
	$("#noeditlist").fadeIn();
}
function noListEditClicked(edited) {
	$("#listEdit").fadeOut(function() {
		$("#listName").css('display', 'inline');
	});
	$("#noeditlist").fadeOut(function() {
		$("#editlist").css('display', 'inline');
		$("#deleteList").css('display', 'inline');
		$("#listmenu").css('display', 'inline');
		$("#deletecheckedList").css('display', 'inline');
		$("#listMenuButtons").css('position', 'absolute');
		if (edited) {
			showHideListMenu();
		}
	});
}
function editList(text) {
	$.get("script/editlist.php", {
		t : encodeURIComponent(text.value)
	}, function(data) {
		$("#listName").html(data);
		noListEditClicked(true);
		getLists();
	});
	lastTypedId = 0;
}
function addListClicked() {
	$("#newlistbox").css('display', 'inline');
	$("#cancelList").css('display', 'inline');
	$("#addList").css('display', 'none');
	$("#newlistbox").focus();
	$("#newlistbox").keyup(function(e) {
		if (e.keyCode == 27) {
			cancelListClicked();
		}
	});
}
function cancelListClicked() {
	$("#newlistbox").css('display', 'none');
	$("#newlistbox").attr('value', '');
	$("#addList").css('display', 'inline');
	$("#cancelList").css('display', 'none');
}
function addList(text) {
	$.get("script/addlist.php", {
		t : encodeURIComponent(text.value)
	}, function(data) {
		cancelListClicked();
		changeList(data);
		getLists();
		if (parseInt($("#listMenuButtons").css('left')) == 0) {
			showHideListMenu();
		}
	});
	lastTypedId = 0;
}
function deleteList() {
	if (numLists <= 1) {
		alert("Cannot delete last list.");
		return;
	}
	if (!confirm("You are about to delete this list and all its items. Are you sure you want to proceed?")) {
		return;
	}
	showHideListMenu();
	$.get("script/deletelist.php", function(data) {
		changeList(data);
		getLists();
	});
	lastTypedId = 0;
}
function showHideListMenu() {
	$("#listMenuButtons").parent().width($("#listMenuButtons").width());
	$("#listMenuButtons").parent().height($("#listMenuButtons").height());
	if (parseInt($("#listMenuButtons").css('left')) == 0) {
		$("#listMenuButtons").css('z-index', '-1');
		$("#listmenu").fadeOut(
				'fast',
				function() {
					$("#listmenu").attr('alt', 'An elipsis.').attr('title',
							'Open Menu').attr('src', 'res/menu.png').fadeIn(
							'fast');
				});
	} else {
		$("#listmenu").fadeOut(
				'fast',
				function() {
					$("#listmenu").attr('alt',
							'An elipsis with a no symbol over it.').attr(
							'title', 'Close Menu')
							.attr('src', 'res/nomenu.png').fadeIn('fast');
				});
	}
	$("#listMenuButtons")
			.animate(
					{
						left : parseInt($("#listMenuButtons").css('left')) == 0 ? -$(
								"#listMenuButtons").width()
								: 0,
						opacity : parseInt($("#listMenuButtons").css('opacity')) == 100 ? 0
								: 100
					}, function() {
						if (parseInt($("#listMenuButtons").css('left')) == 0) {
							$("#listMenuButtons").css('z-index', '1');
						}
					});
	return false;
}
function addUserClicked() {
	$("#listmenu").css('display', 'none');
	$("#listMenuButtons").css('position', 'static');
	$("#adduserbox").fadeIn().focus();
	$("#editlist").css('display', 'none');
	$("#deleteList").css('display', 'none');
	$("#deletecheckedList").css('display', 'none');
	$("#addListUser").css('display', 'none');
	$("#cancelAddListUser").fadeIn();
}
function cancelUserClicked() {
	$("#adduserbox").fadeOut();
	$("#adduserbox").attr('value', '');
	$("#cancelAddListUser").fadeOut(function() {
		$("#editlist").css('display', 'inline');
		$("#deleteList").css('display', 'inline');
		$("#listmenu").css('display', 'inline');
		$("#deletecheckedList").css('display', 'inline');
		$("#addListUser").css('display', 'inline');
		$("#listMenuButtons").css('position', 'absolute');
	});
}
function addListUser(text) {
	$.get("script/addlistuser.php", {
		t : encodeURIComponent(text.value)
	}, function(data) {
		if (data == "0") {
			block = true;
			alert("No such user '" + text.value + "'.");
			setTimeout("block = false", 100);
		} else if (data == "1") {
			block = true;
			alert("User '" + text.value + "' already has access to list.");
			setTimeout("block = false", 100);
		} else if (data == "2") {
			block = true;
			alert("Successfully added user '" + text.value + "' to list.");
			setTimeout("block = false", 100);
			cancelUserClicked();
		} else {
			block = true;
			alert("Error adding user to list.");
			setTimeout("block = false", 100);
			cancelUserClicked();
		}
	});
	lastTypedId = 0;
}
function exportItemAsList(o, isParent) {
	if (!isParent) {
		alert("Idea must have children to export as a list.");
	} else {
		$.get("script/exportitemaslist.php", {
			o : o
		}, function(data) {
			cancelListClicked();
			changeList(data);
			getLists();
			if (parseInt($("#listMenuButtons").css('left')) == 0) {
				showHideListMenu();
			}
		});
	}
	lastTypedId = 0;
}

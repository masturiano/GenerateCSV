// JavaScript Document

	function menu(url)	{
		$("#contentFrame").attr('src',url);
	}
	function validateString(ObjName){
		
		if(ObjName.val().length == 0){
			ObjName.addClass("ui-state-error");
			dialogAlert('Required field');
			return false;
		}
		else{
			ObjName.removeClass("ui-state-error");
			return true;
		}
	}
	function dialogAlert(msg){
		$("#dialogAlert").dialog("destroy");
		$("#dialogMsg").html(msg);
		$("#dialogAlert").dialog({
			modal: true,
			buttons: {
					Ok: function() {
						$(this).dialog('close');
					}
				}
			});	
	}
	
	function logOut(){
		$("#dialog").dialog("destroy");
		$("#msg").html('Are you sure you want to logout?');
		$("#dialog").dialog({
			height: 100,
			modal: true,
			closeOnEscape: false,
			buttons: {
					'Yes': function() {
						$.ajax({
							url: "login/logout",
							type: "POST",
							success: function(msg){
								eval(msg);
							}				
						});	
					},
					'No': function() {
						$(this).dialog('close');
					}
			}
		});	
	}
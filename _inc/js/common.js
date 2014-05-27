jQuery(document).ready( function($) {
    YPUIFunctions.initUI();
    YPVoteFormFunctions.voteformcontrole();
    YPJycroisFunctions.loadJycrois();
  	$('.projects-desc-content').each(function(){WDGProjectPageFunctions.hideOrShow(this)});
 
});


YPUIFunctions = (function($) {
    return {
	initUI: function() {
	    YPMenuFunctions.initMenuBar();

	    if ($("#finish_subscribe").length > 0) {		
		$("#container").css('padding-top', "55px");		
	    }

	    if ($("#fundingproject").val()) { 				
		$("#goalsum_fixe").click(function() { $("#goalsum_flexible_param").hide(); $("#goalsum_fixe_param").show();}); 		
		$("#goalsum_flexible").click(function() { $("#goalsum_flexible_param").show(); $("#goalsum_fixe_param").hide();});
		
		$("#goal_search").change(function() {
		    $("#goal").val(Math.round($("#goal_search").val() * $("#campaign_multiplier").val()));
		    $("#goalsum_campaign_multi").text($("#goal").val() + $("#monney").val());
		});
		$("#minimum_goal_search").change(function() {
		    $("#minimum_goal").val(Math.round($("#minimum_goal_search").val() * $("#campaign_multiplier").val()));
		    $("#goalsum_min_campaign_multi").text($("#minimum_goal").val() + $("#monney").val());
		});
		$("#maximum_goal_search").change(function() {
		    $("#maximum_goal").val(Math.round($("#maximum_goal_search").val() * $("#campaign_multiplier").val()));
		    $("#goalsum_max_campaign_multi").text($("#maximum_goal").val() + $("#monney").val());
		});
	    
		$(".radiofundingtype").change(function(){
		    $("#goal").val("");
		    if ($("#fundingproject").attr("checked") == "checked") {
			$("#fundingdevelopment_param").hide();
			$(".min_amount_value").html($("#min_amount_project").val());
		    }
		    if ($("#fundingdevelopment").attr("checked") == "checked") {
			$("#fundingdevelopment_param").show();
			$(".min_amount_value").html($("#min_amount_development").val());
		    }
		});
	    }
	    
	    if ($("#input_invest_amount_part").length > 0) {
		$("#input_invest_amount_part").change(function() {
		    YPUIFunctions.checkInvestInput();
		});
		
		$("#link_validate_invest_amount").click(function() {
		    $("#validate_invest_amount_feedback").show();
		});
		
		$("#invest_form").submit(function() {
		    return YPUIFunctions.checkInvestInput();
		});
	    }
	    
	    if ($("#company_status").length > 0) {
		$("#company_status").change(function() { 
		    if ($("#company_status").val() == "Autre") $("#company_status_other_zone").show(); 
		    else  $("#company_status_other_zone").hide(); 
		});
	    }
	    
	    if ($("#item-body").length > 0) {
		var aTabs = ["activity", "following", "followers", "projects"];
		var nHeight = 100;
		for (var i = 0; i < aTabs.length; i++) {
		    nHeight = Math.max(nHeight, $("#item-body-" + aTabs[i]).height());
		}
		for (var i = 0; i < aTabs.length; i++) {
		    $("#item-body-" + aTabs[i]).height(nHeight);
		}
	    }
	    
	    if ($(".wp-editor-wrap")[0]) {
		setInterval(YPUIFunctions.onRemoveUploadInterval, 1000);
	    }
	    
	    if ($(".home-large-project").length > 0) {
		$(".home-large-project").each(function() {
		    var descdiv_elmt = $(this).find(".description-zone");
		    var descsum_elmt = $(this).find(".description-summary");
		    var descdisc_elmt = $(this).find(".description-discover");
		    var videodiv_elmt = $(this).find(".video-zone");
		    var descmiddiv_elmt = $(this).find(".description-middle");
		    var iframe_elmt = $(this).find(".video-zone>iframe");
		    if (iframe_elmt.length > 0) $(descdiv_elmt).height($(iframe_elmt).height());
		    else $(descdiv_elmt).height($(videodiv_elmt).height());
		    var remainheight = $(descdiv_elmt).height() - $(descsum_elmt).height() - $(descdisc_elmt).height();
		    $(descmiddiv_elmt).css("top", $(descsum_elmt).height() - $(descmiddiv_elmt).height() / 2 + remainheight / 2);
		});
	    }
	    if ($(".home-activity-list").length > 0) {
		setTimeout(function() {YPUIFunctions.onSlideHomeActivity(); }, YPUIFunctions.homeslideInterval);
	    }
	    if ($(".home-blog-list-nav").length > 0) {
		$(".home-blog-list-nav a").click(function() {
		    $(".home-blog-list-nav a").removeClass("selected");
		    $(this).addClass("selected");
		    $(".home-blog-list").animate(
			{ marginLeft: - $(this).data('targetitem') * YPUIFunctions.homeblogItemWidth}, 
			500
		    );
		});
	    }
	},
	
	onRemoveUploadInterval: function() {
	    if ($(".media-frame-menu")[0]) $(".media-frame-menu").remove();
	    if ($(".media-frame-router")[0]) $(".media-frame-router").show();
	},
	
	homeblogItemWidth: 570,
	homeslideItemWidth: 960,
	homeslideInterval: 3000,
	onSlideHomeActivity: function() {
	    var currentMargin = parseInt($(".home-activity-list").css("margin-left").replace("px", ""));
	    currentMargin -= YPUIFunctions.homeslideItemWidth;
	    if ($(".home-activity-list").width() < (currentMargin * -1 + 1)) currentMargin = 0;
	    $(".home-activity-list").animate(
		{ marginLeft: currentMargin}, 
		500
	    );
	    
	    setTimeout(function() {YPUIFunctions.onSlideHomeActivity(); }, YPUIFunctions.homeslideInterval);
	},
	
	checkInvestInput: function() {
	    $(".invest_error").hide();
	    $(".invest_success").hide();
	    
	    var bValidInput = true;
	    if (!$.isNumeric($("#input_invest_amount_part").val())) {
		$("#invest_error_general").show();
		bValidInput = false;
	    } else {
		$("#input_invest_amount").text($("#input_invest_part_value").val() * $("#input_invest_amount_part").val());
		
		if ($("#input_invest_amount").text() != Math.floor($("#input_invest_amount").text())) {
		    $("#invest_error_integer").show();
		    bValidInput = false;
		}
		if (parseInt($("#input_invest_amount").text()) < $("#input_invest_min_value").val()) {
		    $("#invest_error_min").show();
		    bValidInput = false;
		}
		if (parseInt($("#input_invest_amount").text()) > $("#input_invest_max_value").val()) {
		    $("#invest_error_max").show();
		    bValidInput = false;
		}
		var nAmountInterval = $("#input_invest_max_value").val() - parseInt($("#input_invest_amount").text()); 		
		if (nAmountInterval < $("#input_invest_min_value").val() && nAmountInterval > 0) { 		
		    $("#invest_error_interval").show(); 		
		    bValidInput = false; 		
		}
	    }
	    if (bValidInput) {
		$("#invest_success_amount").text( parseInt($("#input_invest_amount_total").val()) + parseInt($("#input_invest_amount").text()));
		$(".invest_success").show();
	    }
	    
	    $("#input_invest_amount_part").css("color", bValidInput ? "green" : "red");
	    return bValidInput;
	},
	
	switchProfileTab: function(sType) {
	    var aTabs = ["activity", "following", "followers", "projects"];
	    for (var i = 0; i < aTabs.length; i++) {
		$("#item-body-" + aTabs[i]).hide();
		$("#item-submenu-" + aTabs[i]).removeClass("selected");
	    }
	    $("#item-body-" + sType).show();
	    $("#item-submenu-" + sType).addClass("selected");
	}
    }
})(jQuery);

YPMenuFunctions = (function($){
    return {
	initMenuBar: function() {
	    $("#menu_item_connection").mouseenter(function(){
		$("#submenu_item_connection").css("top", $(document).scrollTop() + $("#navigation").height());
		$("#submenu_item_connection").css("left", $("#menu_item_connection").position().left + $("#menu_item_connection").width() - $("#submenu_item_connection").width() - 1);
		clearTimeout($("#menu_item_connection").data('timeoutId'));
		$("#submenu_item_connection").fadeIn("slow");
	    }).mouseleave(function(){
		var timeoutId = setTimeout(function(){
		    $("#submenu_item_connection").fadeOut("slow");
		}, 650);
		$("#menu_item_connection").data('timeoutId', timeoutId); 
	    });
	    
	    $("#submenu_item_connection").mouseenter(function(){
		clearTimeout($("#menu_item_connection").data('timeoutId'));
		$("#submenu_item_connection").fadeIn("slow");
	    }).mouseleave(function(){
		var timeoutId = setTimeout(function(){
		    $("#submenu_item_connection").fadeOut("slow");
		}, 650);
		$("#menu_item_connection").data('timeoutId', timeoutId); 
	    });
	    
	    $("#share_btn").mouseup(function() {
		$("#share_btn_zone").show();
	    });
	    
	    $("#popup_share_close").mouseup(function() {
		$("#popup_share").toggle();
	    });
	},
	
	refreshMenuBar: function() {
	    $("#navigation").css("top", $(window).scrollTop());
	}
    }
})(jQuery);

 
/* FORMULAIRE VOTE*/
YPVoteFormFunctions = (function($) {
    return {
	voteformcontrole:function() {
	    $("#btn-validate_project-true").click(function(){ 
		$("#validate_project-true").show();
		$("#validate_project-false").hide();
	    });
	    $("#btn-validate_project-false").click(function(){ 
		$("#validate_project-false").show();
		$("#validate_project-true").hide();
	    });
    	}
    }   
})(jQuery);

/* FIN FORMULAIRE VOTE*/


/* J'Y CROIS*/
YPJycroisFunctions = (function($){
    return {
	loadJycrois: function() {
	    $("#jcrois_pas").click(function () {
		$("#tab-count-jycrois").load('single-campaign.php');
	    });

	    $("#jcrois").click(function() {
		$("#tab-count-jycrois").load('single-campaign.php');
	    });
	}

    }
})(jQuery);
/* FIN J'Y CROIS */
/* Projet */

WDGProjectPageFunctions=(function($) {
	return {
		currentDiv:0,
		move_picture:function(campaign_id) {
		    $('#img-container').draggable({
				axis: "y"
		    }); // appel du plugin
		    $('#img-container').draggable('enable');
		    $('#reposition-cover').text('Sauvegarder');
		    $('#reposition-cover').attr("onclick", "WDGProjectPageFunctions.save_position("+campaign_id+")");
		    $("#head-content").css({ opacity: 0 });
		    $("#head-content").css({ 'z-index': -1 });
		},

		save_position:function(campaign_id){
		    $("#head-content").css({ opacity: 1 });
		    $("#head-content").css({ 'z-index': 2 });
		    $('#img-container').draggable('disable');
		    $('#reposition-cover').text('Repositionner');
		    $('#reposition-cover').attr("onclick", "WDGProjectPageFunctions.move_picture("+campaign_id+")");
		    $.ajax({
		              'type' : "POST",
		              'url' : ajax_object.ajax_url,
		              'data': { 
		                      'action':'setCoverPosition',
		                      'top' : $('#img-container').css('top'),
		                      'id_campaign' : campaign_id
		                    }
		            }).done()
		},

		move_cursor:function(campaign_id){
		  $('#move-cursor').text('Sauvegarder la position du curseur');
		  $('#move-cursor').attr("onclick", "WDGProjectPageFunctions.save_cursor_position("+campaign_id+")");
		  $('#map-cursor').draggable({
		    containment: '#project-map'
		    });
		  $('#map-cursor').draggable('enable');
		},

		save_cursor_position:function(campaign_id){
			$('#move-cursor').text('Modifier la position du curseur');
			$('#move-cursor').attr("onclick", "WDGProjectPageFunctions.move_cursor("+campaign_id+")");
			$('#map-cursor').draggable('disable');
			$.ajax({
			        'type' : "POST",
			        'url' : ajax_object.ajax_url,
			        'data': { 
			                    'action':'setCursorPosition',
			                    'top' : $('#map-cursor').css('top'),
			                    'left' : $('#map-cursor').css('left'),
			                    'id_campaign' : campaign_id
			                }
			       }).done(); 
		},

		update_jycrois:function(jy_crois,campaign_id,home_url){
	 		var img_url=home_url+'/images/';
			if(jy_crois==0) {
	  			jy_crois_temp=1;
	  			img_url+='grenage_projet.jpg';
	 			$('#jy-crois-btn').css('background-image','url("'+img_url+'")');
	  			$('#jy-crois-txt').text('J\'y crois');
			}else{
	  			jy_crois_temp=0;
	  			img_url+='jycrois_gris.png';
	  			$('#jy-crois-txt').text('');
	  			$('#jy-crois-btn').css('background-image','url("'+img_url+'")');
			}
			var actual_text=$('#nb-jycrois').text();
	            if (jy_crois==1) {
	              $('#nb-jycrois').text(parseInt(actual_text)+1);
	            }
	            else{
	               $('#nb-jycrois').text(parseInt(actual_text)-1);
	            }
				$('.jy-crois').attr("href", "javascript:WDGProjectPageFunctions.update_jycrois("+jy_crois_temp+","+campaign_id+",\""+home_url+"\")");
	   			$.ajax({
			            	'type' : "POST",
			            	'url' : ajax_object.ajax_url,
			            	'data': { 
			                      'action':'update_jy_crois',
			                      'jy_crois' : jy_crois,
			                      'id_campaign' : campaign_id
			                    }
			            }).done(function(){});
		},

		share_btn_click:function() {
			$("#dialog").dialog({
			    width: '350px',
			    zIndex: 5,
			    draggable: false,
			    resizable: false,
			    autoOpen: false,
			    modal: true,
			    show: {
				effect: "blind",
				duration: 300
			    },
			    hide: {
				 effect: "blind",
				duration: 300
			    }
			});
	 		$("#dialog").dialog("open"); 
		},

		print_vote_form:function(){
		    $("#vote-form").animate({ 
	        	bottom: "-686px"
		    }, 500 );
		    $(".description-discover").css('background-color','#333');
		},
		//Description projet
		hideOthers:function(currentDiv){
			var index=0;
			jQuery.noConflict();
	 		jQuery('.projects-desc-content').each(function(){
		 		if(index!=currentDiv){
		 			jQuery(this).find('.projects-more').slideDown(200);
		 			jQuery(this).find('p:gt(0)').slideUp(400);
		 		
		 		}
		 		index++;
			});
		},

		hideOrShow:function(thisthis){
	  		if($(thisthis).find('p').length>1){
	  			$(thisthis).css("cursor", "pointer");
		  		$(thisthis).find('p:lt(1)').append('<div class="projects-more" data-value="'+WDGProjectPageFunctions.currentDiv+'" >Lire plus! </div>');
		  		$(thisthis).click(function(){
						$this=$(this);
						$(this).find('.projects-more').hide(400,function(){
							$this.find('p').slideDown(400);
						});
						$show=false;
						WDGProjectPageFunctions.hideOthers($(this).find('.projects-more').attr("data-value"));
				} 
		  			);
	  		}
	   		$(thisthis).find('p:gt(0)').hide();
	   		WDGProjectPageFunctions.currentDiv++;
   		}

	}
})(jQuery);

   
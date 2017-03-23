jQuery(document).ready( function($) {
	WDGInvestPageFunctions.initUI();
});

WDGInvestPageFunctions = (function($) {
	return {
		forceInvestSubmit: false,
		initUI:function() {
			//Interactions choix de contrepartie
			if ($("#invest_form").length > 0) {
				//Changement de montant
			    $("#input_invest_amount_part").on( 'keyup change', function () {
					$("#validate_invest_amount_feedback").slideUp();
					$("#link_validate_invest_amount").slideDown();
					WDGInvestPageFunctions.checkInvestInput();
			    });
                            
				//Changement de contrepartie
				if($("#reward-selector").length>0){
					$("#reward-selector input:checked").closest("li").addClass("selected");

					$("#reward-selector input").click(function() {
						$("#validate_invest_amount_feedback").slideUp();
						$("#link_validate_invest_amount").slideDown();
						WDGInvestPageFunctions.changeInvestInput();
						WDGInvestPageFunctions.checkInvestInput();
					});
				}
                            
				//Clic sur valider
			    $("#link_validate_invest_amount").click(function() {
					WDGInvestPageFunctions.checkInvestInput();
					$("#link_validate_invest_amount").slideUp();
					$("#validate_invest_amount_feedback").show();
					$('html, body').animate({scrollTop: $('#link_validate_invest_amount').offset().top - $("#navigation").height()}, "slow"); 
			    });
				
				//Validation du formulaire
				$("#invest_form").submit(function(e) {
					var formSelf = this;
					if (!WDGInvestPageFunctions.forceInvestSubmit && ($(formSelf).data("hasfilledinfos") != "1" || $("#invest_type").val() != "user" || Number($("#input_invest_amount_part").val()) > 250)) {
						e.preventDefault();
						$("#invest_form_button").hide();
						$("#invest_form_loading").show();
						$.ajax({
							'type' : "POST",
							'url' : ajax_object.ajax_url,
							'data': { 
								'action': 'check_invest_input',
								'campaign_id': $(formSelf).data("campaignid"),
								'invest_value': $("#input_invest_amount_part").val(),
								'invest_type' : $("#invest_type").val()
							}
						}).done(function(result){
							WDGInvestPageFunctions.formInvestReturnEvent(result);
						});
					}
				});
			}
			
			if ($("#userkyc_form").length > 0) {
				WDGInvestPageFunctions.initKycForm();
			}
		},
		
		checkInvestInput: function() {
			$(".invest_error").hide();
			$(".invest_success").hide();

			var bValidInput = true;
			$("#input_invest_amount_part").val(($("#input_invest_amount_part").val()).replace(/,/g,"."));
                        
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
                            
				//Vérification Contreparties
				if($("#reward-selector").length>0){
					var rewardSelectedAmount = parseInt($("#reward-selector input:checked~.reward-amount").text());
					var rewardSelectedRemaining = parseInt($("#reward-selector input:checked~.reward-remaining").text());

					if(rewardSelectedRemaining <= 0) {
						$("#invest_error_reward_remaining").show(); 		
						bValidInput = false; 
					}

					if (parseInt($("#input_invest_amount").text()) < rewardSelectedAmount){
						$("#invest_error_reward_insufficient").show(); 		
						bValidInput = false; 
					}
				}
			}
			
			if (bValidInput) {
			    $("#invest_success_amount").text( parseInt($("#input_invest_amount_total").val()) + parseInt($("#input_invest_amount").text()));
			    $("#invest_show_amount").text( parseInt($("#input_invest_amount").text()));
				$("#invest_show_reward").text( ($("#reward-selector input:checked").closest("li").find(".reward-name").text()));
				$(".invest_success").show();
			}

			$("#input_invest_amount_part").css("color", bValidInput ? "green" : "red");
			return bValidInput;
		},
		
		changeInvestInput: function(){
			//Change apparence élément sélectionné
			$("#reward-selector li").removeClass("selected");
			$("#reward-selector input:checked").closest("li").addClass("selected");

			//Ajuster le champ de montant choisi à la contrepartie selectionnee
			var rewardSelectedAmount = parseInt($("#reward-selector input:checked~.reward-amount").text());
			$("#input_invest_amount_part").val(rewardSelectedAmount);
		},
		
		isUserInfosFormDisplayed: false,
		showUserInfosForm: function(jsonInfos) {
			$("#wdg-lightbox-userinfos").show();
			$("#wdg-lightbox-orgainfos").hide();
			$("#wdg-lightbox-userkyc").hide();
			$("#lightbox_userinfo_form_button").show();
			$("#lightbox_userinfo_form_loading").hide();
			$("#lightbox_userinfo_form_errors").empty();
			for (var i = 0; i < jsonInfos.errors.length; i++) {
				$("#lightbox_userinfo_form_errors").append("<li>"+jsonInfos.errors[i]+"</li>");
			}
			
			if (!WDGInvestPageFunctions.isUserInfosFormDisplayed) {
				WDGInvestPageFunctions.isUserInfosFormDisplayed = true;
				$("#lightbox_userinfo_form").submit(function(e) {
					e.preventDefault();
					$("#lightbox_userinfo_form_button").hide();
					$("#lightbox_userinfo_form_loading").show();
					$.ajax({
						'type' : "POST",
						'url' : ajax_object.ajax_url,
						'data': { 
							'action': 'save_user_infos',
							'campaign_id': $("#invest_form").data("campaignid"),
							'invest_type': $("#invest_type").val(),
							'email': $("#update_email").val(),
							'gender': $("#update_gender").val(),
							'firstname': $("#update_firstname").val(),
							'lastname': $("#update_lastname").val(),
							'birthday_day': $("#update_birthday_day").val(),
							'birthday_month': $("#update_birthday_month").val(),
							'birthday_year': $("#update_birthday_year").val(),
							'birthplace': $("#update_birthplace").val(),
							'nationality': $("#update_nationality").val(),
							'address': $("#update_address").val(),
							'postal_code': $("#update_postal_code").val(),
							'city': $("#update_city").val(),
							'country': $("#update_country").val(),
							'telephone': $("#update_mobile_phone").val()
						}
					}).done(function(result){
						WDGInvestPageFunctions.formInvestReturnEvent(result);
					});
				});
				
				$("#wdg-lightbox-userinfos .wdg-lightbox-button-close").click(function(e) {
					WDGInvestPageFunctions.isUserInfosFormDisplayed = false;
					WDGInvestPageFunctions.closeInvestLightbox();
				});
			}
		},
		
		isOrgaInfosFormDisplayed: false,
		showOrgaInfosForm: function(jsonInfos) {
			$("#wdg-lightbox-userinfos").hide();
			$("#wdg-lightbox-orgainfos").show();
			$("#wdg-lightbox-userkyc").hide();
			$("#lightbox_orgainfos_form_button").show();
			$("#lightbox_orgainfos_form_loading").hide();
			$("#lightbox_orgainfos_form_errors").empty();
			for (var i = 0; i < jsonInfos.errors.length; i++) {
				$("#lightbox_orgainfos_form_errors").append("<li>"+jsonInfos.errors[i]+"</li>");
			}
			if (jsonInfos.org_name != undefined) {
				$("#org_legalform").val(jsonInfos.org_legalform);
				$("#org_idnumber").val(jsonInfos.org_idnumber);
				$("#org_rcs").val(jsonInfos.org_rcs);
				$("#org_capital").val(jsonInfos.org_capital);
				$("#org_ape").val(jsonInfos.org_ape);
				$("#org_address").val(jsonInfos.org_address);
				$("#org_postal_code").val(jsonInfos.org_postal_code);
				$("#org_city").val(jsonInfos.org_city);
				$("#org_nationality").val(jsonInfos.org_nationality);
				
				$("#org_name").hide();
				$("#org_name_label").text(jsonInfos.org_name);
				$("#org_email").hide();
				$("#org_email_label").text(jsonInfos.org_email);
				$("#org_capable_label").hide();
			}
			
			if (!WDGInvestPageFunctions.isOrgaInfosFormDisplayed) {
				WDGInvestPageFunctions.isOrgaInfosFormDisplayed = true;
				$("#lightbox_orgainfos_form").submit(function(e) {
					e.preventDefault();
					$("#lightbox_orgainfos_form_button").hide();
					$("#lightbox_orgainfos_form_loading").show();
					$.ajax({
						'type' : "POST",
						'url' : ajax_object.ajax_url,
						'data': { 
							'action': 'save_orga_infos',
							'invest_type' : $("#invest_type").val(),
							'campaign_id': $("#invest_form").data("campaignid"),
							'org_name': $("#org_name").val(),
							'org_email': $("#org_email").val(),
							'org_legalform': $("#org_legalform").val(),
							'org_idnumber': $("#org_idnumber").val(),
							'org_rcs': $("#org_rcs").val(),
							'org_capital': $("#org_capital").val(),
							'org_ape': $("#org_ape").val(),
							'org_address': $("#org_address").val(),
							'org_postal_code': $("#org_postal_code").val(),
							'org_city': $("#org_city").val(),
							'org_nationality': $("#org_nationality").val(),
							'org_capable': ($("#org_capable").is(':checked') ? '1': 0)
						}
					}).done(function(result){
						WDGInvestPageFunctions.formInvestReturnEvent(result);
					});
				});
				
				$("#wdg-lightbox-orgainfos .wdg-lightbox-button-close").click(function(e) {
					WDGInvestPageFunctions.isOrgaInfosFormDisplayed = false;
					WDGInvestPageFunctions.closeInvestLightbox();
				});
			}
		},
		
		isUserKycFormDisplayed: false,
		showUserKycForm: function(jsonInfos) {
			$("#wdg-lightbox-userinfos").hide();
			$("#wdg-lightbox-orgainfos").hide();
			$("#wdg-lightbox-userkyc").show();
			$("#userkyc_form_button").show();
			$("#userkyc_form_loading").hide();
			$("#userkyc_form_errors").empty();
			for (var i = 0; i < jsonInfos.errors.length; i++) {
				$("#userkyc_form_errors").append("<li>"+jsonInfos.errors[i]+"</li>");
			}
			
			if (!WDGInvestPageFunctions.isUserKycFormDisplayed) {
				WDGInvestPageFunctions.isUserKycFormDisplayed = true;
				WDGInvestPageFunctions.initKycForm();
				
				$("#wdg-lightbox-userkyc .wdg-lightbox-button-close").click(function(e) {
					WDGInvestPageFunctions.isUserKycFormDisplayed = false;
					WDGInvestPageFunctions.closeInvestLightbox();
				});
			}
		},
		
		initKycForm: function() {
			$("#userkyc_form").submit(function(e) {
				e.preventDefault();
				$("#userkyc_form_button").hide();
				$("#userkyc_form_loading").show();
				var formData = new FormData();
				formData.append('action', 'save_user_docs');
				formData.append('campaign_id', $("#invest_form").data("campaignid"));
				if ($('#org_doc_id').length > 0) {
					formData.append('org_doc_id', $('#org_doc_id')[0].files[0]);
					formData.append('org_doc_home', $('#org_doc_home')[0].files[0]);
					formData.append('org_doc_kbis', $('#org_doc_kbis')[0].files[0]);
					formData.append('org_doc_status', $('#org_doc_status')[0].files[0]);
				} else {
					formData.append('user_doc_id', $('#user_doc_id')[0].files[0]);
					formData.append('user_doc_home', $('#user_doc_home')[0].files[0]);
				}
				$.ajax({
					'type' : "POST",
					'url' : ajax_object.ajax_url,
					'processData': false,
					'contentType': false,
					'data': formData
				}).done(function(result){
					var jsonResult = JSON.parse(result);
					response = jsonResult.response;
					$("#userkyc_form_loading").hide();
					if ( response == "kyc" ) {
						$("#userkyc_form_button").show();
						$("#userkyc_form_errors").empty();
						for (var i = 0; i < jsonResult.errors.length; i++) {
							$("#userkyc_form_errors").append("<li>"+jsonResult.errors[i]+"</li>");
						}
					} else {
						$("#userkyc_form_success").show();
					}
					
				});
			});
		},
		
		closeInvestLightbox: function() {
			$("#invest_form_button").show();
			$("#invest_form_loading").hide();
		},
		
		formInvestReturnEvent: function(result) {
			var response = "";
			if (result != "") { 
				var jsonResult = JSON.parse(result);
				response = jsonResult.response;
			}
			switch (response) {
				case "edit_user":
					WDGInvestPageFunctions.showUserInfosForm(jsonResult);
					break;
				case "new_organization":
					WDGInvestPageFunctions.showOrgaInfosForm(jsonResult);
					break;
				case "edit_organization":
					WDGInvestPageFunctions.showOrgaInfosForm(jsonResult);
					break;
				case "kyc":
					WDGInvestPageFunctions.showUserKycForm(jsonResult);
					break;
				default:
					WDGInvestPageFunctions.forceInvestSubmit = true;
					$("#invest_form").submit();
					break;
			}
		}
		
	};
})(jQuery);
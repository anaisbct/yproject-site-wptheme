function WDGCampaignDashboard() {
	this.walletTimetableDatatable;
    this.initWithHash();
	this.initMenu();
	this.drawTimetable();
	this.initAjaxForms();
	this.initOrgaForms();
}

/**
 * Initialise l'affichage avec le # de l'url
 */
WDGCampaignDashboard.prototype.initWithHash = function() {

	var sCurrentTab = window.location.hash.substring(1);
	if (sCurrentTab !== '') {
		this.switchTab( sCurrentTab );
	} else {
		this.switchTab( 'home' );
	}
	
};

/**
 * Initialise le menu
 */
WDGCampaignDashboard.prototype.initMenu = function() {
	
	var self = this;
	$( 'ul.nav-menu li a' ).each( function() {
		$( this ).click( function() {
			self.switchTab( $( this ).data( 'tab' ) );
		} );
	} );
	
};

/**
 * Change d'onglet
 */
WDGCampaignDashboard.prototype.switchTab = function(sType) {
	
	$( 'ul.nav-menu li' ).removeClass( 'selected' );
	$( 'div#item-body > div.item-body-tab' ).hide();
	
	$( 'ul.nav-menu li#menu-item-' + sType ).addClass( 'selected' );
	$( 'div#item-body > div#item-body-' + sType ).show();
	
};

/**
 * Gère les formulaires ajax
 */
WDGCampaignDashboard.prototype.initAjaxForms = function() {
	
	var self = this;
	$( 'form.ajax-db-form' ).submit( function( e ) {
		
		if ( $(this).attr( 'action' ) != '' && $(this).attr( 'action' ) != undefined ) {
			return;
		}
		e.preventDefault();
		if ($(this).data("action")==undefined) return false;
		var thisForm = $(this);

		//Receuillir informations du formulaire
		var data_to_update = {
			'action': $(this).data("action"),
			'campaign_id': campaign_id
		};

		$(this).find(".field-value").each(function(index){
			 var id = $(this).data('id');
			 switch ($(this).data("type")){
				 case 'datetime':
					 var sDate = $(this).find("input:eq(0)").val();
					 var aDate = sDate.split('/');
					 data_to_update[id] = aDate[1]+'/'+aDate[0]+'/'+aDate[2]+"\ "
						 + $(this).find("select:eq(0)").val() +':'
						 + $(this).find("select:eq(1)").val();
					 break;
				 case 'editor':
					 data_to_update[id] = tinyMCE.get(id).getContent();
					 break;
				 case 'check':
					 data_to_update[id] = $("#"+id).is(':checked');
					 break;
				 case 'multicheck':
					 var data_temp = new Array();
					 $('input', this).each(function() {
						 if ($(this).is(':visible') && $(this).is(':checked')) {
							 data_temp.push($(this).val());
						 }
					 });
					 data_to_update[id] = data_temp;
					 break;
				 case 'text':
				 case 'number':
				 case 'date':
				 case 'link':
				 case 'textarea':
				 case 'select':
				 default:
					 data_to_update[id] = $(':input', this).val();
					 break;
			 }
			 if(data_to_update[id] == undefined){
				 delete data_to_update[id];
			 }
		 });

		//Désactive les champs
		var save_button = $("#"+$(this).attr("id")+"_button");
		save_button.find(".button-text").hide();
		save_button.find(".button-waiting").show();
		$(":input", this).prop('disabled', true);

		thisForm.find('.feedback_save span').fadeOut();

		//Envoi de requête Ajax
		$.ajax({
			'type': "POST",
			'url': ajax_object.ajax_url,
			'data': data_to_update
		}).done(function (result) {
			if (result != "") {
				var jsonResult = JSON.parse(result);
				feedback = jsonResult;

				//Affiche les erreurs
				for(var input in feedback.errors){
					self.fieldError(thisForm.find('#'+input), feedback.errors[input])
				}

				for(var input in feedback.success){
					var thisinput = thisForm.find('#'+input)
					self.removeFieldError(thisinput);
					thisinput.closest(".field-value").parent().find('i.fa.validation').remove();
					thisinput.addClass("validation");
					thisinput.closest(".field-value").after('<i class="fa fa-check validation" aria-hidden="true"></i>');
				}

				//Scrolle jusqu'à la 1ère erreur et la sélectionne
				var firsterror = thisForm.find(".error").first();
				if(firsterror.length == 1){
					self.scrollTo(firsterror);
					//La sélection (ci-dessous) Ne fonctione ne marche pas
					firsterror.focus();
					thisForm.find('.save_errors').fadeIn();
				} else {
					thisForm.find('.save_ok').fadeIn();                          
				}

				// Enregistrer l'organisation liée au projet dans tab-organization
				if (($("#orgainfo_form.db-form").data("action")) == "save_project_organization"){
					//Afficher le bouton d'édition de l'organisation après enregistrement de la liaison
					self.updateEditOrgaBtn(thisForm);
					//Mise à jour du formulaire d'édition après enregistrement de la liaison
					self.updateOrgaForm(feedback);
					//Mise à jour des liens de téléchargement des docs du formulaire d'édition
					self.updateOrgaFormDoc(feedback);
					$("#save-mention").hide();
					$("#orgainfo_form_button").hide();
					thisForm.find('.save_ok').hide();
					$("#wdg-lightbox-valid-changeOrga").css('display', 'block');
					new_project_organization = $("#new_project_organization option:selected").val();
				}
			}
		}).fail(function() {
			thisForm.find('.save_fail').fadeIn();
		}).always(function() {
			//Réactive les champs
			save_button.find(".button-waiting").hide();
			save_button.find(".button-text").show();
			thisForm.find(":input").prop('disabled', false);
		});
	});
};

/**
 * Gestion des formulaires de mise à jour d'organisation
 */
WDGCampaignDashboard.prototype.initOrgaForms = function() {

	var self = this;
	$("#orgainfo_form_button").hide();//suppression bouton enregistrer
	if($("#new_project_organization").val() !== ""){
		var new_project_organization = $("#new_project_organization option:selected").val();
		$("#edit-orga-button").show();
	}
	$("#new_project_organization").change(function(e){
		e.preventDefault();
		$("#orgainfo_form_button").hide();//suppression bouton enregistrer
		if($("#new_project_organization option:selected").val() !== new_project_organization) {
			$("#edit-orga-button").hide();
			$("#orgainfo_form_button").show();//apparition bouton enregistrer
			//Suppression des éléments d'une validation précédente
			if($(".save_ok").length > 0) $(".save_ok").hide();
			if($("#orgainfo_form i.fa.validation").length > 0) $("#orgainfo_form i.fa.validation").remove();
			if($("#new_project_organization").hasClass("validation")) $("#new_project_organization").removeClass("validation");
			if($("#save-mention").is(":hidden")) $("#save-mention").show();
			//
		}else{
			if($("#save-mention").is(":visible")) $("#save-mention").hide();
			$("#edit-orga-button").show();
		}
		$("#wdg-lightbox-editOrga ul.errors li").remove();
	});
	//Suppression du feedback "enregistré" à l'ouverture de la lightbox
	$("#orgainfo_form #edit-orga-button").click(function(){
		$("#orgaedit_form").find('.save_ok').fadeOut();
	});

	//Création objet FormData (Envoi des fichiers uploadés en ajax dans le formulaire d'édition)
	$("#wdg-lightbox-editOrga form#orgaedit_form").submit(function(e){
		e.preventDefault();
		var thisForm = $(this);
		var fd = new FormData($('#wdg-lightbox-editOrga #orgaedit_form')[0]);

		//Désactive les champs
		var save_button = $("#"+$(this).attr("id")+"_button");
		save_button.find(".button-text").hide();
		save_button.find(".button-waiting").show();
		$(":input", this).prop('disabled', true);
		thisForm.find('.feedback_save span').fadeOut();

		$.ajax({
			'type' : "POST",
			'url' :ajax_object.ajax_url,
			'data': fd,
			'cache': false,
			'contentType': false,
			'processData': false,
		}).done(function(result) {
			if(result === "FALSE"){//user non connecté
				window.location.reload();//affiche message de non permission
			}else{
				var jsonResult = JSON.parse(result);
				feedback = jsonResult;
				//Vérification s'il y a des erreurs sur l'envoi de fichiers
				$("#wdg-lightbox-editOrga p.errors").remove();
				var fdFileInfo = feedback.files_info;
				var count_files_errors = 0;
				for (var doc in fdFileInfo){
					if(fdFileInfo[doc] != null) {
						if (fdFileInfo[doc]['code'] === 1){//erreur
							count_files_errors += 1;
							var errFile = $('<p class="errors">'+fdFileInfo[doc]['info']+'</p>');
							errFile.insertAfter($("#orgaedit_form input[name="+doc+"]"));
						}
						else {
							self.updateOrgaDoc(fdFileInfo, doc);//mise à jour des liens de téléchargement
						}
					}
				}
				//Vérification s'il y a des erreurs sur les champs
				var fdErrorsData = feedback.errors;
				var count_data_errors = 0;
				for (var error in fdErrorsData){
					if(error !== "") {
						count_data_errors += 1;
						var err = $("<p class='errors'>"+fdErrorsData[error]+"</p>");
						err.insertAfter($("#orgaedit_form input[name="+error+"]"));
					}
				}
				if(count_files_errors > 0) {
					var err = $("<p class='errors'>Certains champs n'ont pas été validés.</p>");
					err.insertAfter($("#orgaedit_form_button button"));
				}
				//Affichage confirmation enregistrement
				if (count_files_errors === 0 && count_data_errors === 0){
					$("#wdg-lightbox-editOrga p.errors").hide();
					thisForm.find('.save_ok').fadeIn();
					$("#wdg-lightbox-editOrga").hide();
					$("#wdg-lightbox-valid-editOrga").css('display', 'block');

					//Mise à jour du reste du formulaire d'édition (input type text)
					self.updateOrgaForm(feedback);
				}
			}
		}).fail(function() {
			thisForm.find('.save_fail').fadeIn();
		}).always(function() {
			//Réactive les champs
			save_button.find(".button-waiting").hide();
			save_button.find(".button-text").show();
			thisForm. find(":input").prop('disabled', false);
		});
	});
	//Vider les champs à l'ouverture de la lightbox de création
	//Suppression du feedback "enregistré" à l'ouverture de la lightbox
	$('#orgainfo_form #btn-new-orga').click(function(){
		$(':input', '#orgacreate_form')
			.not(':hidden')
			.val('')
			.removeAttr('checked')
			.removeAttr('selected');
		$("#orgacreate_form").find('.save_ok').fadeOut();
		$("#wdg-lightbox-newOrga p.errors").remove();
	});

	//fermeture de la lightbox de création d'organisation après enregistrement
	$("#wdg-lightbox-newOrga form.wdg-forms").submit(function(e){
		e.preventDefault();
		var thisForm = $(this);

		var campaign_id, org_name, org_email, org_representative_function, org_description, org_legalform,
		org_idnumber, org_rcs,org_capital, org_ape, org_vat, org_fiscal_year_end_month, org_address, org_postal_code,
		org_city, org_nationality, org_bankownername, org_bankowneraddress,
		org_bankowneriban, org_bankownerbic, org_capable;

		campaign_id = $('#wdg-lightbox-newOrga input[name=campaign_id]').val();
		org_name = $('#wdg-lightbox-newOrga input[name=org_name]').val();
		org_email = $('#wdg-lightbox-newOrga input[name=org_email]').val();
		org_representative_function = $('#wdg-lightbox-newOrga input[name=org_representative_function]').val();
		org_description = $('#wdg-lightbox-newOrga input[name=org_description]').val();
		org_legalform = $('#wdg-lightbox-newOrga input[name=org_legalform]').val();
		org_idnumber = $('#wdg-lightbox-newOrga input[name=org_idnumber]').val();
		org_rcs = $('#wdg-lightbox-newOrga input[name=org_rcs]').val();
		org_capital = $('#wdg-lightbox-newOrga input[name=org_capital]').val();
		org_ape = $('#wdg-lightbox-newOrga input[name=org_ape]').val();
		org_vat = $('#wdg-lightbox-newOrga input[name=org_vat]').val();
		org_fiscal_year_end_month = $('#wdg-lightbox-newOrga input[name=org_fiscal_year_end_month]').val();
		org_address = $('#wdg-lightbox-newOrga input[name=org_address]').val();
		org_postal_code = $('#wdg-lightbox-newOrga input[name=org_postal_code]').val();
		org_city = $('#wdg-lightbox-newOrga input[name=org_city]').val();
		org_nationality = $('#wdg-lightbox-newOrga #org_nationality option:selected').text();
		org_bankownername = $('#wdg-lightbox-newOrga input[name=org_bankownername]').val();
		org_bankowneraddress = $('#wdg-lightbox-newOrga input[name=org_bankowneraddress]').val();
		org_bankowneriban = $('#wdg-lightbox-newOrga input[name=org_bankowneriban]').val();
		org_bankownerbic = $('#wdg-lightbox-newOrga input[name=org_bankownerbic]').val();
		org_capable = $('#wdg-lightbox-newOrga input[name=org_capable]').is(':checked');

		//Désactive les champs
		var save_button = $("#"+$(this).attr("id")+"_button");
		save_button.find(".button-text").hide();
		save_button.find(".button-waiting").show();
		$(":input", this).prop('disabled', true);
		thisForm.find('.feedback_save span').fadeOut();

		$.ajax({  
			'type': "POST",
			'url': ajax_object.ajax_url,
			'data': {
				'action': 'save_new_organization',
				'campaign_id': campaign_id,
				'org_name': org_name,
				'org_email': org_email,
				'org_representative_function': org_representative_function,
				'org_description': org_description,
				'org_legalform': org_legalform,
				'org_idnumber': org_idnumber,
				'org_rcs': org_rcs,
				'org_capital': org_capital,
				'org_ape': org_ape,
				'org_vat': org_vat,
				'org_fiscal_year_end_month': org_fiscal_year_end_month,
				'org_address': org_address,
				'org_postal_code': org_postal_code,
				'org_city': org_city,
				'org_nationality': org_nationality,
				'org_bankownername': org_bankownername,
				'org_bankowneraddress': org_bankowneraddress,
				'org_bankowneriban': org_bankowneriban,
				'org_bankownerbic': org_bankownerbic,
				'org_capable': org_capable
			}
		}).done(function(result){
			if(result === "FALSE"){//user non connecté
				window.location.reload();//affiche message de non permission
			}else{
				var jsonResult = JSON.parse(result);
				feedback = jsonResult;

				//Vérification s'il y a des erreurs dans le formulaire
				$("#wdg-lightbox-newOrga p.errors").remove();//supprime les erreurs éventuellement affichées après un 1er enregistrement
				var errors = feedback.errors;
				var count_errors = 0;
				for (var error in errors){
					if(error !== ""){
						count_errors+=1;
						var err = $('<p class="errors">'+errors[error]+'</p>');
						if(error !== "org_capable"){
							err.insertAfter($("#orgacreate_form input[name="+error+"]"));
						}
						if(error === "org_nationality") {
							err.insertAfter($("#orgacreate_form select#org_nationality"));
						}
						if(error === "org_capable") {
							err.insertAfter($("#orgacreate_form input[name="+error+"]").next());
						}
					}
				}
				if(count_errors > 0) {
					var firsterror = thisForm.find(".errors").first();
					if(firsterror.length === 1){
						this.scrollTo(firsterror);
					}
				}
				//Affichage confirmation enregistrement
				if(count_errors === 0){
					$("#wdg-lightbox-newOrga p.errors").hide();//cache les erreurs éventuellement affichées après un 1er enregistrement
					thisForm.find('.save_ok').fadeIn();
					$("#wdg-lightbox-newOrga").hide();
					$("#wdg-lightbox-valid-newOrga").css('display', 'block');
					//Mise à jour de l'input select
					self.updateOrgaSelectInput(feedback);

					//Mise à jour du bouton d'édition
					var newname = $("#new_project_organization").find('option:selected').text();
					var edit_btn = $('#orgainfo_form').find($("#edit-orga-button"));
					edit_btn.attr("href","#");
					edit_btn.text("Editer "+newname);

					//Mise à jour du formulaire d'édition
					self.updateOrgaForm(feedback);
				}
			}
		}).fail(function() {
			thisForm.find('.save_fail').fadeIn();
		}).always(function() {
			//Réactive les champs
			save_button.find(".button-waiting").hide();
			save_button.find(".button-text").show();
			thisForm. find(":input").prop('disabled', false);

		});

	});


	$("#update_project_organization").change(function(e){
		var newval = $("#update_project_organization").val();

		if(newval!=''){
			$("#edit-orga-button").show();
			var newname = $("#update_project_organization").find('option:selected').text();
			$("#edit-orga-button").attr("href",$("#edit-orga-button").data("url-edit")+newval);

		};

	});	
};

/* Fonction de mise à jour du bouton d'édition d'une organisation
* une fois l'organisation sélectionnée et enregistrée
* @param {type} form : formulaire de saisie
*/
WDGCampaignDashboard.prototype.updateEditOrgaBtn = function(form){
   var newval = $("#new_project_organization").val();
   if(newval!== ''){
	   var edit_btn = form.find($("#edit-orga-button")).show();

	   var newname = $("#new_project_organization").find('option:selected').text();
	   edit_btn.attr("href","#");
	   edit_btn.text("Editer "+newname);
   } else {
	   edit_btn.hide();
   }
};

/**
* Fonction de mise à jour du formulaire d'édition d'une organisation
* une fois l'organisation sélectionnée et enregistrée
* @param {objet} feedback : retour ajax
*/
WDGCampaignDashboard.prototype.updateOrgaForm = function(feedback){
   $("#wdg-lightbox-editOrga #org_name").html(feedback.organization.name);
   $("#wdg-lightbox-editOrga input[name=org_email]").val(feedback.organization.email);
   $("#wdg-lightbox-editOrga input[name=org_representative_function]").val(feedback.organization.representative_function);
   $("#wdg-lightbox-editOrga input[name=org_description]").val(feedback.organization.description);
   $("#wdg-lightbox-editOrga input[name=org_legalform]").val(feedback.organization.legalForm);
   $("#wdg-lightbox-editOrga input[name=org_idnumber]").val(feedback.organization.idNumber);
   $("#wdg-lightbox-editOrga input[name=org_rcs]").val(feedback.organization.rcs);
   $("#wdg-lightbox-editOrga input[name=org_capital]").val(feedback.organization.capital);
   $("#wdg-lightbox-editOrga input[name=org_ape]").val(feedback.organization.ape);
   $("#wdg-lightbox-editOrga input[name=org_vat]").val(feedback.organization.vat);
   $("#wdg-lightbox-editOrga input[name=org_fiscal_year_end_month]").val(feedback.organization.fiscal_year_end_month);
   $("#wdg-lightbox-editOrga input[name=org_address]").val(feedback.organization.address);
   $("#wdg-lightbox-editOrga input[name=org_postal_code]").val(feedback.organization.postal_code);
   $("#wdg-lightbox-editOrga input[name=org_city]").val(feedback.organization.city);
   $("#wdg-lightbox-editOrga input[name=org_nationality]").val(feedback.organization.nationality);
   $("#wdg-lightbox-editOrga input[name=org_bankownername]").val(feedback.organization.bankownername);
   $("#wdg-lightbox-editOrga input[name=org_bankowneraddress]").val(feedback.organization.bankowneraddress);
   $("#wdg-lightbox-editOrga input[name=org_bankowneriban]").val(feedback.organization.bankowneriban);
   $("#wdg-lightbox-editOrga input[name=org_bankownerbic]").val(feedback.organization.bankownerbic);
};

/**
* Fonction de mise à jour des liens de téléchargement des documents
* uploadés de l'organisation après l'action save_project_organisation
* @param {object} feedback : infos renvoyées par l'action php
*/
WDGCampaignDashboard.prototype.updateOrgaFormDoc = function(feedback){
	if(feedback.organization.doc_bank.path != null){
		if($("#wdg-lightbox-editOrga a#org_doc_bank").length === 0){
			var link_bank = $('<a id="org_doc_bank" class="button blue-pale download-file" target="_blank" href="'+feedback.organization.doc_bank.path+'">'+feedback.organization.doc_bank.date_uploaded+'</a><br />');
			link_bank.insertBefore($("#wdg-lightbox-editOrga input[name=org_doc_bank]"));
		} else{
			$("#wdg-lightbox-editOrga a#org_doc_bank").attr("href", feedback.organization.doc_bank.path);
			$("#wdg-lightbox-editOrga a#org_doc_bank").html(feedback.organization.doc_bank.date_uploaded);
		}
	} else {
		$("#wdg-lightbox-editOrga a#org_doc_bank").remove();
	}

	if(feedback.organization.doc_kbis.path != null){
		if($("#wdg-lightbox-editOrga a#org_doc_kbis").length === 0){
			var link_kbis = $('<a id="org_doc_kbis" class="button blue-pale download-file" target="_blank" href="'+feedback.organization.doc_kbis.path+'">'+feedback.organization.doc_kbis.date_uploaded+'</a><br />');
			link_kbis.insertBefore($("#wdg-lightbox-editOrga input[name=org_doc_kbis]"));
		} else{
			$("#wdg-lightbox-editOrga a#org_doc_kbis").attr("href", feedback.organization.doc_kbis.path);
			$("#wdg-lightbox-editOrga a#org_doc_kbis").html(feedback.organization.doc_kbis.date_uploaded);
		}
	} else {
		$("#wdg-lightbox-editOrga a#org_doc_kbis").remove();
	}

	if(feedback.organization.doc_status.path != null){
		if($("#wdg-lightbox-editOrga a#org_doc_status").length === 0){
			var link_status = $('<a id="org_doc_status" class="button blue-pale download-file" target="_blank" href="'+feedback.organization.doc_status.path+'">'+feedback.organization.doc_status.date_uploaded+'</a><br />');
			link_status.insertBefore($("#wdg-lightbox-editOrga input[name=org_doc_status]"));
		} else{
			$("#wdg-lightbox-editOrga a#org_doc_status").attr("href", feedback.organization.doc_status.path);
			$("#wdg-lightbox-editOrga a#org_doc_status").html(feedback.organization.doc_status.date_uploaded);
		}
	} else {
		$("#wdg-lightbox-editOrga a#org_doc_status").remove();
	}

	if(feedback.organization.doc_id.path != null){
		if($("#wdg-lightbox-editOrga a#org_doc_id").length === 0){
			var link_id = $('<a id="org_doc_id" class="button blue-pale download-file" target="_blank" href="'+feedback.organization.doc_id.path+'">'+feedback.organization.doc_id.date_uploaded+'</a><br />');
			link_id.insertBefore($("#wdg-lightbox-editOrga input[name=org_doc_id]"));
		} else{
			$("#wdg-lightbox-editOrga a#org_doc_id").attr("href", feedback.organization.doc_id.path);
			$("#wdg-lightbox-editOrga a#org_doc_id").html(feedback.organization.doc_id.date_uploaded);
		}
	} else {
		$("#wdg-lightbox-editOrga a#org_doc_id").remove();
	}

	if(feedback.organization.doc_home.path != null){
		if($("#wdg-lightbox-editOrga a#org_doc_home").length === 0){
			var link_home = $('<a id="org_doc_home" class="button blue-pale download-file" target="_blank" href="'+feedback.organization.doc_home.path+'">'+feedback.organization.doc_home.date_uploaded+'</a><br />');
			link_home.insertBefore($("#wdg-lightbox-editOrga input[name=org_doc_home]"));
		} else{
			$("#wdg-lightbox-editOrga a#org_doc_home").attr("href", feedback.organization.doc_home.path);
			$("#wdg-lightbox-editOrga a#org_doc_home").html(feedback.organization.doc_home.date_uploaded);
		}
	} else {
		$("#wdg-lightbox-editOrga a#org_doc_home").remove();
	}
};

/**
* Fonction de mise à jour du lien de téléchargement du fichier uploadé
* @param {array} fileInfo : tableau des infos sur tous les fichiers uploadés
* @param {String} document : nom du document uploadé
*/
WDGCampaignDashboard.prototype.updateOrgaDoc = function(fileInfo, document){
	if(fileInfo[document]['info'] !== null) { //il y a un fichier à uploader
		if($("#wdg-lightbox-editOrga a#"+document).length === 0){
			var link = $('<a id="'+document+'" class="button blue-pale download-file" target="_blank" href="'+fileInfo[document]['info']+'">'+fileInfo[document]['date']+'</a><br />');
			link.insertBefore($("#wdg-lightbox-editOrga input[name="+document+"]"));
		}
		else{
			$("#wdg-lightbox-editOrga a#"+document).attr("href", fileInfo[document]['info']);
			$("#wdg-lightbox-editOrga a#"+document).html(fileInfo[document]['date']);
		}
	}
};

/**
* Fonction de mise à jour du select pour le choix de l'organisation
* après la création d'une organisation depuis le tableau de bord
* @param {objet} feedback : retour ajax
*/
WDGCampaignDashboard.prototype.updateOrgaSelectInput = function(feedback){
	var orgaName = feedback.organization.name;
	var orgaWpref = feedback.organization.wpref;

	$("#orgainfo_form #new_project_organization").append(new Option(orgaName, orgaWpref));
	$("#orgainfo_form #new_project_organization option:selected").removeAttr('selected');
	$("#orgainfo_form #new_project_organization option[value="+orgaWpref+"]").attr("selected", "selected");
};

WDGCampaignDashboard.prototype.getContactsTable = function(inv_data, campaign_id) {
	var self = this;
	
	$.ajax({
		'type' : "POST",
		'url' : ajax_object.ajax_url,
		'data': {
			'action':'create_contacts_table',
			'id_campaign':campaign_id,
			'data' : inv_data
		}
	}).done(function(result){
		//Affiche resultat requete Ajax une fois reçue
		$('#ajax-contacts-load').after(result);
		$('#ajax-loader-img').hide();//On cache la roue de chargement.

		YPUIFunctions.initQtip();

		//Création du tableau dynamique dataTable
		self.table = $('#contacts-table').DataTable({
			scrollX: '100%',
			scrollY: '70vh', //Taille max du tableau : 70% de l'écran
			scrollCollapse: true, //Diminue taille du tableau si peu d'éléments*/

			paging: false, //Pas de pagination, affiche tous les éléments yolo
			order: [[result_contacts_table['default_sort'],"desc"]],

			colReorder: { //On peut réorganiser les colonnes
				fixedColumnsLeft: result_contacts_table['id_column_index']+1 //Les 5 colonnes à gauche sont fixes
			},
			fixedColumns : {
				leftColumns: result_contacts_table['id_column_index']+1
			},


			columnDefs: [
				{
					targets: result_contacts_table['array_hidden'], //Cache colonnes par défaut
					visible: false
				},{
					targets: [result_contacts_table['id_column_index']], //Cache colonnes par défaut
					visible: false
				},{
					className: 'select-checkbox',
					targets : 0,
					orderable: false,
				},{
					width: "30px",
					className: "dt-body-center nopadding",
					targets: [2,3,4]
				}
			],

			//Permet la sélection de lignes
			select: {
				style: 'multi', //Sélection multiple
				selector: 'td:first-child'
			},

			dom: 'Bfrtip',
			buttons: [
				{
					text: '<i class="fa fa-square-o" aria-hidden="true"></i> Sélectionner les éléments affichés',
					action: function () {
						self.table.rows( { search: 'applied' } ).select();
					}
				},{
					//Bouton envoi de mail
					extend: 'selected',
					text: '<i class="fa fa-envelope" aria-hidden="true"></i> Envoyer un mail',
					action: function ( e, dt, button, config ) {
						$("#send-mail-tab").slideDown();
						var target = $(this).data("target");
						self.scrollTo($("#send-mail-tab"));
					}
					//TODO : Scroller jusqu'au panneau
				},


				{
					extend: 'collection',
					text: '<i class="fa fa-eye" aria-hidden="true"></i> Informations à afficher',
					buttons: [{
						//Bouton d'affichage de colonnes
						extend: 'colvis',
						text: '<i class="fa fa-columns" aria-hidden="true"></i> Colonnes à afficher',
						columns: ':gt('+result_contacts_table['id_column_index']+')', //On ne peut pas cacher les 5 premières colonnes
						collectionLayout: 'two-column'
					},{
						extend: 'colvisGroup',
						text: 'Tout afficher',
						show: ':gt('+result_contacts_table['id_column_index']+'):hidden'
					},{
						extend: 'colvisGroup',
						text: 'Tout masquer',
						hide: ':gt('+result_contacts_table['id_column_index']+')'
					},{
						extend: 'colvisRestore',
						text: '<i class="fa fa-refresh" aria-hidden="true"></i> Rétablir colonnes par défaut'
					}]
				},

				//Menu d'export
				{
					extend: 'collection',
					text: '<i class="fa fa-download" aria-hidden="true"></i> Exporter',
					buttons: [ {
						//Bouton d'export excel
						extend: 'excel',
						text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Fichier Excel',
						exportOptions: {
							modifier: {
								columns: ':visible'
							}
						}
					},{
						//Bouton d'export impression
						extend: 'print',
						text: '<i class="fa fa-print" aria-hidden="true"></i> Imprimer',
						exportOptions: {
							modifier: {
								columns: ':visible'
							}
						}
					} ]
				}
			],

			language : {
				"sProcessing":     "Traitement en cours...",
				"sSearch":         "Rechercher&nbsp;:",
				"sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
				"sInfo":           "Affichage de _TOTAL_ &eacute;l&eacute;ments",
				"sInfoEmpty":      "Aucun &eacute;l&eacute;ment &agrave; afficher",
				"sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
				"sInfoPostFix":    "",
				"sLoadingRecords": "Chargement en cours...",
				"sZeroRecords":    "Aucun &eacute;l&eacute;ment",
				"sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
				"oPaginate": {
					"sFirst":      "Premier",
					"sPrevious":   "Pr&eacute;c&eacute;dent",
					"sNext":       "Suivant",
					"sLast":       "Dernier"
				},
				"oAria": {
					"sSortAscending":  ": activer pour trier la colonne par ordre croissant",
					"sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
				},
				select: {
					rows: {
						_: "<br /><b>%d</b> contacts sélectionnés",
						0: '<br />Cliquez sur un contact pour le sélectionner',
						1: "<br /><b>1</b> contact sélectionné"
					}
				}
			}
		});
		self.table.columns.adjust();

		var mailButtonDefault = self.table.button(1).text()
		self.table.on("select.dt deselect.dt", function ( e, dt, type, indexes ) {
			//Maj Bouton de Mail
			var selectedCount = self.table.rows({ selected: true }).count();
			if(selectedCount==0){
				self.table.button(1).text(mailButtonDefault);
				$("#send-mail-tab").slideUp();
			} else {
				self.table.button(1).text(mailButtonDefault+" ("+selectedCount+")");
			}


			//Maj Bouton de sélection
			var allContained = true;
			self.table.rows( { search:'applied' } ).every( function ( rowIdx, tableLoop, rowLoop ) {
				if($.inArray(rowIdx, self.table.rows({ selected: true }).indexes())==-1){
					allContained= false;
				}
			} );

			if(allContained){
				self.table.button(0).text('<i class="fa fa-check-square-o" aria-hidden="true"></i> Déselectionner les éléments affichés');
				self.table.button(0).action(function () {
					self.table.rows( { search: 'applied' } ).deselect();
				});
			} else {
				self.table.button(0).text('<i class="fa fa-square-o" aria-hidden="true"></i> Sélectionner les éléments affichés');
				self.table.button(0).action(function () {
					self.table.rows( { search: 'applied' } ).select();
				});
			}

			//Maj Champs de Mail
			$("#nb-mailed-contacts").text(selectedCount);

			//Maj liste des identifiants à mailer
			var recipients_array = [];
			$.each(self.table.rows({ selected: true }).data(), function(index, element){
				recipients_array.push(element[result_contacts_table['id_column_index']]);
			});
			$("#mail_recipients").val(recipients_array);
		} );

		// Champs de filtrage
		$( self.table.table().container() ).on( 'keyup', 'tfoot .text input', function () {
			self.table
				.column( $(this).data('index') )
				.search( this.value )
				.draw();
		} );
		$( self.table.table().container() ).on( 'change', 'tfoot .check input', function () {
			if($(this).is(":checked")){
				self.table
					.column( $(this).data('index') )
					.search("1")
					.draw();
			}
			else {
				self.table
					.column( $(this).data('index') )
					.search("")
					.draw();
			}
		} );


	}).fail(function(){
		$('#ajax-contacts-load').after("<em>Le chargement du tableau a échoué</em>");
		$('#ajax-loader-img').hide();//On cache la roue de chargement.
	});
	
};

WDGCampaignDashboard.prototype.drawTimetable = function() {
	// Ajoute mise en page et interactions du tableau
	// Ajoute un champ de filtre à chaque colonne dans le footer
	$('#wdg-timetable tfoot td').each( function () {
		$(this).prepend( '<input type="text" placeholder="Filtrer par :" class="col-filter"/><br/>' );
	} );

	// Ajoute les actions de filtrage
	$("#wdg-timetable tfoot input").on( 'keyup change', function () {
		walletTimetable
			.column( $(this).parent().index()+':visible' )
			.search( this.value )
			.draw();
	} );

	//Récupère le tri par défaut 
	sortColumn = 0;

	this.walletTimetableDatatable = $('#wdg-timetable').DataTable({
		scrollX: true,

		order: [[ sortColumn, "asc" ]], //Colonne à trier (date)

		dom: 'RC<"clear">lfrtip',
		lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Tous"]], //nombre d'élements possibles
		iDisplayLength: 50,//nombre d'éléments par défaut

		//Boutons de sélection de colonnes
		colVis: {
			buttonText: "Afficher/cacher colonnes",
			restore: "Restaurer",
			showAll: "Tout afficher",
			showNone: "Tout cacher",
			overlayFade: 100
		},
		language: {
			"sProcessing":     "Traitement en cours...",
			"sSearch":         "Rechercher&nbsp;:",
			"sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
			"sInfo":           "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
			"sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
			"sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
			"sInfoPostFix":    "",
			"sLoadingRecords": "Chargement en cours...",
			"sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
			"sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
			"oPaginate": {
				"sFirst":      "Premier",
				"sPrevious":   "Pr&eacute;c&eacute;dent",
				"sNext":       "Suivant",
				"sLast":       "Dernier"
			},
			"oAria": {
				"sSortAscending":  ": activer pour trier la colonne par ordre croissant",
				"sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
			}
		}
	});
};

WDGCampaignDashboard.prototype.scrollTo = function( target ) {
	$( 'html, body, .wdg-lightbox-padder' ).animate(
		{ scrollTop: target.offset().top - 75 },
		'slow'
	);
};

WDGCampaignDashboard.prototype.fieldError = function( $param, errorText ) {
	$param.addClass("error");
	$param.removeClass("validation");
	$param.qtip({
		content: errorText,
		position: {
			my: 'bottom center',
			at: 'top center',
		},
		style: {
			classes: 'wdgQtip qtip-red qtip-rounded qtip-shadow'
		},
		show: 'focus',
		hide: 'blur'
	});
	$param.closest(".field-value").parent().find('i.fa.validation').remove();
};

WDGCampaignDashboard.prototype.removeFieldError = function( $param ){
	if ( $param.hasClass( "error" ) ) {
		$param.removeClass( "error" );
		$param.qtip().destroy();
	}
};

var wdgCampaignDashboard;
$( function(){
    wdgCampaignDashboard = new WDGCampaignDashboard();
} );
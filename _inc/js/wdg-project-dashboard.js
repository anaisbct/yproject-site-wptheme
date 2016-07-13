jQuery(document).ready( function($) {
    WDGProjectDashboard.init();
});


var WDGProjectDashboard = (function($) {
	return {
		currentOpenedROI: 0,
	    
		init: function() {
			//Gestion de l'AJAX pour la lightbox de ROI
			if ($(".transfert-roi-open").length > 0) {
				$(".transfert-roi-open").click(function() {
					if ($(this).data('roideclaration-id') !== WDGProjectDashboard.currentOpenedROI) {
						//Affichage
						WDGProjectDashboard.currentOpenedROI = $(this).data('roideclaration-id');
						$("#wdg-lightbox-transfer-roi #lightbox-content .loading-content").html("");
						$("#wdg-lightbox-transfer-roi #lightbox-content .loading-image").show();
						$("#wdg-lightbox-transfer-roi #lightbox-content .loading-form").hide();
							
						//Lancement de la requête pour récupérer les utilisateurs et les sommes associées
						$.ajax({
							'type' : "POST",
							'url' : ajax_object.ajax_url,
							'data': { 
							      'action': 'display_roi_user_list',
							      'roideclaration_id' : $(this).data('roideclaration-id')
							}
						}).done(function(result){
							var content = '<table>';
							content += '<tr><td>Utilisateur</td><td>Investissement</td><td>Versement</td><td>Commission</td></tr>';
							content += result;
							content += '</table>';
							$("#wdg-lightbox-transfer-roi #lightbox-content .loading-content").html(content);
							$("#wdg-lightbox-transfer-roi #lightbox-content .loading-image").hide();
							$("#wdg-lightbox-transfer-roi #lightbox-content .loading-form input#hidden-roi-id").val(WDGProjectDashboard.currentOpenedROI);
							$("#wdg-lightbox-transfer-roi #lightbox-content .loading-form").show();
						});
					}
				});
			}

			if ($("#ndashboard-navbar li").length > 0) {
                $("#ndashboard-navbar li:not(.disabled)").click(function(){
                    $("#ndashboard-navbar li").removeClass("active");
                    $(this).addClass("active");
                })
            }
		}
	};
    
})(jQuery);
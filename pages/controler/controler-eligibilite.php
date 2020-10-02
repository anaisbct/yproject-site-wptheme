<?php
$template_engine = WDG_Templates_Engine::instance();
$template_engine->set_controler( new WDG_Page_Controler_ProspectSetup() );

class WDG_Page_Controler_ProspectSetup extends WDG_Page_Controler {

	private $guid;
	
	public function __construct() {
		parent::__construct();
		
		define( 'SKIP_BASIC_HTML', TRUE );

		// Analyse d'un retour éventuel de LW
		$input_is_success = filter_input( INPUT_GET, 'is_success' );
		$input_is_error = filter_input( INPUT_GET, 'is_error' );
		$input_is_canceled = filter_input( INPUT_GET, 'is_canceled' );
		if ( !empty( $input_is_success ) || !empty( $input_is_error ) || !empty( $input_is_canceled ) ) {
			$input_guid = filter_input( INPUT_GET, 'guid' );
			$api_result = WDGWPREST_Entity_Project_Draft::get( $input_guid );

			// Succès de paiement
			if ( $input_is_success === '1' ) {
				$new_status = 'paid';
				$new_step = 'project-complete';
				$new_authorization = 'can-create-db';
				if ( $api_result->authorization != 'can-create-db' ) {
					$metadata_decoded = json_decode( $api_result->metadata );

					// Notif réception de paiement par carte
					$draft_url = home_url( '/financement/eligibilite/?guid=' . $guid );
					NotificationsAPI::prospect_setup_payment_method_received_card( $api_result->email, $metadata_decoded->user->name, $draft_url );
					
					// Mise à jour date de paiement
					date_default_timezone_set("Europe/Paris");
					$datetime = new DateTime();
					$metadata_decoded->package->paymentDate = $datetime->format( 'Y-m-d H:i:s' );
					$api_result->metadata = json_encode( $metadata_decoded );
				}
				WDGWPREST_Entity_Project_Draft::update( $input_guid, $api_result->id_user, $api_result->email, $new_status, $new_step, $new_authorization, $api_result->metadata );
			
			// Erreur de paiement
			} elseif ( $input_is_error === '1' || $input_is_canceled === '1' ) {
				NotificationsAPI::prospect_setup_payment_method_error_card( $api_result->email, $metadata_decoded->user->name, $draft_url );
					
			}
		}
		
		// on récupère le composant Vue
		$WDG_Vue_Components = WDG_Vue_Components::instance();
		$WDG_Vue_Components->enqueue_component( WDG_Vue_Components::$component_prospect_setup );

		$this->guid = filter_input( INPUT_GET, 'guid' );
	}

	public function has_init_guid() {
		return ( !empty( $this->guid ) );
	}

	public function get_init_guid() {
		return $this->guid;
	}
	
}
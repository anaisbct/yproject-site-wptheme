<?php
$template_engine = WDG_Templates_Engine::instance();
$template_engine->set_controler( new WDG_Page_Controler_User_Account() );

class WDG_Page_Controler_User_Account extends WDG_Page_Controler {
	
	private $current_user;
	private $user_id;
	private $user_name;
	private $user_project_list;
	private $user_data;
	private $wallet_to_bankaccount_result;
	private $form_user_details;
	
	public function __construct() {
		parent::__construct();
		define( 'SKIP_BASIC_HTML', TRUE );
		if (!is_user_logged_in()) {
			wp_redirect( home_url( '/connexion' ) . '?redirect-page=mon-compte' );
		}
		WDGFormUsers::register_rib();
		$this->wallet_to_bankaccount_result = WDGFormUsers::wallet_to_bankaccount();
		$this->init_current_user();
		$this->init_project_list();
		$this->init_form();
		locate_template( array( 'country_list.php'  ), true );
	}
	
/******************************************************************************/
// CURRENT USER
/******************************************************************************/
	/**
	 * Retourne les informations de l'utilisateur en cours
	 * @return WDGUser
	 */
	public function get_current_user() {
		return $this->current_user;
	}
	private function init_current_user() {
		$WDGUser_current = WDGUser::current();
		$this->current_user = $WDGUser_current;
		if ( $WDGUser_current->is_admin() ) {
			$input_user_id = filter_input( INPUT_GET, 'override_current_user' );
			if ( !empty( $input_user_id ) ) {
				$this->current_user = new WDGUser( $input_user_id );
			}
		}
		
		$this->user_id = $this->current_user->get_wpref();
		$this->init_user_name();
	}
	
/******************************************************************************/
// USER ID
/******************************************************************************/
	public function get_user_id() {
		return $this->user_id;
	}
	
/******************************************************************************/
// USER NAME
/******************************************************************************/
	public function get_user_name() {
		return $this->user_name;
	}
	private function init_user_name() {
		$first_name = $this->current_user->get_firstname();
		if ( !empty( $first_name ) ) {
			$this->user_name = $first_name;
		
		} else {
			
			$display_name = $this->current_user->wp_user->display_name;
			if ( !empty( $display_name ) ) {
				$this->user_name = $display_name;
				
			} else {
				$this->user_name = $this->current_user->wp_user->user_login;
				
			}
			
		}
	}
	
/******************************************************************************/
// USER DATA
/******************************************************************************/
	private function init_form() {
		$this->form_user_details = new WDG_Form_User_Details( $this->current_user->get_wpref(), WDG_Form_User_Details::$type_extended );
		$action_posted = filter_input( INPUT_POST, 'action' );
		if ( $action_posted == WDG_Form_User_Details::$name ) {
			$this->form_feedback = $this->form_user_details->postForm();
		}
	}
	
	public function get_user_details_form() {
		return $this->form_user_details;
	}
	
	public function get_user_details_form_feedback() {
		return $this->form_feedback;
	}
	
	public function get_user_data( $data_key ) {
		$buffer = '';
		if ( !empty( $data_key ) ) {
			if ( empty( $this->user_data[ $data_key ] ) ) {
				if ( isset( $this->current_user->wp_user->{ 'user_' . $data_key } ) ) {
					$this->user_data[ $data_key ] = $this->current_user->wp_user->{ 'user_' . $data_key };
				} else if ( isset( $this->current_user->wp_user->{ $data_key } ) ) {
					$this->user_data[ $data_key ] = $this->current_user->wp_user->{ $data_key };
				} else {
					$this->user_data[ $data_key ] = $this->current_user->wp_user->get( 'user_' . $data_key );
					if ( empty( $this->user_data[ $data_key ] ) ) {
						$this->user_data[ $data_key ] = $this->current_user->wp_user->get( $data_key );
					}
				}
				
			}
			$buffer = $this->user_data[ $data_key ];
		}
		return $buffer;
	}
	
/******************************************************************************/
// PROJECT LIST
/******************************************************************************/
	public function has_user_project_list() {
		return ( !empty( $this->user_project_list ) );
	}
	public function get_user_project_list() {
		return $this->user_project_list;
	}
	private function init_project_list() {
		$this->user_project_list = array();
		
		$args = array(
			'post_type'		=> 'download',
			'author'		=> $this->current_user->wp_user->ID,
			'post_status'	=> 'publish'
		);
		query_posts($args);
		
		if (have_posts()) {
			while (have_posts()) {
				the_post();
				$project = array(
					'link'	=> home_url( '/tableau-de-bord' ) . '?campaign_id=' . get_the_ID(),
					'name'	=> get_the_title()
				);
				array_push( $this->user_project_list, $project );
			}
		}
		
		$api_user_id = $this->current_user->get_api_id();
		$project_list = WDGWPREST_Entity_User::get_projects_by_role( $api_user_id, WDGWPREST_Entity_Project::$link_user_type_team );
		if ( !empty( $project_list ) ) {
			foreach ($project_list as $project) {
				$project = array(
					'link'	=> home_url( '/tableau-de-bord' ) . '?campaign_id=' . $project->wpref,
					'name'	=> $project->name
				);
				array_push( $this->user_project_list, $project );
			}
		}
	}
	
/******************************************************************************/
// TRANSFER TO BANK ACCOUNT
/******************************************************************************/
	public function get_wallet_to_bankaccount_result() {
		return $this->wallet_to_bankaccount_result;
	}
	
}
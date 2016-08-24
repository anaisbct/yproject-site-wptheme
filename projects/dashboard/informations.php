<?php

function print_informations_page()
{
    locate_template('country_list.php', true);
    global $country_list;
    global $campaign_id, $campaign, $post_campaign,
           $WDGAuthor, $WDGUser_current,
           $is_admin, $is_author;

    ?>

    <div class="head"><?php _e("Informations","yproject");?></div>
    <div class="bloc-grid">
        <div class="display-bloc" data-tab-target="tab-project">
            <i class="fa fa-lightbulb-o fa-4x aria-hidden="true"></i>
            <div class="infobloc-title">
                <?php _e("Le projet","yproject");?>
            </div>
        </div>
        <div class="display-bloc" data-tab-target="tab-user-infos">
            <i class="fa fa-user fa-4x aria-hidden="true"></i>
            <div class="infobloc-title">
                <?php _e("Infos personnelles","yproject");?>
            </div>
        </div>
        <div class="display-bloc" data-tab-target="tab-organization">
            <i class="fa fa-building fa-4x aria-hidden="true"></i>
            <div class="infobloc-title">
                <?php _e("L'organisation","yproject");?>
            </div>
        </div>
        <div class="display-bloc" data-tab-target="tab-funding">
            <i class="fa fa-money fa-4x aria-hidden="true"></i>
            <div class="infobloc-title">
                <?php _e("Besoin de financement","yproject");?>
            </div>
        </div>
        <div class="display-bloc" data-tab-target="tab-communication">
            <i class="fa fa-bullhorn fa-4x aria-hidden="true"></i>
            <div class="infobloc-title">
                <?php _e("Votre communication","yproject");?>
            </div>
        </div>
        <div class="display-bloc" data-tab-target="tab-contract">
            <span class="fa-stack fa-2x">
                <i class="fa fa-file-o fa-stack-2x"></i>
                <i class="fa fa-check-circle-o fa-stack-1x"></i>
            </span>
            <div class="infobloc-title">
                <?php _e("Contractualisation","yproject");?>
            </div>
        </div>
    </div>

    <div id="tab-container">
        <div class="tab-content" id="tab-project">
            <?php
            //Gestion des catégories
            $campaign_categories = get_the_terms($campaign_id, 'download_category');
            $selected_category = 0;
            $selected_activity = 0;
            $terms_category = get_terms('download_category', array('slug' => 'categories', 'hide_empty' => false));
            $term_category_id = $terms_category[0]->term_id;
            $terms_activity = get_terms('download_category', array('slug' => 'activities', 'hide_empty' => false));
            $term_activity_id = $terms_activity[0]->term_id;
            if ($campaign_categories) {
                foreach ($campaign_categories as $campaign_category) {
                    if ($campaign_category->parent == $term_category_id) {
                        $selected_category = $campaign_category->term_id;
                    }
                    if ($campaign_category->parent == $term_activity_id) {
                        $selected_activity = $campaign_category->term_id;
                    }
                }
            }
            ?>
            <form id="projectinfo_form" class="db-form">
                <ul class="errors">

                </ul>

                <?php
                DashboardUtility::create_field(array(
                    "id"=>"project_name",
                    "type"=>"text",
                    "label"=>"Nom du projet",
                    "value"=>$post_campaign->post_title
                ));

                DashboardUtility::create_field(array(
                    "id"=>"backoffice_summary",
                    "type"=>"editor",
                    "label"=>"R&eacute;sum&eacute; du projet",
                    "infobubble"=>"Ces informations seront traitées de manière confidentielle",
                    "value"=>$campaign->backoffice_summary()
                ));
                ?>
                <div class="field"><label for="categories">Cat&eacute;gorie</label>
                    <?php wp_dropdown_categories(array(
                        'hide_empty' => 0,
                        'taxonomy' => 'download_category',
                        'selected' => $selected_category,
                        'echo' => 1,
                        'child_of' => $term_category_id,
                        'name' => 'categories',
                        'id' => 'update_project_category'
                    )); ?></div>

                <div class="field"><label for="activities">Secteur d&apos;activit&eacute;</label>
                    <?php wp_dropdown_categories(array(
                        'hide_empty' => 0,
                        'taxonomy' => 'download_category',
                        'selected' => $selected_activity,
                        'echo' => 1,
                        'child_of' => $term_activity_id,
                        'name' => 'activities',
                        'id' => 'update_project_activity'
                    )); ?></div>

                <?php
                $locations = atcf_get_locations();

                DashboardUtility::create_field(array(
                    "id"=>"project_location",
                    "type"=>"select",
                    "label"=>"Localisation",
                    "value"=>$campaign->location(),
                    "options_id"=>array_keys($locations),
                    "options_names"=>array_values($locations)
                ));


                DashboardUtility::create_field(array(
                    "id"=>"project_WDG_notoriety",
                    "type"=>"textarea",
                    "label"=>'"Comment avez-vous connu WDG ?"',
                    "value"=>$campaign->backoffice_WDG_notoriety(),
                    "visible"=>$is_admin,
                    "admin_theme"=>$is_admin,
                    "editable"=>false
                ));

                DashboardUtility::create_save_button("projectinfo_form"); ?>
            </form>
        </div>

        <div class="tab-content" id="tab-user-infos">
            <form id="userinfo_form" class="db-form">
                <?php if ($is_author) {
                    ?><p><?php _e("Complétez vos informations personnelles de porteur de projet","yproject");?></p>
                    <input type="hidden" id="input_is_project_holder" name="is_project_holder" value="1"/><?php
                } else {
                    ?><p><?php _e("Seul le créateur du projet peut compléter ses informations personnelles","yproject");?></p><?php
                }?>

                <ul class="errors">

                </ul>

                <?php
                DashboardUtility::create_field(array(
                    "id"=>"gender",
                    "type"=>"select",
                    "label"=>"Vous &ecirc;tes",
                    "value"=>$WDGAuthor->wp_user->get('user_gender'),
                    "editable"=>$is_author,
                    "options_id"=>array("female", "male"),
                    "options_names"=>array("une femme", "un homme")
                ));

                DashboardUtility::create_field(array(
                    "id"=>"firstname",
                    "type"=>"text",
                    "label"=>"Pr&eacute;nom",
                    "value"=>$WDGAuthor->wp_user->user_firstname,
                    "editable"=>$is_author
                ));

                DashboardUtility::create_field(array(
                    "id"=>"lastname",
                    "type"=>"text",
                    "label"=>"Nom",
                    "value"=>$WDGAuthor->wp_user->user_lastname,
                    "editable"=>$is_author
                ));

                $bd = new DateTime();
				$user_birthday_year = $WDGAuthor->wp_user->get('user_birthday_year');
                if(!empty($user_birthday_year)){
                    $bd->setDate(intval($WDGAuthor->wp_user->get('user_birthday_year')),
                        intval($WDGAuthor->wp_user->get('user_birthday_month')),
                        intval($WDGAuthor->wp_user->get('user_birthday_day')));
                }

                DashboardUtility::create_field(array(
                    "id"=>"birthday",
                    "type"=>"date",
                    "label"=>"Date de naissance",
                    "value"=>$bd,
                    "editable"=>$is_author
                ));

                DashboardUtility::create_field(array(
                    "id"=>"birthplace",
                    "type"=>"text",
                    "label"=>"Ville de naissance",
                    "value"=>$WDGAuthor->wp_user->get('user_birthplace'),
                    "editable"=>$is_author
                ));

                DashboardUtility::create_field(array(
                    "id"=>"nationality",
                    "type"=>"select",
                    "label"=>"Nationalit&eacute;",
                    "value"=>$WDGAuthor->wp_user->get('user_nationality'),
                    "editable"=>$is_author,
                    "options_id"=>array_keys($country_list),
                    "options_names"=>array_values($country_list)
                ));

                DashboardUtility::create_field(array(
                    "id"=>"mobile_phone",
                    "type"=>"text",
                    "label"=>"T&eacute;l&eacute;phone mobile",
                    "value"=>$WDGAuthor->wp_user->get('user_mobile_phone'),
                    "infobubble"=>"Ce num&eacute;ro sera celui utilis&eacute; pour vous contacter &agrave; propos de votre projet",
                    "editable"=>$is_author,
                    "left_icon"=>"mobile-phone"
                ));

                DashboardUtility::create_field(array(
                    "id"=>"email",
                    "type"=>"text",
                    "label"=>"Adresse &eacute;lectronique",
                    "value"=>$WDGAuthor->wp_user->get('user_email'),
                    "infobubble"=>"Pour modifier votre adresse e-mail de contact, rendez-vous dans vos param&egrave;tres de compte",
                    "editable"=>false
                ));

                DashboardUtility::create_field(array(
                    "id"=>"address",
                    "type"=>"text",
                    "label"=>"Adresse",
                    "value"=>$WDGAuthor->wp_user->get('user_address'),
                    "editable"=>$is_author
                ));

                DashboardUtility::create_field(array(
                    "id"=>"postal_code",
                    "type"=>"text",
                    "label"=>"Code postal",
                    "value"=>$WDGAuthor->wp_user->get('user_postal_code'),
                    "editable"=>$is_author
                ));

                DashboardUtility::create_field(array(
                    "id"=>"city",
                    "type"=>"text",
                    "label"=>"Ville",
                    "value"=>$WDGAuthor->wp_user->get('user_city'),
                    "editable"=>$is_author
                ));

                DashboardUtility::create_field(array(
                    "id"=>"country",
                    "type"=>"text",
                    "label"=>"Pays",
                    "value"=>$WDGAuthor->wp_user->get('user_country'),
                    "editable"=>$is_author
                ));?>
                <br/>

                <?php
                DashboardUtility::create_save_button("userinfo_form",$is_author); ?>
            </form>
        </div>

        <div class="tab-content" id="tab-organization">
            <form id="orgainfo_form" class="db-form">
                <ul class="errors">

                </ul>

                <?php
                // Gestion des organisations
                $str_organisations = '';
                global $current_user;
                $api_project_id = BoppLibHelpers::get_api_project_id($post_campaign->ID);
                $current_organisations = BoppLib::get_project_organisations_by_role($api_project_id, BoppLibHelpers::$project_organisation_manager_role['slug']);
                if (isset($current_organisations) && count($current_organisations) > 0) {
                    $current_organisation = $current_organisations[0];
                }
                $api_user_id = BoppLibHelpers::get_api_user_id($post_campaign->post_author);
                $organisations_list = BoppUsers::get_organisations_by_role($api_user_id, BoppLibHelpers::$organisation_creator_role['slug']);
                if ($organisations_list) {
                    foreach ($organisations_list as $organisation_item) {
                        $selected_str = ($organisation_item->id == $current_organisation->id) ? 'selected="selected"' : '';
                        $str_organisations .= '<option ' . $selected_str . ' value="'.$organisation_item->organisation_wpref.'">' .$organisation_item->organisation_name. '</option>';
                    }
                }
                ?>
                <label for="project-organisation">Organisation :</label>
                <?php if ($str_organisations != ''): ?>
                    <select name="project-organisation" id="update_project_organisation">
                        <option value=""></option>
                        <?php echo $str_organisations; ?>
                    </select>
                    <?php if ($current_organisation!=null){
                        $page_edit_orga = get_page_by_path('editer-une-organisation');
                        $edit_org = '<a id="edit-orga-button" class="button" 
                            data-url-edit="'.  get_permalink($page_edit_orga->ID) .'?orga_id='.'" 
                            href="'.  get_permalink($page_edit_orga->ID) .'?orga_id='.$current_organisation->organisation_wpref.'">';
                        $edit_org .= 'Editer '.$current_organisation->organisation_name.'</a>';
                        echo $edit_org;
                    } ?>

                <?php else: ?>
                    <?php _e('Le porteur de projet n&apos;est li&eacute; &agrave; aucune organisation.', 'yproject'); ?>
                    <input type="hidden" name="project-organisation" value="" />
                <?php endif;

                $page_new_orga = get_page_by_path('creer-une-organisation'); ?>
                <a href="<?php echo get_permalink($page_new_orga->ID); ?>" class="button">Cr&eacute;er une organisation</a>

                <br />
                <?php DashboardUtility::create_save_button("orgainfo_form"); ?>
            </form>
        </div>

        <div class="tab-content" id="tab-funding">
            <ul class="errors">

            </ul>
            <form action="" id="projectfunding_form"  class="db-form">
                <?php
                DashboardUtility::create_field(array(
                    "id"=>"maximum_goal",
                    "type"=>"number",
                    "label"=>"Montant maximal demand&eacute;",
                    "value"=>$campaign->goal(false),
                    "right_icon"=>"eur",
                    "min"=>500
                ));

                DashboardUtility::create_field(array(
                    "id"=>"minimum_goal",
                    "type"=>"number",
                    "label"=>"Palier minimal",
                    "infobubble"=>"Au-del&agrave; de ce palier, la collecte sera valid&eacute; mais rien n'emp&ecirc;che d'avoir un objectif plus ambitieux !",
                    "value"=>$campaign->minimum_goal(false),
                    "right_icon"=>"eur",
                    "min"=>500
                ));


                DashboardUtility::create_field(array(
                    "id"=>"funding_duration",
                    "type"=>"number",
                    "label"=>"Dur&eacute;e du financement",
                    "value"=>$campaign->funding_duration(),
                    "suffix"=>" ann&eacute;es",
                    "min"=>1,
                    "max"=>10
                ));

                DashboardUtility::create_field(array(
                    "id"=>"roi_percent_estimated",
                    "type"=>"number",
                    "label"=>"Pourcentage de reversement estim&eacute;",
                    "value"=>$campaign->funding_duration(),
                    "suffix"=>"&nbsp;% du CA",
                    "min"=>0,
                    "max"=>100,
                    "step"=>0.01
                ));

                DashboardUtility::create_field(array(
                    "id"=>"first_payment",
                    "type"=>"date",
                    "label"=>"Première date de versement",
                    "value"=>new DateTime($campaign->first_payment_date()),
                    "editable"=> $is_admin,
                    "admin_theme"=>$is_admin,
                    "visible"=>$is_admin || ($campaign->first_payment_date()!="")
                ));

                ?>

                <div class="field"><label>CA pr&eacute;visionnel</label></div>
                <ul id="estimated-turnover">
                    <?php
					$estimated_turnover = $campaign->estimated_turnover();
                    if(!empty($estimated_turnover)){
                        foreach (($campaign->estimated_turnover()) as $year => $turnover) : ?>
                            <li><label>Année <span class="year"><?php echo $year?></span></label><input type="text" value="<?php echo $turnover?>"/>
                            </li>
                        <?php endforeach;
                    }
                     ?>
                </ul>

                <?php DashboardUtility::create_save_button("projectfunding_form"); ?>
            </form>
        </div>

        <div class="tab-content" id="tab-communication">
            <ul class="errors">

            </ul>
            <form action="" id="communication_form" class="db-form">
                <?php
                DashboardUtility::create_field(array(
                    "id"=>"website",
                    "type"=>"text",
                    "label"=>'Site web',
                    "value"=> $campaign->campaign_external_website(),
                    "right_icon"=>"link",
                ));

                DashboardUtility::create_field(array(
                    "id"=>"facebook",
                    "type"=>"text",
                    "label"=>'Page Facebook',
                    "value"=> $campaign->facebook_name(),
                    "prefix"=>"www.facebook.com/",
                    "placeholder"=>"PageFacebook",
                    "right_icon"=>"facebook",
                ));

                DashboardUtility::create_field(array(
                    "id"=>"twitter",
                    "type"=>"text",
                    "label"=>'Twitter',
                    "value"=> $campaign->twitter_name(),
                    "prefix"=>"@",
                    "placeholder"=>"CompteTwitter",
                    "right_icon"=>"twitter",
                ));

                DashboardUtility::create_save_button("communication_form");?>
            </form>
        </div>

        <div class="tab-content" id="tab-contract">
            <ul class="errors">

            </ul>
            <form action="" id="contract_form" class="db-form">
                <?php

                DashboardUtility::create_field(array(
                    "id"=>"contract_url",
                    "type"=>"link",
                    "label"=>"Lien du contrat",
                    "value"=> $campaign->contract_doc_url(),
                    "editable"=> $is_admin,
                    "admin_theme"=>$is_admin,
                    "placeholder"=>"http://.....",
                    "default_display"=>"Le contrat n'est pas encore écrit"
                ));

                DashboardUtility::create_save_button("contract_form", $is_admin);?>
            </form>
        </div>
    </div>
    <?php
}

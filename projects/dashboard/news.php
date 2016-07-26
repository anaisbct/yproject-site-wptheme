<?php function print_news_page() {
    global $campaign_id, $campaign, $post_campaign,
           $WDGAuthor, $WDGUser_current;
    $category_link = get_category_link($campaign->get_news_category_id());
    $news_link = esc_url($category_link);
    $posts_blog = get_posts(array('category'=>$campaign->get_news_category_id()));
    $nbposts_blog = count($posts_blog);
    $page_edit_news = get_page_by_path('editer-une-actu');
    ?>
    <div id ="block-news" class="block">
        <div class="head">Actualit&eacute;s</div>
        <div class="tab-content" style="text-align:center">
            <?php if ($nbposts_blog == 0): ?>
                <p>Vous n'avez pas encore publi&eacute; d'article.</p>
            <?php else: ?>
                <ul>
                    <?php
                    foreach ($posts_blog as $post_blog) {
                        $link = get_permalink($post_blog->ID);
                        $link_edit = get_permalink($page_edit_news->ID)."?campaign_id=".$campaign_id."&edit_post_id=".$post_blog->ID;
                        $title = $post_blog->post_title;
                        $date = (new DateTime($post_blog->post_date))->format('d/m/Y');

                        $line = '<li>';
                        $line .= '<div class="news-item-tools">';
                        $line .= '<a class="nb-comments-widget" href="'.$link.'#responds">'.$post_blog->comment_count;
                        $line .= '&nbsp;<i class="fa fa-comments" aria-hidden="true"></i></a> ';
                        $line .= '<a class="button" href="'.$link_edit.'">&Eacute;diter</a></div>';
                        $line .= '<a href="'.$link.'">'.$title.'</a><br/>';
                        $line .= '<em>Publi&eacute; le '.$date.'</em>';
                        $line .= '<div class="clear"/></li>';

                        echo $line;
                    }
                    ?>
                </ul>
            <?php endif; ?>

            <div class="list-button">
                <a href="<?php echo $news_link; ?>" class="button"><?php _e('Voir plus', 'yproject'); ?></a>
                <a href="<?php echo $news_link.'?new-topic=1'; ?>" class="button"><?php _e('Publier une nouvelle actualit&eacute;', 'yproject'); ?></a>
            </div>
        </div>
    </div>
<?php } ?>
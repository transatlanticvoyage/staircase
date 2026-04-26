<?php
/**
 * Galleryberry page template — dark project gallery.
 *
 * Renders a grid of wp_work_projects rows with their attached images
 * (via wp_work_projects_images_relations → wp_posts attachments).
 *
 * All classes/ids/data attributes prefixed with `galleryberry_` for isolation.
 */

if (!defined('ABSPATH')) { exit; }

global $wpdb;

$gby_projects_table = $wpdb->prefix . 'work_projects';
$gby_relations_table = $wpdb->prefix . 'work_projects_images_relations';

// Fetch projects (only render if table exists)
$gby_table_exists = $wpdb->get_var($wpdb->prepare(
    "SHOW TABLES LIKE %s",
    $gby_projects_table
));

$gby_projects = array();
if ($gby_table_exists) {
    $gby_projects = $wpdb->get_results(
        "SELECT id, project_name, client_name, client_location, project_date, status, description, technologies, project_url, featured, display_order
         FROM {$gby_projects_table}
         ORDER BY display_order ASC, id DESC",
        ARRAY_A
    );
}

// For each project, fetch attached images and build a photos array
$gby_cards = array();
foreach ($gby_projects as $gby_project) {
    $gby_project_id = intval($gby_project['id']);
    $gby_relations = $wpdb->get_results($wpdb->prepare(
        "SELECT r.image_id, r.client_name, r.client_location, p.post_title
         FROM {$gby_relations_table} r
         LEFT JOIN {$wpdb->posts} p ON p.ID = r.image_id
         WHERE r.project_id = %d
         ORDER BY r.relation_id ASC",
        $gby_project_id
    ), ARRAY_A);

    $gby_photos = array();
    foreach ($gby_relations as $gby_rel) {
        $gby_image_id = intval($gby_rel['image_id']);
        if ($gby_image_id <= 0) continue;
        $gby_full = wp_get_attachment_image_url($gby_image_id, 'full');
        $gby_large = wp_get_attachment_image_url($gby_image_id, 'large');
        $gby_thumb = wp_get_attachment_image_url($gby_image_id, 'medium');
        if (!$gby_full && !$gby_large && !$gby_thumb) continue;
        $gby_photos[] = array(
            'id'              => $gby_image_id,
            'thumb'           => $gby_thumb ?: ($gby_large ?: $gby_full),
            'full'            => $gby_full ?: ($gby_large ?: $gby_thumb),
            'alt'             => $gby_rel['post_title'] ?: '',
            'client_name'     => $gby_rel['client_name'] ?: '',
            'client_location' => $gby_rel['client_location'] ?: '',
        );
    }

    // Skip projects with zero photos entirely
    if (empty($gby_photos)) continue;

    // Cover = first photo (by relation_id ASC)
    $gby_cover = $gby_photos[0];

    // Year: extract from project_date if present
    $gby_year = '';
    if (!empty($gby_project['project_date'])) {
        $gby_ts = strtotime($gby_project['project_date']);
        if ($gby_ts) $gby_year = date('Y', $gby_ts);
    }

    $gby_cards[] = array(
        'id'              => $gby_project_id,
        'title'           => $gby_project['project_name'] ?: ('Project #' . $gby_project_id),
        'description'     => $gby_project['description'] ?: '',
        'year'            => $gby_year,
        'photo_count'     => count($gby_photos),
        'cover'           => $gby_cover,
        'photos'          => $gby_photos,
    );
}

?>

<div class="galleryberry_main" id="galleryberry_main">
    <div class="galleryberry_inner">

        <?php $gby_post_title = get_the_title(); ?>
        <?php if (!empty($gby_post_title)) : ?>
            <h1 class="galleryberry_page_title"><?php echo esc_html($gby_post_title); ?></h1>
        <?php endif; ?>

        <?php if (empty($gby_cards)) : ?>
            <div class="galleryberry_empty">
                <h2 class="galleryberry_empty_title">No projects yet</h2>
                <p class="galleryberry_empty_sub">Once projects and images are added via the admin area, they will appear here.</p>
            </div>
        <?php else : ?>
            <div class="galleryberry_grid" role="list">
                <?php foreach ($gby_cards as $gby_card) :
                    $gby_count = intval($gby_card['photo_count']);
                    $gby_count_text = $gby_count . ' photo' . ($gby_count === 1 ? '' : 's');
                    $gby_card_aria = 'Open photo gallery for ' . $gby_card['title'] . ' (' . $gby_count_text . ')';
                    $gby_cover_alt = $gby_card['cover']['alt'] ?: ($gby_card['title'] . ' — cover photo');
                ?>
                    <article
                        class="galleryberry_card"
                        role="listitem"
                        tabindex="0"
                        data-galleryberry-project-id="<?php echo esc_attr($gby_card['id']); ?>"
                        data-galleryberry-project-name="<?php echo esc_attr($gby_card['title']); ?>"
                        data-galleryberry-project-description="<?php echo esc_attr($gby_card['description']); ?>"
                        data-galleryberry-photos="<?php echo esc_attr(wp_json_encode($gby_card['photos'])); ?>"
                        aria-label="<?php echo esc_attr($gby_card_aria); ?>"
                    >
                        <div class="galleryberry_card_cover">
                            <img
                                class="galleryberry_card_cover_img"
                                src="<?php echo esc_url($gby_card['cover']['thumb']); ?>"
                                alt="<?php echo esc_attr($gby_cover_alt); ?>"
                                loading="lazy"
                            />
                            <span class="galleryberry_card_badge" aria-hidden="true"><?php echo $gby_count; ?></span>
                        </div>
                        <div class="galleryberry_card_body">
                            <h2 class="galleryberry_card_title"><?php echo esc_html($gby_card['title']); ?></h2>
                            <div class="galleryberry_card_meta">
                                <?php if (!empty($gby_card['year'])) : ?>
                                    <span class="galleryberry_card_year"><?php echo esc_html($gby_card['year']); ?></span>
                                <?php endif; ?>
                                <span class="galleryberry_card_count">
                                    <?php echo $gby_count; ?> photo<?php echo $gby_count === 1 ? '' : 's'; ?>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Lightbox overlay (hidden until a card is activated)
     - role="dialog" + aria-modal="true": screen readers treat this as a modal
     - aria-labelledby points to the dynamically populated project title heading
       so the modal's accessible name reflects the current project
     - The figure heading and a status live region announce photo changes -->
<div class="galleryberry_lightbox" id="galleryberry_lightbox" role="dialog" aria-modal="true" aria-labelledby="galleryberry_lightbox_info_title" hidden>
    <button type="button" class="galleryberry_lightbox_close" id="galleryberry_lightbox_close" aria-label="Close photo viewer">&times;</button>
    <button type="button" class="galleryberry_lightbox_prev" id="galleryberry_lightbox_prev" aria-label="Previous photo">&#10094;</button>
    <button type="button" class="galleryberry_lightbox_next" id="galleryberry_lightbox_next" aria-label="Next photo">&#10095;</button>

    <figure class="galleryberry_lightbox_figure">
        <img class="galleryberry_lightbox_img" id="galleryberry_lightbox_img" src="" alt="" />
    </figure>

    <aside class="galleryberry_lightbox_info" id="galleryberry_lightbox_info">
        <h2 class="galleryberry_lightbox_info_title" id="galleryberry_lightbox_info_title"></h2>
        <p class="galleryberry_lightbox_info_desc" id="galleryberry_lightbox_info_desc"></p>
        <p class="galleryberry_lightbox_info_client" id="galleryberry_lightbox_info_client" hidden>
            <strong class="galleryberry_lightbox_info_client_name" id="galleryberry_lightbox_info_client_name"></strong>
            <span class="galleryberry_lightbox_info_client_from"> from </span>
            <strong class="galleryberry_lightbox_info_client_location" id="galleryberry_lightbox_info_client_location"></strong>
        </p>
    </aside>

    <!-- Visual counter ("3 / 5"). aria-hidden so screen readers don't read "3 slash 5". -->
    <div class="galleryberry_lightbox_counter" id="galleryberry_lightbox_counter" aria-hidden="true"></div>

    <!-- Screen-reader-only live region announces photo changes ("Photo 3 of 5") -->
    <div class="galleryberry_sr_only" id="galleryberry_lightbox_announce" role="status" aria-live="polite" aria-atomic="true"></div>
</div>

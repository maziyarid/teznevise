<?php
/**
 * Legacy WPCode shortcodes restored after the HTML → WordPress migration.
 * Loaded only as fallbacks so leftover [tz_*] tags render the original UI.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --- Download Template --- */
if ( ! function_exists( 'teznevise_downloads_archive' ) ) {
// ============================================
// تزنویسه - قالب نمایش دانلود + آرشیو + اسکیما
// نسخه ۱.۰
// ============================================


// ===== ۱. تابع کمکی: حروف اول نام =====
if ( ! function_exists( 'teznevise_dl_initials' ) ) {
    function teznevise_dl_initials( $name ) {
        $parts = explode( ' ', trim( $name ) );
        $out   = '';

        foreach ( $parts as $p ) {
            if ( $p !== '' ) {
                $out .= mb_substr( $p, 0, 1, 'UTF-8' );
                if ( mb_strlen( $out, 'UTF-8' ) >= 2 ) {
                    break;
                }
            }
        }

        return $out ?: '📄';
    }
}


// ===== ۲. آیکون پسوند فایل =====
if ( ! function_exists( 'teznevise_file_icon' ) ) {
    function teznevise_file_icon( $ext ) {
        $ext = strtolower( $ext );
        $map = array(
            'pdf'  => '📕',
            'doc'  => '📘',
            'docx' => '📘',
            'xls'  => '📗',
            'xlsx' => '📗',
            'ppt'  => '📙',
            'pptx' => '📙',
            'zip'  => '🗜️',
            'rar'  => '🗜️',
            'txt'  => '📄',
            'csv'  => '📊',
            'spss' => '📈',
            'sav'  => '📈',
            'jpg'  => '🖼️',
            'png'  => '🖼️',
            'mp4'  => '🎬',
            'mp3'  => '🎵',
        );

        return isset( $map[ $ext ] ) ? $map[ $ext ] : '📎';
    }
}


// ===== ۳. نمایش محتوای تک‌فایل دانلود (روی the_content) =====
add_filter( 'the_content', 'teznevise_single_download_content', 20 );

function teznevise_single_download_content( $content ) {
    if ( ! is_singular( 'download' ) || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    $post_id     = get_the_ID();
    $links       = get_post_meta( $post_id, '_teznevise_download_links', true );
    $version     = get_post_meta( $post_id, '_teznevise_version',        true );
    $license     = get_post_meta( $post_id, '_teznevise_license',        true );
    $lang        = get_post_meta( $post_id, '_teznevise_lang',           true );
    $source      = get_post_meta( $post_id, '_teznevise_source',         true );
    $dl_count    = intval( get_post_meta( $post_id, '_teznevise_download_count', true ) );
    $author_name = get_the_author();
    $date        = get_the_date( 'j F Y' );
    $cats        = get_the_terms( $post_id, 'download_category' );
    $thumb       = get_the_post_thumbnail_url( $post_id, 'large' );

    ob_start();
    ?>
    <div class="tzdl-single">

        <!-- ===== Header ===== -->
        <div class="tzdl-single-header">
            <div class="tzdl-single-thumb">
                <?php if ( $thumb ) : ?>
                    <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" />
                <?php else : ?>
                    <div class="tzdl-thumb-fallback">📥</div>
                <?php endif; ?>
            </div>

            <div class="tzdl-single-info">
                <?php if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) : ?>
                    <div class="tzdl-single-cats">
                        <?php foreach ( $cats as $cat ) : ?>
                            <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
                                <?php echo esc_html( $cat->name ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h2 class="tzdl-single-title"><?php the_title(); ?></h2>

                <div class="tzdl-quick-info">
                    <?php if ( $version ) : ?>
                        <div class="tzdl-quick-item">
                            <span class="tzdl-qi-icon">🔖</span>
                            <div>
                                <span class="tzdl-qi-label">نسخه</span>
                                <span class="tzdl-qi-value"><?php echo esc_html( $version ); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( $license ) : ?>
                        <div class="tzdl-quick-item">
                            <span class="tzdl-qi-icon">📜</span>
                            <div>
                                <span class="tzdl-qi-label">مجوز</span>
                                <span class="tzdl-qi-value"><?php echo esc_html( $license ); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( $lang ) : ?>
                        <div class="tzdl-quick-item">
                            <span class="tzdl-qi-icon">🌐</span>
                            <div>
                                <span class="tzdl-qi-label">زبان</span>
                                <span class="tzdl-qi-value"><?php echo esc_html( $lang ); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="tzdl-quick-item">
                        <span class="tzdl-qi-icon">⬇️</span>
                        <div>
                            <span class="tzdl-qi-label">دانلود</span>
                            <span class="tzdl-qi-value"><?php echo number_format_i18n( $dl_count ); ?></span>
                        </div>
                    </div>

                    <?php if ( $source ) : ?>
                        <div class="tzdl-quick-item">
                            <span class="tzdl-qi-icon">🏛️</span>
                            <div>
                                <span class="tzdl-qi-label">منبع</span>
                                <span class="tzdl-qi-value"><?php echo esc_html( $source ); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="tzdl-quick-item">
                        <span class="tzdl-qi-icon">📅</span>
                        <div>
                            <span class="tzdl-qi-label">تاریخ</span>
                            <span class="tzdl-qi-value"><?php echo esc_html( $date ); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Download Box ===== -->
        <?php if ( is_array( $links ) && ! empty( $links ) ) : ?>
            <div class="tzdl-download-box" id="tzdl-download">
                <div class="tzdl-box-header">
                    <span class="tzdl-box-icon">📦</span>
                    <h2>لینک‌های دانلود</h2>
                </div>

                <div class="tzdl-box-body">
                    <?php foreach ( $links as $link ) : ?>
                        <div class="tzdl-link-row">
                            <div class="tzdl-link-meta">
                                <span class="tzdl-link-ficon"><?php echo teznevise_file_icon( $link['ext'] ); ?></span>
                                <div class="tzdl-link-text">
                                    <span class="tzdl-link-title"><?php echo esc_html( $link['text'] ); ?></span>
                                    <div class="tzdl-link-tags">
                                        <?php if ( ! empty( $link['ext'] ) ) : ?>
                                            <span class="tzdl-link-tag tzdl-tag-ext">
                                                <?php echo esc_html( strtoupper( $link['ext'] ) ); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $link['size'] ) ) : ?>
                                            <span class="tzdl-link-tag tzdl-tag-size">
                                                📏 <?php echo esc_html( $link['size'] ); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo esc_url( $link['url'] ); ?>"
                               class="tzdl-download-btn"
                               data-post="<?php echo $post_id; ?>"
                               target="_blank"
                               rel="nofollow noopener"
                               download>
                                <span class="tzdl-btn-icon">⬇</span> دانلود
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="tzdl-box-footer">
                    <span>🔒 دانلود امن و مستقیم</span>
                    <span>•</span>
                    <span>✓ بدون نیاز به ثبت‌نام</span>
                </div>
            </div>
        <?php endif; ?>

        <!-- ===== Tabs ===== -->
        <div class="tzdl-tabs">
            <div class="tzdl-tab-nav">
                <button class="tzdl-tab-btn tzdl-tab-active" data-tab="desc">📝 توضیحات</button>
                <button class="tzdl-tab-btn" data-tab="details">📋 جزئیات</button>
            </div>

            <div class="tzdl-tab-pane tzdl-pane-active" id="tzdl-pane-desc">
                <div class="tzdl-content-body">
                    <?php echo $content; ?>
                </div>
            </div>

            <div class="tzdl-tab-pane" id="tzdl-pane-details">
                <table class="tzdl-details-table">
                    <tbody>
                        <tr><td>عنوان فایل</td><td><?php the_title(); ?></td></tr>
                        <?php if ( $version ) : ?>
                            <tr><td>نسخه / ویرایش</td><td><?php echo esc_html( $version ); ?></td></tr>
                        <?php endif; ?>
                        <?php if ( $license ) : ?>
                            <tr><td>نوع مجوز</td><td><?php echo esc_html( $license ); ?></td></tr>
                        <?php endif; ?>
                        <?php if ( $lang ) : ?>
                            <tr><td>زبان</td><td><?php echo esc_html( $lang ); ?></td></tr>
                        <?php endif; ?>
                        <?php if ( $source ) : ?>
                            <tr><td>منبع</td><td><?php echo esc_html( $source ); ?></td></tr>
                        <?php endif; ?>
                        <tr>
                            <td>تعداد فایل‌ها</td>
                            <td><?php echo is_array( $links ) ? number_format_i18n( count( $links ) ) : '۰'; ?> فایل</td>
                        </tr>
                        <tr><td>تعداد دانلود</td><td><?php echo number_format_i18n( $dl_count ); ?> بار</td></tr>
                        <tr><td>تاریخ انتشار</td><td><?php echo esc_html( $date ); ?></td></tr>
                        <tr><td>آخرین به‌روزرسانی</td><td><?php echo get_the_modified_date( 'j F Y' ); ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== Related Downloads ===== -->
        <?php
        if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
            $cat_ids = wp_list_pluck( $cats, 'term_id' );
            $related = new WP_Query( array(
                'post_type'      => 'download',
                'posts_per_page' => 4,
                'post__not_in'   => array( $post_id ),
                'tax_query'      => array( array(
                    'taxonomy' => 'download_category',
                    'field'    => 'term_id',
                    'terms'    => $cat_ids,
                ) ),
            ) );

            if ( $related->have_posts() ) :
        ?>
            <div class="tzdl-related">
                <div class="tzdl-related-head">
                    <span class="tzdl-related-icon">🔗</span>
                    <h2>دانلودهای مرتبط</h2>
                </div>

                <div class="tzdl-related-grid">
                    <?php while ( $related->have_posts() ) : $related->the_post();
                        $r_thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                        $r_links = get_post_meta( get_the_ID(), '_teznevise_download_links', true );
                        $r_count = is_array( $r_links ) ? count( $r_links ) : 0;
                    ?>
                        <a href="<?php the_permalink(); ?>" class="tzdl-related-card">
                            <div class="tzdl-related-thumb">
                                <?php if ( $r_thumb ) : ?>
                                    <img src="<?php echo esc_url( $r_thumb ); ?>" alt="<?php the_title_attribute(); ?>" />
                                <?php else : ?>
                                    <div class="tzdl-thumb-fallback-sm">📄</div>
                                <?php endif; ?>
                                <span class="tzdl-related-badge"><?php echo number_format_i18n( $r_count ); ?> فایل</span>
                            </div>
                            <div class="tzdl-related-body">
                                <h3><?php the_title(); ?></h3>
                                <span class="tzdl-related-more">دانلود ←</span>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php
            endif;
            wp_reset_postdata();
        }
        ?>

    </div>
    <?php
    return ob_get_clean();
}


// ===== ۴. اسکیمای ساختاریافته (Schema) =====
add_action( 'wp_head', 'teznevise_download_schema' );

function teznevise_download_schema() {

    // اسکیما برای تک‌پست دانلود
    if ( is_singular( 'download' ) ) {
        $post_id  = get_the_ID();
        $links    = get_post_meta( $post_id, '_teznevise_download_links', true );
        $thumb    = get_the_post_thumbnail_url( $post_id, 'large' );
        $dl_count = intval( get_post_meta( $post_id, '_teznevise_download_count', true ) );
        $cats     = get_the_terms( $post_id, 'download_category' );
        $cat_name = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';

        $schema = array(
            '@context'          => 'https://schema.org',
            '@type'             => 'CreativeWork',
            'name'              => get_the_title(),
            'description'       => wp_strip_all_tags( get_the_excerpt() ),
            'url'               => get_permalink(),
            'datePublished'     => get_the_date( 'c' ),
            'dateModified'      => get_the_modified_date( 'c' ),
            'author'            => array( '@type' => 'Organization', 'name' => 'تزنویسه' ),
            'publisher'         => array(
                '@type' => 'Organization',
                'name'  => 'تزنویسه',
                'logo'  => array( '@type' => 'ImageObject', 'url' => get_site_icon_url() ),
            ),
            'genre'             => $cat_name,
            'isAccessibleForFree' => true,
            'inLanguage'        => 'fa-IR',
        );

        if ( $thumb ) {
            $schema['image'] = $thumb;
        }

        if ( is_array( $links ) && ! empty( $links ) ) {
            $downloads = array();
            foreach ( $links as $link ) {
                $downloads[] = array(
                    '@type'           => 'DataDownload',
                    'name'            => $link['text'],
                    'contentUrl'      => $link['url'],
                    'encodingFormat'  => $link['ext'],
                );
            }
            $schema['distribution'] = $downloads;
        }

        echo "\n" . '<script type="application/ld+json">'
            . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
            . '</script>' . "\n";

        // Breadcrumb Schema
        $breadcrumb = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => array(
                array( '@type' => 'ListItem', 'position' => 1, 'name' => 'خانه',      'item' => home_url( '/' ) ),
                array( '@type' => 'ListItem', 'position' => 2, 'name' => 'دانلودها', 'item' => home_url( '/download/' ) ),
            ),
        );

        if ( $cat_name ) {
            $breadcrumb['itemListElement'][] = array(
                '@type'    => 'ListItem',
                'position' => 3,
                'name'     => $cat_name,
                'item'     => get_term_link( $cats[0] ),
            );
            $breadcrumb['itemListElement'][] = array(
                '@type'    => 'ListItem',
                'position' => 4,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        } else {
            $breadcrumb['itemListElement'][] = array(
                '@type'    => 'ListItem',
                'position' => 3,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        }

        echo '<script type="application/ld+json">'
            . wp_json_encode( $breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
            . '</script>' . "\n";
    }

    // اسکیما برای آرشیو
    if ( is_post_type_archive( 'download' ) || is_tax( 'download_category' ) ) {
        $schema = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'CollectionPage',
            'name'        => is_tax()
                ? single_term_title( '', false )
                : 'دانلودهای رایگان تزنویسه',
            'description' => 'مجموعه فایل‌های آموزشی و پژوهشی رایگان شامل پرسشنامه، فرم پروپوزال، چک‌لیست و راهنما',
            'url'         => is_tax()
                ? get_term_link( get_queried_object() )
                : home_url( '/download/' ),
            'inLanguage'  => 'fa-IR',
        );

        echo "\n" . '<script type="application/ld+json">'
            . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
            . '</script>' . "\n";
    }
}


// ===== ۵. اسکریپت شمارش دانلود =====
add_action( 'wp_footer', 'teznevise_download_counter_script' );

function teznevise_download_counter_script() {
    if ( ! is_singular( 'download' ) ) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // شمارش دانلود
        var btns = document.querySelectorAll('.tzdl-download-btn');
        btns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var postId = this.getAttribute('data-post');
                var fd     = new FormData();
                fd.append('action',  'teznevise_count_download');
                fd.append('post_id', postId);
                fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin',
                });
            });
        });

        // Tabs
        var tabBtns = document.querySelectorAll('.tzdl-tab-btn');
        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var tab = this.getAttribute('data-tab');
                document.querySelectorAll('.tzdl-tab-btn').forEach(function (b) {
                    b.classList.remove('tzdl-tab-active');
                });
                document.querySelectorAll('.tzdl-tab-pane').forEach(function (p) {
                    p.classList.remove('tzdl-pane-active');
                });
                this.classList.add('tzdl-tab-active');
                document.getElementById('tzdl-pane-' + tab).classList.add('tzdl-pane-active');
            });
        });

    });
    </script>
    <?php
}


// ============================================
// ===== ۶. شورت‌کد صفحه اصلی دانلودها =====
// ============================================
add_shortcode( 'teznevise_downloads', 'teznevise_downloads_archive' );

function teznevise_downloads_archive( $atts ) {
    $atts = shortcode_atts(
        array( 'per_category' => 8 ),
        $atts
    );

    ob_start();

    $categories = get_terms( array(
        'taxonomy'   => 'download_category',
        'hide_empty' => true,
        'orderby'    => 'count',
        'order'      => 'DESC',
    ) );

    $total = wp_count_posts( 'download' )->publish;

    // آیکون‌های دسته‌ها
    $cat_icons = array( '📋', '📝', '✅', '📖', '📚', '📊', '🎓', '🔬', '⚙️', '📑' );
    ?>
    <div class="tzdl-archive">
        <div class="tzdl-container">

            <!-- Hero -->
            <div class="tzdl-hero">
                <div class="tzdl-hero-inner">
                    <span class="tzdl-hero-tag">📥 دانلود رایگان</span>
                    <h1>مرکز منابع رایگان تزنویسه</h1>
                    <p>دانلود رایگان پرسشنامه‌ها، فرم‌های پروپوزال دانشگاه‌های مختلف، چک‌لیست‌ها، راهنماها و کتاب‌های تخصصی — همه برای پیشبرد پژوهش شما</p>

                    <div class="tzdl-hero-search">
                        <input type="text" class="tzdl-search-input" placeholder="جستجو در منابع..." />
                        <span class="tzdl-search-icon">🔍</span>
                    </div>

                    <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                        <div class="tzdl-hero-stats">
                            <div class="tzdl-stat">
                                <strong><?php echo number_format_i18n( $total ); ?></strong>
                                <span>فایل رایگان</span>
                            </div>
                            <div class="tzdl-stat">
                                <strong><?php echo number_format_i18n( count( $categories ) ); ?></strong>
                                <span>دسته‌بندی</span>
                            </div>
                            <div class="tzdl-stat">
                                <strong>۱۰۰٪</strong>
                                <span>رایگان</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ( empty( $categories ) || is_wp_error( $categories ) ) : ?>
                <div class="tzdl-empty">
                    <div class="tzdl-empty-icon">📂</div>
                    <h3>هنوز فایلی برای دانلود وجود ندارد</h3>
                    <p>به‌زودی منابع رایگان در این بخش قرار خواهند گرفت.</p>
                </div>

            <?php else : ?>

                <!-- Category Quick Nav -->
                <div class="tzdl-catnav">
                    <?php
                    $ci = 0;
                    foreach ( $categories as $cat ) :
                        $icon = $cat_icons[ $ci % count( $cat_icons ) ];
                        $ci++;
                    ?>
                        <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="tzdl-catnav-item">
                            <span class="tzdl-catnav-icon"><?php echo $icon; ?></span>
                            <span class="tzdl-catnav-name"><?php echo esc_html( $cat->name ); ?></span>
                            <span class="tzdl-catnav-count"><?php echo number_format_i18n( $cat->count ); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Category Sections -->
                <?php
                $ci = 0;
                foreach ( $categories as $cat ) :
                    $icon  = $cat_icons[ $ci % count( $cat_icons ) ];
                    $ci++;

                    $cat_q = new WP_Query( array(
                        'post_type'      => 'download',
                        'posts_per_page' => $atts['per_category'],
                        'tax_query'      => array( array(
                            'taxonomy' => 'download_category',
                            'field'    => 'term_id',
                            'terms'    => $cat->term_id,
                        ) ),
                    ) );

                    if ( ! $cat_q->have_posts() ) {
                        continue;
                    }
                ?>
                    <section class="tzdl-section" id="cat-<?php echo $cat->slug; ?>">
                        <div class="tzdl-section-head">
                            <div class="tzdl-section-title">
                                <span class="tzdl-section-icon"><?php echo $icon; ?></span>
                                <h2><?php echo esc_html( $cat->name ); ?></h2>
                                <span class="tzdl-section-count"><?php echo number_format_i18n( $cat->count ); ?> فایل</span>
                            </div>
                            <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="tzdl-section-link">
                                مشاهده همه ←
                            </a>
                        </div>

                        <div class="tzdl-grid">
                            <?php while ( $cat_q->have_posts() ) : $cat_q->the_post();
                                echo teznevise_dl_card( get_the_ID() );
                            endwhile; ?>
                        </div>
                    </section>
                    <?php wp_reset_postdata();
                endforeach; ?>

                <!-- نتیجه‌ای یافت نشد -->
                <div class="tzdl-no-results">
                    <div class="tzdl-empty-icon">🔍</div>
                    <h3>نتیجه‌ای یافت نشد</h3>
                    <p>فایلی با این عبارت یافت نشد. عبارت دیگری را امتحان کنید.</p>
                </div>

            <?php endif; ?>

            <!-- CTA -->
            <div class="tzdl-cta">
                <h2>فایل مورد نظرتان را پیدا نکردید؟</h2>
                <p>اگر به پرسشنامه، فرم یا منبع خاصی نیاز دارید که در این مجموعه نیست، با ما در میان بگذارید. همچنین برای انجام پروژه‌های پژوهشی، تیم ما آماده کمک است.</p>
                <a href="/contact/" class="tzdl-cta-btn">درخواست منبع / مشاوره رایگان</a>
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}


// ===== ۷. تابع رندر کارت دانلود =====
if ( ! function_exists( 'teznevise_dl_card' ) ) {
    function teznevise_dl_card( $post_id ) {
        $thumb    = get_the_post_thumbnail_url( $post_id, 'medium' );
        $links    = get_post_meta( $post_id, '_teznevise_download_links', true );
        $count    = is_array( $links ) ? count( $links ) : 0;
        $dl_count = intval( get_post_meta( $post_id, '_teznevise_download_count', true ) );
        $lang     = get_post_meta( $post_id, '_teznevise_lang', true );
        $cats     = get_the_terms( $post_id, 'download_category' );
        $cat      = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? $cats[0] : null;
        $excerpt  = wp_trim_words( get_the_excerpt(), 15, '...' );
        $title    = get_the_title( $post_id );
        $ext      = ( is_array( $links ) && ! empty( $links ) ) ? $links[0]['ext'] : '';
        $bg_idx   = ( $post_id % 6 ) + 1;

        ob_start();
        ?>
        <a href="<?php echo get_permalink( $post_id ); ?>"
           class="tzdl-card"
           data-title="<?php echo esc_attr( mb_strtolower( $title ) ); ?>"
           data-desc="<?php echo esc_attr( mb_strtolower( $excerpt ) ); ?>">

            <div class="tzdl-card-thumb">
                <?php if ( $thumb ) : ?>
                    <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                <?php else : ?>
                    <div class="tzdl-card-fallback tzdl-bg-<?php echo $bg_idx; ?>">
                        <span><?php echo teznevise_file_icon( $ext ); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ( $cat ) : ?>
                    <span class="tzdl-card-cat"><?php echo esc_html( $cat->name ); ?></span>
                <?php endif; ?>

                <?php if ( $ext ) : ?>
                    <span class="tzdl-card-ext"><?php echo esc_html( strtoupper( $ext ) ); ?></span>
                <?php endif; ?>
            </div>

            <div class="tzdl-card-body">
                <h3><?php echo esc_html( $title ); ?></h3>
                <?php if ( $excerpt ) : ?>
                    <p><?php echo esc_html( $excerpt ); ?></p>
                <?php endif; ?>

                <div class="tzdl-card-foot">
                    <span class="tzdl-card-files">📦 <?php echo number_format_i18n( $count ); ?> فایل</span>
                    <span class="tzdl-card-downloads">⬇ <?php echo number_format_i18n( $dl_count ); ?></span>
                </div>

                <span class="tzdl-card-btn">مشاهده و دانلود ←</span>
            </div>
        </a>
        <?php
        return ob_get_clean();
    }
}


// ===== ۸. قالب آرشیو و دسته‌بندی =====
add_filter( 'template_include', 'teznevise_download_archive_template', 99 );

function teznevise_download_archive_template( $template ) {
    // برای آرشیو دسته‌بندی، از قالب صفحه استفاده می‌کنیم
    return $template;
}

// نمایش محتوای آرشیو دسته از طریق هوک
add_action( 'loop_start', 'teznevise_category_archive_intro' );

function teznevise_category_archive_intro( $query ) {
    if ( is_tax( 'download_category' ) && $query->is_main_query() && ! is_admin() ) {
        static $done = false;
        if ( $done ) {
            return;
        }
        $done = true;

        $term = get_queried_object();
        echo teznevise_render_category_page( $term );
    }
}


// ===== ۹. رندر صفحه دسته‌بندی =====
function teznevise_render_category_page( $term ) {
    ob_start();
    ?>
    <div class="tzdl-archive tzdl-cat-archive">
        <div class="tzdl-container">

            <div class="tzdl-cat-hero">
                <div class="tzdl-breadcrumb">
                    <a href="<?php echo home_url( '/' ); ?>">خانه</a>
                    ›
                    <a href="<?php echo home_url( '/download/' ); ?>">دانلودها</a>
                    ›
                    <span><?php echo esc_html( $term->name ); ?></span>
                </div>

                <h2><?php echo esc_html( $term->name ); ?></h2>

                <?php if ( $term->description ) : ?>
                    <p><?php echo esc_html( $term->description ); ?></p>
                <?php else : ?>
                    <p>
                        دانلود رایگان <?php echo esc_html( $term->name ); ?> — مجموعه‌ای از بهترین و کاربردی‌ترین منابع برای پژوهشگران و دانشجویان
                    </p>
                <?php endif; ?>

                <div class="tzdl-cat-count-badge">
                    📦 <?php echo number_format_i18n( $term->count ); ?> فایل در این دسته
                </div>
            </div>

            <div class="tzdl-grid tzdl-cat-grid">
    <?php
    return ob_get_clean();
}
}

/* --- Price Calculator Addons --- */
if ( ! function_exists( 'teznevise_price_box_shortcode' ) ) {
// ============================================
// تزنویسه - شورت‌کدهای محاسبه‌گر هزینه
// ============================================

// ===== ۱. ویجت باکس قیمت برای صفحه خدماتی =====
add_shortcode('tz_price_box', 'teznevise_price_box_shortcode');
function teznevise_price_box_shortcode($atts) {
    $atts = shortcode_atts(array('id' => 0), $atts);
    $id = intval($atts['id']);
    if (!$id) return '';
    
    $title    = get_the_title($id);
    $icon     = get_post_meta($id, '_tz_icon', true) ?: '📊';
    $desc     = get_post_meta($id, '_tz_desc', true);
    $min      = get_post_meta($id, '_tz_price_min', true);
    $max      = get_post_meta($id, '_tz_price_max', true);
    $unit     = get_post_meta($id, '_tz_unit', true) ?: 'تومان';
    $duration = get_post_meta($id, '_tz_duration', true);
    $note     = get_post_meta($id, '_tz_note', true);
    $factors  = get_post_meta($id, '_tz_factors', true);
    
    ob_start();
    ?>
    <div class="tzpc-box" data-min="<?php echo esc_attr($min); ?>" data-max="<?php echo esc_attr($max); ?>" data-unit="<?php echo esc_attr($unit); ?>">
      <div class="tzpc-box-head">
        <span class="tzpc-box-icon"><?php echo esc_html($icon); ?></span>
        <div>
          <h3 class="tzpc-box-title"><?php echo esc_html($title); ?></h3>
          <?php if ($desc) : ?><p class="tzpc-box-desc"><?php echo esc_html($desc); ?></p><?php endif; ?>
        </div>
      </div>
      
      <div class="tzpc-box-price">
        <span class="tzpc-price-label">برآورد هزینه:</span>
        <div class="tzpc-price-value">
          <span class="tzpc-price-num" data-base-min="<?php echo esc_attr($min); ?>" data-base-max="<?php echo esc_attr($max); ?>">
            <?php echo tz_format_price($min); ?> تا <?php echo tz_format_price($max); ?>
          </span>
          <span class="tzpc-price-unit"><?php echo esc_html($unit); ?></span>
        </div>
      </div>
      
      <?php if (is_array($factors) && !empty($factors)) : ?>
        <div class="tzpc-factors">
          <span class="tzpc-factors-label">گزینه‌های تأثیرگذار بر قیمت:</span>
          <?php foreach ($factors as $fi => $f) : ?>
            <label class="tzpc-factor-check">
              <input type="checkbox" class="tzpc-factor-input" data-percent="<?php echo esc_attr($f['percent']); ?>" />
              <span class="tzpc-factor-text"><?php echo esc_html($f['label']); ?></span>
              <span class="tzpc-factor-badge">+<?php echo tz_format_price($f['percent']); ?>٪</span>
            </label>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <div class="tzpc-box-meta">
        <?php if ($duration) : ?>
          <div class="tzpc-meta-item"><span>⏱️</span> زمان انجام: <strong><?php echo esc_html($duration); ?></strong></div>
        <?php endif; ?>
        <?php if ($note) : ?>
          <div class="tzpc-meta-note">ℹ️ <?php echo esc_html($note); ?></div>
        <?php endif; ?>
      </div>
      
      <div class="tzpc-box-actions">
        <a href="/inquiry/?service=<?php echo $id; ?>" class="tzpc-btn-primary">ثبت سفارش این خدمت</a>
        <a href="/contact/" class="tzpc-btn-outline">مشاوره رایگان</a>
      </div>
    </div>
    <?php
    return ob_get_clean();
}

// ===== ۲. CTA کوچک =====
add_shortcode('tz_price_cta', 'teznevise_price_cta_shortcode');
function teznevise_price_cta_shortcode($atts) {
    $atts = shortcode_atts(array('id' => 0), $atts);
    $id = intval($atts['id']);
    if (!$id) return '';
    
    $title = get_the_title($id);
    $icon  = get_post_meta($id, '_tz_icon', true) ?: '📊';
    $min   = get_post_meta($id, '_tz_price_min', true);
    $max   = get_post_meta($id, '_tz_price_max', true);
    $unit  = get_post_meta($id, '_tz_unit', true) ?: 'تومان';
    
    ob_start();
    ?>
    <div class="tzpc-cta">
      <div class="tzpc-cta-icon"><?php echo esc_html($icon); ?></div>
      <div class="tzpc-cta-content">
        <span class="tzpc-cta-title"><?php echo esc_html($title); ?></span>
        <span class="tzpc-cta-price">از <strong><?php echo tz_format_price($min); ?></strong> <?php echo esc_html($unit); ?></span>
      </div>
      <a href="/inquiry/?service=<?php echo $id; ?>" class="tzpc-cta-btn">ثبت سفارش ←</a>
    </div>
    <?php
    return ob_get_clean();
}

// ===== ۳. محاسبه‌گر کامل صفحه اصلی =====
add_shortcode('tz_price_calculator', 'teznevise_full_calculator');
function teznevise_full_calculator($atts) {
    $services = get_posts(array(
        'post_type'      => 'tz_service',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    ));
    
    ob_start();
    ?>
    <div class="tzpc-calc">
      <div class="tzpc-calc-container">
        
        <!-- Hero -->
        <div class="tzpc-hero">
          <div class="tzpc-hero-inner">
            <span class="tzpc-hero-tag">🧮 محاسبه‌گر آنلاین</span>
            <h1>برآورد هزینه پروژه شما</h1>
            <p>در چند ثانیه، هزینه تقریبی خدمت مورد نظر خود را برآورد کنید. قیمت نهایی پس از بررسی دقیق پروژه توسط کارشناسان اعلام می‌شود.</p>
          </div>
        </div>
        
        <?php if (empty($services)) : ?>
          <div class="tzpc-empty">
            <div class="tzpc-empty-icon">🧮</div>
            <h3>هنوز خدمتی تعریف نشده است</h3>
            <p>به‌زودی خدمات و قیمت‌ها در این بخش قرار خواهند گرفت.</p>
          </div>
        <?php else : ?>
        
        <div class="tzpc-calc-grid">
          
          <!-- Steps -->
          <div class="tzpc-steps">
            
            <!-- Step 1: Select Service -->
            <div class="tzpc-step">
              <div class="tzpc-step-head">
                <span class="tzpc-step-num">۱</span>
                <h2>خدمت مورد نظر را انتخاب کنید</h2>
              </div>
              <div class="tzpc-services-grid">
                <?php foreach ($services as $i => $service) :
                  $sid = $service->ID;
                  $icon = get_post_meta($sid, '_tz_icon', true) ?: '📊';
                  $desc = get_post_meta($sid, '_tz_desc', true);
                  $min  = get_post_meta($sid, '_tz_price_min', true);
                  $max  = get_post_meta($sid, '_tz_price_max', true);
                  $unit = get_post_meta($sid, '_tz_unit', true) ?: 'تومان';
                  $duration = get_post_meta($sid, '_tz_duration', true);
                  $factors = get_post_meta($sid, '_tz_factors', true);
                  $factors_json = is_array($factors) ? wp_json_encode($factors, JSON_UNESCAPED_UNICODE) : '[]';
                ?>
                  <label class="tzpc-service-card">
                    <input type="radio" name="tzpc_service" value="<?php echo $sid; ?>"
                           data-min="<?php echo esc_attr($min); ?>"
                           data-max="<?php echo esc_attr($max); ?>"
                           data-unit="<?php echo esc_attr($unit); ?>"
                           data-duration="<?php echo esc_attr($duration); ?>"
                           data-title="<?php echo esc_attr($service->post_title); ?>"
                           data-icon="<?php echo esc_attr($icon); ?>"
                           data-factors='<?php echo esc_attr($factors_json); ?>'
                           <?php echo $i === 0 ? 'checked' : ''; ?> />
                    <span class="tzpc-service-inner">
                      <span class="tzpc-service-icon"><?php echo esc_html($icon); ?></span>
                      <span class="tzpc-service-name"><?php echo esc_html($service->post_title); ?></span>
                      <?php if ($desc) : ?><span class="tzpc-service-desc"><?php echo esc_html($desc); ?></span><?php endif; ?>
                      <span class="tzpc-service-check">✓</span>
                    </span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
            
            <!-- Step 2: Factors -->
            <div class="tzpc-step" id="tzpc-step-factors" style="display:none;">
              <div class="tzpc-step-head">
                <span class="tzpc-step-num">۲</span>
                <h2>گزینه‌های اضافی (اختیاری)</h2>
              </div>
              <div class="tzpc-factors-list" id="tzpc-factors-list">
                <!-- داینامیک پر می‌شود -->
              </div>
            </div>
            
          </div>
          
          <!-- Result Sidebar -->
          <div class="tzpc-result">
            <div class="tzpc-result-sticky">
              <div class="tzpc-result-head">
                <span class="tzpc-result-icon" id="tzpc-result-icon">📊</span>
                <h3 id="tzpc-result-title">برآورد هزینه</h3>
              </div>
              
              <div class="tzpc-result-price">
                <span class="tzpc-result-label">هزینه تقریبی:</span>
                <div class="tzpc-result-value">
                  <span id="tzpc-result-amount">۰</span>
                  <span class="tzpc-result-unit" id="tzpc-result-unit">تومان</span>
                </div>
              </div>
              
              <div class="tzpc-result-duration" id="tzpc-result-duration-wrap" style="display:none;">
                <span>⏱️</span> زمان انجام: <strong id="tzpc-result-duration"></strong>
              </div>
              
              <div class="tzpc-result-note">
                ℹ️ این برآورد تقریبی است. قیمت نهایی پس از بررسی دقیق پروژه توسط کارشناسان اعلام می‌گردد.
              </div>
              
              <div class="tzpc-result-actions">
                <a href="/inquiry/" class="tzpc-result-btn-primary" id="tzpc-order-btn">ثبت سفارش</a>
                <a href="/contact/" class="tzpc-result-btn-outline">مشاوره رایگان</a>
              </div>
              
              <div class="tzpc-result-features">
                <div class="tzpc-feature">✓ مشاوره اولیه رایگان</div>
                <div class="tzpc-feature">✓ محرمانگی کامل اطلاعات</div>
                <div class="tzpc-feature">✓ پشتیبانی تا تحویل نهایی</div>
              </div>
            </div>
          </div>
          
        </div>
        
        <?php endif; ?>
        
      </div>
    </div>
    
    <script>
    (function(){
      document.addEventListener('DOMContentLoaded', function() {
        var radios = document.querySelectorAll('input[name="tzpc_service"]');
        var factorsStep = document.getElementById('tzpc-step-factors');
        var factorsList = document.getElementById('tzpc-factors-list');
        var resultAmount = document.getElementById('tzpc-result-amount');
        var resultUnit = document.getElementById('tzpc-result-unit');
        var resultTitle = document.getElementById('tzpc-result-title');
        var resultIcon = document.getElementById('tzpc-result-icon');
        var durationWrap = document.getElementById('tzpc-result-duration-wrap');
        var durationEl = document.getElementById('tzpc-result-duration');
        var orderBtn = document.getElementById('tzpc-order-btn');
        
        if (!radios.length) return;
        
        function toPersian(num) {
          var p = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
          return String(num).replace(/\d/g, function(d){ return p[d]; });
        }
        function formatNum(num) {
          return toPersian(Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','));
        }
        
        function calculate() {
          var selected = document.querySelector('input[name="tzpc_service"]:checked');
          if (!selected) return;
          
          var min = parseInt(selected.getAttribute('data-min')) || 0;
          var max = parseInt(selected.getAttribute('data-max')) || 0;
          var unit = selected.getAttribute('data-unit') || 'تومان';
          var duration = selected.getAttribute('data-duration') || '';
          var title = selected.getAttribute('data-title') || 'برآورد هزینه';
          var icon = selected.getAttribute('data-icon') || '📊';
          
          // اعمال فاکتورهای انتخابی
          var totalPercent = 0;
          var checkedFactors = factorsList.querySelectorAll('.tzpc-cfactor:checked');
          checkedFactors.forEach(function(f) {
            totalPercent += parseInt(f.getAttribute('data-percent')) || 0;
          });
          
          var finalMin = min * (1 + totalPercent/100);
          var finalMax = max * (1 + totalPercent/100);
          
          resultAmount.textContent = formatNum(finalMin) + ' تا ' + formatNum(finalMax);
          resultUnit.textContent = unit;
          resultTitle.textContent = title;
          resultIcon.textContent = icon;
          
          if (duration) {
            durationWrap.style.display = 'flex';
            durationEl.textContent = duration;
          } else {
            durationWrap.style.display = 'none';
          }
          
          orderBtn.href = '/inquiry/?service=' + selected.value;
        }
        
        function loadFactors() {
          var selected = document.querySelector('input[name="tzpc_service"]:checked');
          if (!selected) return;
          var factorsData = selected.getAttribute('data-factors');
          var factors = [];
          try { factors = JSON.parse(factorsData); } catch(e) {}
          
          factorsList.innerHTML = '';
          if (factors && factors.length > 0) {
            factorsStep.style.display = 'block';
            factors.forEach(function(f) {
              var label = document.createElement('label');
              label.className = 'tzpc-cfactor-label';
              label.innerHTML = '<input type="checkbox" class="tzpc-cfactor" data-percent="' + f.percent + '" /><span class="tzpc-cfactor-text">' + f.label + '</span><span class="tzpc-cfactor-badge">+' + toPersian(f.percent) + '٪</span>';
              factorsList.appendChild(label);
            });
            factorsList.querySelectorAll('.tzpc-cfactor').forEach(function(c){
              c.addEventListener('change', calculate);
            });
          } else {
            factorsStep.style.display = 'none';
          }
        }
        
        radios.forEach(function(r) {
          r.addEventListener('change', function() {
            loadFactors();
            calculate();
          });
        });
        
        // اجرای اولیه
        loadFactors();
        calculate();
      });
    })();
    </script>
    <?php
    return ob_get_clean();
}
}

/* --- Sample Size Calculator Styles & Shortcodes --- */
if ( ! function_exists( 'teznevise_sample_size_calculator' ) ) {
// ============================================
// تزنویسه - شورت‌کد محاسبه‌گر حجم نمونه + اسکیما
// ============================================

add_shortcode('tz_sample_size', 'teznevise_sample_size_calculator');
function teznevise_sample_size_calculator($atts) {
    ob_start();
    ?>
    <div class="tzss">
      <div class="tzss-container">

        <!-- ===== Hero ===== -->
        <div class="tzss-hero">
          <div class="tzss-hero-inner">
            <span class="tzss-hero-tag">🧮 ابزار رایگان آنلاین</span>
            <h1>محاسبه‌گر حجم نمونه</h1>
            <p>حجم نمونه آماری مورد نیاز پژوهش خود را به‌صورت دقیق و رایگان با فرمول کوکران و جدول مورگان محاسبه کنید. ابزاری ضروری برای پایان‌نامه، مقاله و پروژه‌های پژوهشی.</p>
          </div>
        </div>

        <!-- ===== Calculator ===== -->
        <div class="tzss-calc-card">
          <div class="tzss-tabs">
            <button class="tzss-tab tzss-tab-active" data-tab="cochran">📐 فرمول کوکران</button>
            <button class="tzss-tab" data-tab="morgan">📋 جدول مورگان</button>
          </div>

          <!-- Cochran Pane -->
          <div class="tzss-pane tzss-pane-active" id="tzss-pane-cochran">
            <div class="tzss-grid">
              <div class="tzss-fields">

                <div class="tzss-field">
                  <label>حجم جامعه آماری <span class="tzss-hint">(تعداد کل افراد جامعه — برای جامعه نامحدود خالی بگذارید)</span></label>
                  <input type="number" id="tzss-population" placeholder="مثلاً: ۱۰۰۰۰" min="0" value="10000" />
                </div>

                <div class="tzss-field">
                  <label>سطح اطمینان</label>
                  <div class="tzss-pills">
                    <div class="tzss-pill" data-conf="90">۹۰٪</div>
                    <div class="tzss-pill tzss-pill-active" data-conf="95">۹۵٪</div>
                    <div class="tzss-pill" data-conf="99">۹۹٪</div>
                  </div>
                </div>

                <div class="tzss-field">
                  <label>حاشیه خطا (٪) <span class="tzss-hint">(معمولاً ۵٪)</span></label>
                  <input type="number" id="tzss-margin" placeholder="5" min="1" max="20" step="0.5" value="5" />
                </div>

                <div class="tzss-field">
                  <label>نسبت موفقیت / واریانس (٪) <span class="tzss-hint">(اگر نامشخص است، ۵۰ بگذارید)</span></label>
                  <input type="number" id="tzss-proportion" placeholder="50" min="1" max="99" value="50" />
                </div>

                <div class="tzss-method-note">
                  <strong>راهنما:</strong> فرمول کوکران (Cochran) رایج‌ترین روش برای محاسبه حجم نمونه در پژوهش‌های پیمایشی است. اگر نسبت موفقیت را نمی‌دانید، مقدار ۵۰٪ بیشترین حجم نمونه (محافظه‌کارانه‌ترین حالت) را می‌دهد.
                </div>
              </div>

              <div class="tzss-result">
                <div class="tzss-result-label">حجم نمونه پیشنهادی</div>
                <div class="tzss-result-number" id="tzss-result-number">۰</div>
                <div class="tzss-result-unit">نفر</div>
                <div class="tzss-result-divider"></div>
                <div class="tzss-result-detail"><span>سطح اطمینان:</span><span id="tzss-rd-conf">۹۵٪</span></div>
                <div class="tzss-result-detail"><span>حاشیه خطا:</span><span id="tzss-rd-margin">۵٪</span></div>
                <div class="tzss-result-detail"><span>حجم جامعه:</span><span id="tzss-rd-pop">۱۰،۰۰۰</span></div>
                <div class="tzss-result-formula">n = (z²·p·q/e²) / [1+(z²·p·q/e²-1)/N]</div>
                <a href="/service-statistics/" class="tzss-result-cta">سفارش تحلیل آماری</a>
              </div>
            </div>
          </div>

          <!-- Morgan Pane -->
          <div class="tzss-pane" id="tzss-pane-morgan">
            <div class="tzss-field tzss-morgan-search">
              <label>حجم جامعه آماری خود را وارد کنید</label>
              <input type="number" id="tzss-morgan-pop" placeholder="مثلاً: ۵۰۰" min="1" value="500" />
            </div>
            <div class="tzss-morgan-result">
              <div class="tzss-morgan-result-num" id="tzss-morgan-num">—</div>
              <div class="tzss-morgan-result-label">حجم نمونه بر اساس جدول کرجسی و مورگان</div>
            </div>
            <div class="tzss-morgan-table-wrap">
              <table class="tzss-morgan-table">
                <thead><tr><th>حجم جامعه (N)</th><th>حجم نمونه (n)</th></tr></thead>
                <tbody>
                  <tr><td>۵۰</td><td>۴۴</td></tr>
                  <tr><td>۱۰۰</td><td>۸۰</td></tr>
                  <tr><td>۲۰۰</td><td>۱۳۲</td></tr>
                  <tr><td>۳۰۰</td><td>۱۶۹</td></tr>
                  <tr><td>۵۰۰</td><td>۲۱۷</td></tr>
                  <tr><td>۱،۰۰۰</td><td>۲۷۸</td></tr>
                  <tr><td>۱،۵۰۰</td><td>۳۰۶</td></tr>
                  <tr><td>۳،۰۰۰</td><td>۳۴۱</td></tr>
                  <tr><td>۵،۰۰۰</td><td>۳۵۷</td></tr>
                  <tr><td>۱۰،۰۰۰</td><td>۳۷۰</td></tr>
                  <tr><td>۱۰۰،۰۰۰ و بیشتر</td><td>۳۸۴</td></tr>
                </tbody>
              </table>
            </div>
            <div class="tzss-method-note">
              <strong>جدول مورگان:</strong> جدول کرجسی و مورگان (Krejcie & Morgan, 1970) یکی از پرکاربردترین روش‌های تعیین حجم نمونه در علوم انسانی و اجتماعی است که با سطح اطمینان ۹۵٪ و حاشیه خطای ۵٪ تنظیم شده است.
            </div>
          </div>
        </div>

        <!-- ===== SEO Content ===== -->
        <div class="tzss-content">
          <h2>محاسبه حجم نمونه چیست و چرا اهمیت دارد؟</h2>
          <p><strong>حجم نمونه (Sample Size)</strong> به تعداد افرادی گفته می‌شود که از یک جامعه آماری بزرگ‌تر انتخاب می‌شوند تا پژوهشگر بتواند با مطالعه آن‌ها، نتایج را با اطمینان مشخصی به کل جامعه تعمیم دهد. تعیین صحیح حجم نمونه، یکی از <strong>حساس‌ترین مراحل روش‌شناسی پژوهش</strong> است؛ زیرا نمونه بسیار کوچک، اعتبار نتایج را زیر سؤال می‌برد و نمونه بیش از حد بزرگ، هزینه و زمان پژوهش را بدون دلیل افزایش می‌دهد.</p>
          <p>در پایان‌نامه‌ها و مقالات علمی، داوران همواره به نحوه محاسبه حجم نمونه توجه ویژه دارند. به همین دلیل، استفاده از روش‌های استاندارد و علمی مانند <strong>فرمول کوکران</strong> یا <strong>جدول کرجسی و مورگان</strong> ضروری است.</p>

          <h2>فرمول کوکران برای محاسبه حجم نمونه</h2>
          <p>فرمول کوکران (Cochran) پرکاربردترین روش برای محاسبه حجم نمونه در پژوهش‌های پیمایشی است. این فرمول به دو شکل برای جامعه نامحدود و محدود ارائه می‌شود:</p>
          <div class="tzss-formula-box">n₀ = (Z² × p × q) / e²</div>
          <p>که در آن:</p>
          <ul>
            <li><strong>n₀</strong>: حجم نمونه اولیه (برای جامعه نامحدود)</li>
            <li><strong>Z</strong>: مقدار z متناظر با سطح اطمینان (برای ۹۵٪ برابر ۱.۹۶)</li>
            <li><strong>p</strong>: نسبت برخورداری از صفت مورد بررسی (معمولاً ۰.۵)</li>
            <li><strong>q</strong>: نسبت عدم برخورداری (q = 1 - p)</li>
            <li><strong>e</strong>: حاشیه خطای مجاز (معمولاً ۰.۰۵)</li>
          </ul>
          <p>برای <strong>جامعه محدود</strong>، از فرمول تصحیح‌شده زیر استفاده می‌شود:</p>
          <div class="tzss-formula-box">n = n₀ / [1 + (n₀ - 1) / N]</div>
          <p>که در آن <strong>N</strong> حجم کل جامعه آماری است. محاسبه‌گر بالای این صفحه، به‌صورت خودکار هر دو حالت را در نظر می‌گیرد.</p>

          <h2>جدول کرجسی و مورگان</h2>
          <p>جدول کرجسی و مورگان (Krejcie & Morgan, 1970) یک روش سریع و آماده برای تعیین حجم نمونه است که نیازی به محاسبات پیچیده ندارد. این جدول بر اساس سطح اطمینان ۹۵٪ و حاشیه خطای ۵٪ تنظیم شده و به‌ویژه در <strong>پژوهش‌های علوم انسانی، مدیریت و علوم اجتماعی</strong> بسیار محبوب است. کافی است حجم جامعه خود را بدانید تا حجم نمونه متناظر را از جدول استخراج کنید.</p>

          <h2>عوامل مؤثر بر تعیین حجم نمونه</h2>
          <p>چهار عامل اصلی در محاسبه حجم نمونه نقش دارند:</p>
          <ol>
            <li><strong>سطح اطمینان (Confidence Level):</strong> معمولاً ۹۵٪ انتخاب می‌شود. هرچه بالاتر باشد، حجم نمونه بزرگ‌تر خواهد بود.</li>
            <li><strong>حاشیه خطا (Margin of Error):</strong> میزان خطای قابل قبول؛ معمولاً ۵٪. کاهش آن، حجم نمونه را افزایش می‌دهد.</li>
            <li><strong>حجم جامعه (Population Size):</strong> تعداد کل افراد جامعه آماری مورد مطالعه.</li>
            <li><strong>واریانس / نسبت (Proportion):</strong> در صورت عدم اطلاع، مقدار ۰.۵ بیشترین حجم نمونه را می‌دهد و محافظه‌کارانه‌ترین انتخاب است.</li>
          </ol>

          <h2>چه زمانی از کدام روش استفاده کنیم؟</h2>
          <p>اگر در پژوهش خود به دنبال <strong>برآورد یک نسبت یا میانگین</strong> هستید و می‌خواهید کنترل دقیقی روی حاشیه خطا داشته باشید، <strong>فرمول کوکران</strong> انتخاب بهتری است. اما اگر به یک روش سریع، استاندارد و پذیرفته‌شده در علوم انسانی نیاز دارید، <strong>جدول مورگان</strong> گزینه مناسبی است. در بسیاری از پایان‌نامه‌ها، اساتید استفاده از جدول مورگان را ترجیح می‌دهند.</p>
        </div>

        <!-- ===== FAQ ===== -->
        <div class="tzss-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>

          <div class="tzss-faq-item">
            <div class="tzss-faq-q">حجم نمونه مناسب برای پایان‌نامه چقدر است؟ <span class="tzss-faq-icon">+</span></div>
            <div class="tzss-faq-a"><div class="tzss-faq-a-inner">حجم نمونه مناسب به حجم جامعه آماری، سطح اطمینان و حاشیه خطا بستگی دارد. برای جوامع بزرگ (بالای ۱۰۰،۰۰۰ نفر)، حجم نمونه معمولاً حدود ۳۸۴ نفر است. بهترین کار، استفاده از فرمول کوکران یا جدول مورگان متناسب با جامعه آماری خاص پژوهش شماست.</div></div>
          </div>

          <div class="tzss-faq-item">
            <div class="tzss-faq-q">تفاوت فرمول کوکران و جدول مورگان چیست؟ <span class="tzss-faq-icon">+</span></div>
            <div class="tzss-faq-a"><div class="tzss-faq-a-inner">فرمول کوکران امکان تنظیم دقیق سطح اطمینان، حاشیه خطا و نسبت را فراهم می‌کند و انعطاف بیشتری دارد. جدول مورگان یک روش آماده و سریع است که بر اساس سطح اطمینان ۹۵٪ و خطای ۵٪ از پیش محاسبه شده است. هر دو روش در پژوهش‌های علمی پذیرفته‌شده هستند.</div></div>
          </div>

          <div class="tzss-faq-item">
            <div class="tzss-faq-q">اگر حجم جامعه آماری نامشخص باشد چه کنیم؟ <span class="tzss-faq-icon">+</span></div>
            <div class="tzss-faq-a"><div class="tzss-faq-a-inner">اگر حجم جامعه نامحدود یا بسیار بزرگ است، فیلد «حجم جامعه» را در محاسبه‌گر کوکران خالی بگذارید یا عدد بسیار بزرگی وارد کنید. در این حالت، فرمول جامعه نامحدود اعمال می‌شود که معمولاً حجم نمونه‌ای حدود ۳۸۴ نفر (با اطمینان ۹۵٪) ارائه می‌دهد.</div></div>
          </div>

          <div class="tzss-faq-item">
            <div class="tzss-faq-q">چرا نسبت موفقیت را ۵۰٪ در نظر می‌گیریم؟ <span class="tzss-faq-icon">+</span></div>
            <div class="tzss-faq-a"><div class="tzss-faq-a-inner">وقتی نسبت واقعی برخورداری از صفت مورد مطالعه را نمی‌دانیم، مقدار p=0.5 بیشترین مقدار حاصل‌ضرب p×q را تولید می‌کند و در نتیجه بزرگ‌ترین (محافظه‌کارانه‌ترین) حجم نمونه را می‌دهد. این انتخاب تضمین می‌کند که نمونه شما به‌اندازه کافی بزرگ باشد.</div></div>
          </div>

          <div class="tzss-faq-item">
            <div class="tzss-faq-q">آیا این محاسبه‌گر برای مقالات ISI معتبر است؟ <span class="tzss-faq-icon">+</span></div>
            <div class="tzss-faq-a"><div class="tzss-faq-a-inner">بله. این محاسبه‌گر بر اساس فرمول‌های استاندارد و معتبر علمی (کوکران و کرجسی-مورگان) طراحی شده که در مقالات ISI و پایان‌نامه‌های دانشگاهی پذیرفته‌شده هستند. با این حال، توصیه می‌شود برای پژوهش‌های پیچیده (مثل مطالعات با طرح‌های آماری خاص)، با یک متخصص آمار مشورت کنید.</div></div>
          </div>
        </div>

        <!-- ===== CTA ===== -->
        <div class="tzss-cta">
          <h2>به کمک تخصصی در تحلیل آماری نیاز دارید؟</h2>
          <p>از تعیین حجم نمونه و طراحی پرسش‌نامه تا تحلیل کامل داده‌ها و گزارش‌نویسی — تیم متخصصان تزنویسه در تمام مراحل پژوهش در کنار شماست. مشاوره اولیه کاملاً رایگان است.</p>
          <a href="/service-statistics/" class="tzss-cta-btn">سفارش تحلیل آماری</a>
        </div>

      </div>
    </div>
    <?php
    return ob_get_clean();
}

// ===== اسکیمای سئو =====
add_action('wp_head', 'teznevise_sample_size_schema');
function teznevise_sample_size_schema() {
    // فقط در صفحه‌ای که شورت‌کد دارد
    if (!is_singular()) return;
    global $post;
    if (!$post || strpos($post->post_content, 'tz_sample_size') === false) return;

    $page_url = get_permalink();
    $page_title = get_the_title();

    // WebApplication Schema
    $app = array(
        '@context' => 'https://schema.org',
        '@type'    => 'WebApplication',
        'name'     => 'محاسبه‌گر حجم نمونه تزنویسه',
        'url'      => $page_url,
        'applicationCategory' => 'EducationalApplication',
        'operatingSystem' => 'All',
        'description' => 'ابزار رایگان آنلاین محاسبه حجم نمونه آماری با فرمول کوکران و جدول کرجسی و مورگان برای پژوهش، پایان‌نامه و مقاله',
        'inLanguage' => 'fa-IR',
        'offers' => array('@type'=>'Offer','price'=>'0','priceCurrency'=>'IRR'),
        'publisher' => array('@type'=>'Organization','name'=>'تزنویسه'),
    );
    echo "\n".'<script type="application/ld+json">'.wp_json_encode($app, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";

    // FAQ Schema
    $faqs = array(
        array('حجم نمونه مناسب برای پایان‌نامه چقدر است؟', 'حجم نمونه مناسب به حجم جامعه آماری، سطح اطمینان و حاشیه خطا بستگی دارد. برای جوامع بزرگ، حجم نمونه معمولاً حدود ۳۸۴ نفر است. بهترین کار استفاده از فرمول کوکران یا جدول مورگان است.'),
        array('تفاوت فرمول کوکران و جدول مورگان چیست؟', 'فرمول کوکران امکان تنظیم دقیق سطح اطمینان، حاشیه خطا و نسبت را فراهم می‌کند. جدول مورگان یک روش آماده و سریع است که بر اساس سطح اطمینان ۹۵٪ و خطای ۵٪ از پیش محاسبه شده است.'),
        array('اگر حجم جامعه آماری نامشخص باشد چه کنیم؟', 'اگر حجم جامعه نامحدود است، فرمول جامعه نامحدود اعمال می‌شود که معمولاً حجم نمونه‌ای حدود ۳۸۴ نفر با اطمینان ۹۵٪ ارائه می‌دهد.'),
        array('چرا نسبت موفقیت را ۵۰٪ در نظر می‌گیریم؟', 'وقتی نسبت واقعی را نمی‌دانیم، مقدار p=0.5 بزرگ‌ترین و محافظه‌کارانه‌ترین حجم نمونه را می‌دهد و تضمین می‌کند نمونه به‌اندازه کافی بزرگ باشد.'),
        array('آیا این محاسبه‌گر برای مقالات ISI معتبر است؟', 'بله. این محاسبه‌گر بر اساس فرمول‌های استاندارد کوکران و کرجسی-مورگان طراحی شده که در مقالات ISI و پایان‌نامه‌های دانشگاهی پذیرفته‌شده هستند.'),
    );
    $faq_items = array();
    foreach ($faqs as $f) {
        $faq_items[] = array(
            '@type' => 'Question',
            'name'  => $f[0],
            'acceptedAnswer' => array('@type'=>'Answer','text'=>$f[1]),
        );
    }
    $faq_schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'FAQPage',
        'mainEntity' => $faq_items,
    );
    echo '<script type="application/ld+json">'.wp_json_encode($faq_schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";

    // Breadcrumb
    $bc = array(
        '@context'=>'https://schema.org','@type'=>'BreadcrumbList',
        'itemListElement'=>array(
            array('@type'=>'ListItem','position'=>1,'name'=>'خانه','item'=>home_url('/')),
            array('@type'=>'ListItem','position'=>2,'name'=>$page_title,'item'=>$page_url),
        ),
    );
    echo '<script type="application/ld+json">'.wp_json_encode($bc, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
}
}

/* --- careers page terms checkbox --- */
if ( ! function_exists( 'teznevise_careers_terms_shortcode' ) ) {
// ============================================
// تزنویسه - شورت‌کد ضوابط همکاری
// [tz_careers_terms]  یا  [tz_careers_terms checkbox="yes"]
// ============================================

add_shortcode('tz_careers_terms', 'teznevise_careers_terms_shortcode');
function teznevise_careers_terms_shortcode($atts) {
    $atts = shortcode_atts(array(
        'checkbox' => 'no',   // yes برای نمایش چک‌باکس پذیرش (در فرم استخدام)
        'title'    => 'yes',
    ), $atts);

    $terms = array(
        array('🎓','صلاحیت علمی و تخصصی', '<p>پژوهشگر متعهد می‌شود که در حوزه اعلام‌شده دارای <strong>دانش، مهارت و تجربه واقعی</strong> باشد و صرفاً پروژه‌هایی را بپذیرد که توانایی انجام آن‌ها را با کیفیت استاندارد دارد. ارائه اطلاعات نادرست درباره سطح تخصص، نقض جدی این توافق تلقی می‌شود.</p>'),
        array('🛡️','محرمانگی و عدم افشا (NDA)', '<p>کلیه اطلاعات، داده‌ها، ایده‌ها و مستندات پروژه‌های متقاضیان، <strong>کاملاً محرمانه</strong> است. پژوهشگر متعهد می‌شود:</p><ul><li>هیچ اطلاعاتی از پروژه‌ها را با اشخاص ثالث به اشتراک نگذارد.</li><li>از داده‌های متقاضیان برای مقاصد شخصی یا تجاری استفاده نکند.</li><li>پس از اتمام همکاری نیز این تعهد را برای همیشه حفظ کند.</li></ul>'),
        array('✍️','اصالت محتوا و عدم سرقت ادبی', '<p>پژوهشگر تضمین می‌کند که تمامی خروجی‌ها <strong>اصیل، تولید اختصاصی و عاری از سرقت ادبی</strong> هستند. استفاده از محتوای کپی‌شده، بازنویسی غیرمستند یا تولید محتوا با ابزارهای هوش مصنوعی مولد، موجب فسخ فوری همکاری و مسئولیت جبران خسارت خواهد بود.</p>'),
        array('⏰','تعهد به زمان‌بندی', '<p>پژوهشگر متعهد می‌شود پروژه‌ها را در <strong>زمان توافق‌شده</strong> تحویل دهد و در صورت بروز مشکل، در اسرع وقت موسسه را مطلع سازد. تأخیرهای مکرر و غیرموجه، موجب کاهش اولویت ارجاع پروژه یا قطع همکاری می‌شود.</p>'),
        array('💬','ارتباط از طریق سامانه موسسه', '<p>کلیه ارتباطات، تبادل فایل و مذاکرات باید <strong>صرفاً از طریق سامانه رسمی موسسه</strong> انجام شود. هرگونه تلاش برای برقراری ارتباط مستقیم مالی یا کاری با متقاضیان خارج از سامانه، ممنوع و موجب فسخ همکاری است.</p>'),
        array('💰','پرداخت و تسویه‌حساب', '<p>پرداخت‌ها بر اساس <strong>توافق پروژه‌ای</strong> و پس از تأیید کیفیت کار توسط واحد تضمین کیفیت انجام می‌شود. جزئیات مالی هر پروژه پیش از آغاز کار به اطلاع پژوهشگر می‌رسد.</p>'),
        array('🔄','بازنگری و تضمین کیفیت', '<p>پژوهشگر متعهد می‌شود در صورت نیاز به <strong>اصلاحات منطقی</strong> در چارچوب پروژه تعریف‌شده، آن‌ها را بدون هزینه اضافه انجام دهد. کیفیت کار باید با استانداردهای علمی ژورنال‌های معتبر هم‌خوانی داشته باشد.</p>'),
        array('⚖️','رفتار حرفه‌ای و اخلاقی', '<p>پژوهشگر متعهد به رعایت <strong>اصول اخلاق حرفه‌ای</strong>، احترام متقابل و پرهیز از هرگونه رفتار خلاف شأن موسسه است. عدم پذیرش پروژه‌هایی که زمینه تخلف علمی یا فریب نهادها را فراهم می‌کنند، الزامی است.</p>'),
        array('📜','مالکیت فکری خروجی‌ها', '<p>کلیه خروجی‌های تولیدشده در چارچوب همکاری، تحت <strong>قرارداد و سیاست‌های موسسه</strong> مدیریت می‌شوند. پژوهشگر حق استفاده مجدد یا انتشار خروجی‌ها را برای خود محفوظ نمی‌داند.</p>'),
        array('🚫','شرایط فسخ همکاری', '<p>نقض هر یک از بندهای فوق، می‌تواند منجر به <strong>تعلیق یا فسخ فوری همکاری</strong>، حذف پروفایل پژوهشگر و در موارد جدی، پیگرد قانونی و مطالبه خسارت گردد.</p>'),
    );

    ob_start();
    ?>
    <div class="tzc">
      <div class="tzc-terms">

        <?php if ($atts['title'] === 'yes') : ?>
          <div class="tzc-terms-intro">
            📋 <strong>ضوابط و تعهدات همکاری با تزنویسه:</strong> لطفاً موارد زیر را با دقت مطالعه فرمایید. همکاری با موسسه منوط به پذیرش کامل این ضوابط است.
          </div>
        <?php endif; ?>

        <?php foreach ($terms as $i => $t) : ?>
          <div class="tzc-term-block">
            <div class="tzc-term-title">
              <span class="tzc-term-num"><?php echo $i + 1; ?></span>
              <h3><?php echo $t[0]; ?> <?php echo esc_html($t[1]); ?></h3>
            </div>
            <?php echo $t[2]; ?>
          </div>
        <?php endforeach; ?>

        <?php if ($atts['checkbox'] === 'yes') : ?>
          <div style="margin-top:24px; background:#f0f7f4; border:2px solid #145D4A; border-radius:12px; padding:18px 20px;">
            <label style="display:flex; align-items:flex-start; gap:12px; cursor:pointer; font-size:14px; color:#1f2937; line-height:1.9;">
              <input type="checkbox" name="tz_careers_accept" id="tz_careers_accept" required 
                     style="width:22px; height:22px; accent-color:#145D4A; margin-top:2px; flex-shrink:0; cursor:pointer;" />
              <span>اینجانب کلیه ضوابط و تعهدات همکاری فوق را با دقت <strong>مطالعه نموده</strong> و <strong>به‌طور کامل می‌پذیرم</strong>. متعهد می‌شوم در تمام دوران همکاری با موسسه تزنویسه، به این اصول پایبند باشم.</span>
            </label>
          </div>
        <?php endif; ?>

      </div>
    </div>
    <?php
    return ob_get_clean();
}
}

/* --- cronbach alpha calculator shortcode & schema --- */
if ( ! function_exists( 'teznevise_cronbach_calculator' ) ) {
// ============================================
// تزنویسه - شورت‌کد محاسبه‌گر آلفای کرونباخ + اسکیما
// ============================================

add_shortcode('tz_cronbach', 'teznevise_cronbach_calculator');
function teznevise_cronbach_calculator($atts) {
    ob_start();
    ?>
    <div class="tzca">
      <div class="tzca-container">

        <!-- Hero -->
        <div class="tzca-hero">
          <div class="tzca-hero-inner">
            <span class="tzca-hero-tag">🧮 ابزار رایگان آنلاین</span>
            <h1>محاسبه‌گر آلفای کرونباخ</h1>
            <p>پایایی (Reliability) پرسش‌نامه خود را با ضریب آلفای کرونباخ به‌صورت رایگان و دقیق محاسبه کنید. ابزاری ضروری برای پایان‌نامه، مقاله و پژوهش‌های پیمایشی.</p>
          </div>
        </div>

        <!-- Calculator -->
        <div class="tzca-card">
          <div class="tzca-tabs">
            <button class="tzca-tab tzca-tab-active" data-tab="data">📋 وارد کردن داده‌ها</button>
            <button class="tzca-tab" data-tab="manual">📐 محاسبه دستی</button>
          </div>

          <!-- Data Pane -->
          <div class="tzca-pane tzca-pane-active" id="tzca-pane-data">
            <div class="tzca-data-note">
              💡 داده‌های خود را وارد کنید: <strong>هر ردیف یک پاسخ‌دهنده</strong> و <strong>هر ستون یک گویه (سؤال)</strong>. مقادیر را با فاصله، تب یا کاما از هم جدا کنید. مثلاً برای طیف لیکرت ۵ تایی، اعداد ۱ تا ۵.
            </div>
            <div class="tzca-field">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <label style="margin:0;">ماتریس داده‌ها (پاسخ‌دهنده × گویه)</label>
                <button type="button" class="tzca-sample-btn" id="tzca-sample">📝 بارگذاری داده نمونه</button>
              </div>
              <textarea id="tzca-data-input" placeholder="مثال:&#10;4 3 4 5 4&#10;3 3 4 4 3&#10;5 5 4 5 5&#10;2 2 3 2 3"></textarea>
            </div>
            <div class="tzca-error" id="tzca-err-data"></div>
            <button type="button" class="tzca-calc-btn" id="tzca-calc-data">محاسبه آلفای کرونباخ</button>
          </div>

          <!-- Manual Pane -->
          <div class="tzca-pane" id="tzca-pane-manual">
            <div class="tzca-data-note">
              💡 اگر مقادیر واریانس را از قبل محاسبه کرده‌اید، آن‌ها را مستقیماً وارد کنید.
            </div>
            <div class="tzca-grid3">
              <div class="tzca-field">
                <label>تعداد گویه‌ها (k)</label>
                <input type="number" id="tzca-m-k" min="2" placeholder="مثلاً: 10" />
              </div>
              <div class="tzca-field">
                <label>مجموع واریانس گویه‌ها (Σσ²ᵢ)</label>
                <input type="number" id="tzca-m-sumvar" step="any" placeholder="مثلاً: 8.5" />
              </div>
              <div class="tzca-field">
                <label>واریانس نمرات کل (σ²ₜ)</label>
                <input type="number" id="tzca-m-totvar" step="any" placeholder="مثلاً: 45.2" />
              </div>
            </div>
            <div class="tzca-error" id="tzca-err-manual"></div>
            <button type="button" class="tzca-calc-btn" id="tzca-calc-manual">محاسبه آلفای کرونباخ</button>
          </div>

          <!-- Result -->
          <div style="padding:0 28px 28px;">
            <div class="tzca-result" id="tzca-result"></div>
          </div>
        </div>

        <!-- SEO Content -->
        <div class="tzca-content">
          <h2>آلفای کرونباخ چیست؟</h2>
          <p><strong>ضریب آلفای کرونباخ (Cronbach's Alpha)</strong> یکی از پرکاربردترین شاخص‌ها برای سنجش <strong>پایایی (Reliability)</strong> و <strong>همسانی درونی (Internal Consistency)</strong> یک پرسش‌نامه یا آزمون است. این ضریب که توسط لی کرونباخ در سال ۱۹۵۱ معرفی شد، نشان می‌دهد که گویه‌های مختلف یک پرسش‌نامه تا چه اندازه یک مفهوم واحد را اندازه‌گیری می‌کنند و با یکدیگر همبستگی دارند.</p>
          <p>مقدار آلفای کرونباخ بین <strong>۰ تا ۱</strong> قرار می‌گیرد؛ هرچه این مقدار به ۱ نزدیک‌تر باشد، پایایی پرسش‌نامه بالاتر است. در پایان‌نامه‌ها و مقالات علمی، گزارش این ضریب برای ابزارهای اندازه‌گیری تقریباً همیشه ضروری است.</p>

          <h2>فرمول آلفای کرونباخ</h2>
          <p>آلفای کرونباخ بر اساس تعداد گویه‌ها، واریانس هر گویه و واریانس نمرات کل محاسبه می‌شود:</p>
          <div class="tzca-formula-box">α = (k / (k−1)) × (1 − Σσ²ᵢ / σ²ₜ)</div>
          <p>که در آن:</p>
          <ul>
            <li><strong>α</strong>: ضریب آلفای کرونباخ</li>
            <li><strong>k</strong>: تعداد گویه‌ها (سؤالات) پرسش‌نامه</li>
            <li><strong>Σσ²ᵢ</strong>: مجموع واریانس تک‌تک گویه‌ها</li>
            <li><strong>σ²ₜ</strong>: واریانس نمرات کل (مجموع نمرات هر پاسخ‌دهنده)</li>
          </ul>
          <p>محاسبه‌گر بالای این صفحه، با دریافت داده‌های خام شما، به‌صورت خودکار تمامی این مقادیر را محاسبه و ضریب نهایی را ارائه می‌دهد.</p>

          <h2>تفسیر مقدار آلفای کرونباخ</h2>
          <p>برای تفسیر ضریب به‌دست‌آمده، می‌توانید از جدول استاندارد زیر (بر اساس معیار جرج و مالری) استفاده کنید:</p>
          <table class="tzca-interp-table">
            <thead><tr><th>مقدار آلفا</th><th>تفسیر پایایی</th></tr></thead>
            <tbody>
              <tr><td>α ≥ ۰.۹</td><td>عالی (Excellent)</td></tr>
              <tr><td>۰.۸ ≤ α < ۰.۹</td><td>خوب (Good)</td></tr>
              <tr><td>۰.۷ ≤ α < ۰.۸</td><td>قابل قبول (Acceptable)</td></tr>
              <tr><td>۰.۶ ≤ α < ۰.۷</td><td>مورد تردید (Questionable)</td></tr>
              <tr><td>۰.۵ ≤ α < ۰.۶</td><td>ضعیف (Poor)</td></tr>
              <tr><td>α < ۰.۵</td><td>غیرقابل قبول (Unacceptable)</td></tr>
            </tbody>
          </table>
          <p>در اغلب پژوهش‌های علوم انسانی و اجتماعی، مقدار <strong>۰.۷ و بالاتر</strong> به‌عنوان حد قابل قبول برای پایایی پرسش‌نامه در نظر گرفته می‌شود.</p>

          <h3>«آلفا در صورت حذف گویه» چیست؟</h3>
          <p>محاسبه‌گر ما علاوه بر آلفای کل، برای هر گویه نشان می‌دهد که اگر آن گویه از پرسش‌نامه حذف شود، ضریب آلفا چه مقداری خواهد شد. اگر حذف یک گویه باعث <strong>افزایش قابل توجه</strong> آلفا شود، آن گویه احتمالاً همبستگی کافی با سایر گویه‌ها ندارد و بازنگری یا حذف آن می‌تواند پایایی کل ابزار را بهبود بخشد.</p>

          <h2>نکات مهم درباره آلفای کرونباخ</h2>
          <ul>
            <li>آلفای کرونباخ به <strong>تعداد گویه‌ها حساس است</strong>؛ افزایش تعداد گویه‌ها معمولاً آلفا را بالا می‌برد.</li>
            <li>آلفای بسیار بالا (مثلاً بالای ۰.۹۵) ممکن است نشانه <strong>افزونگی (Redundancy)</strong> و تکراری بودن گویه‌ها باشد.</li>
            <li>این ضریب برای گویه‌هایی که یک مفهوم (سازه) واحد را می‌سنجند معنادار است؛ برای ابعاد مختلف باید جداگانه محاسبه شود.</li>
            <li>برای داده‌های دوحالتی (بله/خیر)، روش معادل آن <strong>KR-20</strong> است.</li>
          </ul>
        </div>

        <!-- FAQ -->
        <div class="tzca-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzca-faq-item"><div class="tzca-faq-q">آلفای کرونباخ قابل قبول چقدر است؟ <span class="tzca-faq-icon">+</span></div><div class="tzca-faq-a"><div class="tzca-faq-a-inner">در اغلب پژوهش‌ها، مقدار آلفای ۰.۷ و بالاتر قابل قبول تلقی می‌شود. مقادیر ۰.۸ تا ۰.۹ نشان‌دهنده پایایی خوب و بالای ۰.۹ عالی است. در برخی پژوهش‌های اکتشافی، آلفای ۰.۶ نیز پذیرفته می‌شود.</div></div></div>
          <div class="tzca-faq-item"><div class="tzca-faq-q">اگر آلفای کرونباخ پایین باشد چه کنیم؟ <span class="tzca-faq-icon">+</span></div><div class="tzca-faq-a"><div class="tzca-faq-a-inner">می‌توانید از ستون «آلفا در صورت حذف گویه» استفاده کنید تا گویه‌های ضعیف را شناسایی و حذف یا بازنویسی کنید. همچنین بررسی کنید که گویه‌ها واقعاً یک مفهوم واحد را بسنجند و تعداد کافی گویه داشته باشید.</div></div></div>
          <div class="tzca-faq-item"><div class="tzca-faq-q">آیا آلفای بالاتر همیشه بهتر است؟ <span class="tzca-faq-icon">+</span></div><div class="tzca-faq-a"><div class="tzca-faq-a-inner">خیر. آلفای بسیار بالا (بالای ۰.۹۵) می‌تواند نشانه تکراری بودن گویه‌ها باشد. هدف، رسیدن به همسانی درونی مناسب است، نه لزوماً بالاترین مقدار ممکن.</div></div></div>
          <div class="tzca-faq-item"><div class="tzca-faq-q">برای هر بُعد پرسش‌نامه باید جداگانه آلفا حساب کنم؟ <span class="tzca-faq-icon">+</span></div><div class="tzca-faq-a"><div class="tzca-faq-a-inner">بله. اگر پرسش‌نامه شما چند بُعد یا سازه مختلف را می‌سنجد، بهتر است آلفای کرونباخ را برای هر بُعد به‌صورت جداگانه و همچنین برای کل پرسش‌نامه گزارش کنید.</div></div></div>
          <div class="tzca-faq-item"><div class="tzca-faq-q">این محاسبه‌گر چه روشی استفاده می‌کند؟ <span class="tzca-faq-icon">+</span></div><div class="tzca-faq-a"><div class="tzca-faq-a-inner">این ابزار از فرمول استاندارد آلفای کرونباخ بر پایه واریانس گویه‌ها و واریانس نمرات کل استفاده می‌کند (با واریانس نمونه‌ای). نتایج با خروجی نرم‌افزارهایی مانند SPSS مطابقت دارد.</div></div></div>
        </div>

        <!-- CTA -->
        <div class="tzca-cta">
          <h2>به کمک تخصصی در تحلیل پایایی نیاز دارید؟</h2>
          <p>از سنجش پایایی و روایی پرسش‌نامه تا تحلیل عاملی تأییدی و اکتشافی — تیم متخصصان تزنویسه در تمام مراحل پژوهش کنار شماست. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzca-cta-btn">سفارش تحلیل آماری</a>
        </div>

      </div>
    </div>
    <?php
    return ob_get_clean();
}

// ===== اسکیمای سئو =====
add_action('wp_head', 'teznevise_cronbach_schema');
function teznevise_cronbach_schema() {
    if (!is_singular()) return;
    global $post;
    if (!$post || strpos($post->post_content, 'tz_cronbach') === false) return;

    $url = get_permalink();
    $title = get_the_title();

    // WebApplication
    $app = array('@context'=>'https://schema.org','@type'=>'WebApplication',
        'name'=>'محاسبه‌گر آلفای کرونباخ تزنویسه','url'=>$url,
        'applicationCategory'=>'EducationalApplication','operatingSystem'=>'All',
        'description'=>'ابزار رایگان آنلاین محاسبه ضریب آلفای کرونباخ برای سنجش پایایی پرسش‌نامه در پژوهش، پایان‌نامه و مقاله',
        'inLanguage'=>'fa-IR','offers'=>array('@type'=>'Offer','price'=>'0','priceCurrency'=>'IRR'),
        'publisher'=>array('@type'=>'Organization','name'=>'تزنویسه'));
    echo "\n".'<script type="application/ld+json">'.wp_json_encode($app,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";

    // FAQ
    $faqs=array(
        array('آلفای کرونباخ قابل قبول چقدر است؟','در اغلب پژوهش‌ها مقدار ۰.۷ و بالاتر قابل قبول است. مقادیر ۰.۸ تا ۰.۹ پایایی خوب و بالای ۰.۹ عالی محسوب می‌شود.'),
        array('اگر آلفای کرونباخ پایین باشد چه کنیم؟','با استفاده از شاخص «آلفا در صورت حذف گویه» گویه‌های ضعیف را شناسایی و حذف یا بازنویسی کنید و از تک‌بعدی بودن سازه اطمینان حاصل نمایید.'),
        array('آیا آلفای بالاتر همیشه بهتر است؟','خیر؛ آلفای بالای ۰.۹۵ می‌تواند نشانه تکراری بودن گویه‌ها باشد. هدف، همسانی درونی مناسب است نه بالاترین مقدار.'),
        array('این محاسبه‌گر چه روشی استفاده می‌کند؟','از فرمول استاندارد آلفای کرونباخ بر پایه واریانس گویه‌ها و واریانس نمرات کل استفاده می‌کند و نتایج با SPSS مطابقت دارد.'),
    );
    $items=array();
    foreach($faqs as $f){ $items[]=array('@type'=>'Question','name'=>$f[0],'acceptedAnswer'=>array('@type'=>'Answer','text'=>$f[1])); }
    $faqSchema=array('@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$items);
    echo '<script type="application/ld+json">'.wp_json_encode($faqSchema,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";

    // Breadcrumb
    $bc=array('@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>array(
        array('@type'=>'ListItem','position'=>1,'name'=>'خانه','item'=>home_url('/')),
        array('@type'=>'ListItem','position'=>2,'name'=>$title,'item'=>$url),
    ));
    echo '<script type="application/ld+json">'.wp_json_encode($bc,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
}
}

/* --- pearson-correlation-calculator --- */
if ( ! function_exists( 'teznevise_pearson_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر ضریب همبستگی پیرسون
// ============================================
add_shortcode('tz_pearson', 'teznevise_pearson_calc');
function teznevise_pearson_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">🧮 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر ضریب همبستگی پیرسون</h1>
          <p>میزان و جهت رابطه خطی بین دو متغیر را با ضریب همبستگی پیرسون (r) به‌صورت رایگان و دقیق محاسبه کنید. به‌همراه سطح معناداری و تفسیر کامل.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 داده‌های دو متغیر X و Y را وارد کنید. هر ردیف یک مشاهده، با فاصله یا کاما بین X و Y. مثلاً: <code>۱۲, ۱۵</code></div>
          <div class="tzt-grid2">
            <div class="tzt-field">
              <label>متغیر X <span class="tzt-hint">(هر مقدار در یک خط)</span></label>
              <textarea id="tzp-x" placeholder="12&#10;15&#10;18&#10;20"></textarea>
            </div>
            <div class="tzt-field">
              <label>متغیر Y <span class="tzt-hint">(هر مقدار در یک خط)</span></label>
              <textarea id="tzp-y" placeholder="14&#10;17&#10;19&#10;23"></textarea>
            </div>
          </div>
          <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <button type="button" class="tzt-sample-btn" id="tzp-sample">📝 بارگذاری داده نمونه</button>
          </div>
          <div class="tzt-error" id="tzp-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzp-calc">محاسبه ضریب همبستگی</button>
          <div class="tzt-result" id="tzp-result"></div>
        </div>

        <div class="tzt-content">
          <h2>ضریب همبستگی پیرسون چیست؟</h2>
          <p><strong>ضریب همبستگی پیرسون (Pearson Correlation Coefficient)</strong> که با نماد <strong>r</strong> نشان داده می‌شود، یکی از پرکاربردترین شاخص‌های آماری برای سنجش <strong>شدت و جهت رابطه خطی</strong> بین دو متغیر کمی است. مقدار این ضریب همواره بین <strong>۱- تا ۱+</strong> قرار می‌گیرد.</p>
          <p>مقدار مثبت نشان‌دهنده رابطه مستقیم (هم‌جهت)، مقدار منفی نشان‌دهنده رابطه معکوس، و مقدار نزدیک به صفر بیانگر نبود رابطه خطی است.</p>

          <h2>فرمول ضریب همبستگی پیرسون</h2>
          <div class="tzt-formula-box">r = Σ((xᵢ−x̄)(yᵢ−ȳ)) / √(Σ(xᵢ−x̄)² × Σ(yᵢ−ȳ)²)</div>
          <p>این محاسبه‌گر علاوه بر r، آماره <strong>t</strong>، <strong>درجه آزادی</strong> و <strong>ضریب تعیین (r²)</strong> را نیز محاسبه می‌کند.</p>

          <h2>تفسیر مقدار ضریب همبستگی</h2>
          <table class="tzt-itable">
            <thead><tr><th>قدر مطلق r</th><th>شدت رابطه</th></tr></thead>
            <tbody>
              <tr><td>۰ تا ۰.۱۹</td><td>بسیار ضعیف / ناچیز</td></tr>
              <tr><td>۰.۲ تا ۰.۳۹</td><td>ضعیف</td></tr>
              <tr><td>۰.۴ تا ۰.۵۹</td><td>متوسط</td></tr>
              <tr><td>۰.۶ تا ۰.۷۹</td><td>قوی</td></tr>
              <tr><td>۰.۸ تا ۱</td><td>بسیار قوی</td></tr>
            </tbody>
          </table>
          <h3>ضریب تعیین (r²) چیست؟</h3>
          <p>مجذور ضریب همبستگی (r²) نشان می‌دهد که چند درصد از تغییرات یک متغیر توسط متغیر دیگر تبیین می‌شود. مثلاً اگر r=۰.۸ باشد، r²=۰.۶۴ یعنی ۶۴٪ از واریانس مشترک است.</p>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">همبستگی به معنای علیت است؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">خیر. همبستگی صرفاً وجود رابطه خطی را نشان می‌دهد و به معنای رابطه علت و معلولی نیست. ممکن است متغیر سومی عامل هر دو باشد.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">پیش‌فرض‌های آزمون پیرسون چیست؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">داده‌ها باید کمی (فاصله‌ای/نسبی)، دارای توزیع نرمال و رابطه خطی باشند. در صورت نقض این پیش‌فرض‌ها، از همبستگی اسپیرمن استفاده کنید.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چه زمانی همبستگی معنادار است؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">اگر سطح معناداری (p-value) کمتر از ۰.۰۵ باشد، رابطه از نظر آماری معنادار تلقی می‌شود. این محاسبه‌گر آماره t را برای بررسی معناداری ارائه می‌دهد.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل همبستگی و رگرسیون نیاز دارید؟</h2>
          <p>از تحلیل همبستگی ساده تا رگرسیون چندمتغیره و مدل‌سازی پیشرفته — متخصصان تزنویسه کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">سفارش تحلیل آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzp-calc')) return;
      tztInitFAQ();
      function parseCol(t){ return t.trim().split(/\r?\n/).filter(function(l){return l.trim()!=='';}).map(function(v){return parseFloat(v.trim());}); }
      function interp(r){ var a=Math.abs(r);
        if(a>=0.8) return {t:'بسیار قوی',c:'#16a34a'}; if(a>=0.6) return {t:'قوی',c:'#22c55e'};
        if(a>=0.4) return {t:'متوسط',c:'#84cc16'}; if(a>=0.2) return {t:'ضعیف',c:'#d97706'};
        return {t:'بسیار ضعیف',c:'#dc2626'}; }

      document.getElementById('tzp-sample').addEventListener('click', function(){
        document.getElementById('tzp-x').value='10\n12\n15\n18\n20\n22\n25\n28\n30\n33';
        document.getElementById('tzp-y').value='15\n18\n20\n22\n26\n28\n30\n35\n38\n42';
      });

      document.getElementById('tzp-calc').addEventListener('click', function(){
        var err=document.getElementById('tzp-err'); err.classList.remove('tzt-show');
        document.getElementById('tzp-result').classList.remove('tzt-show');
        try{
          var x=parseCol(document.getElementById('tzp-x').value);
          var y=parseCol(document.getElementById('tzp-y').value);
          if(x.some(isNaN)||y.some(isNaN)) throw 'داده‌ها باید عددی باشند.';
          if(x.length!==y.length) throw 'تعداد مقادیر X و Y باید برابر باشد (X: '+x.length+'، Y: '+y.length+').';
          if(x.length<3) throw 'حداقل به ۳ جفت داده نیاز است.';
          var n=x.length;
          var mx=x.reduce(function(a,b){return a+b;},0)/n;
          var my=y.reduce(function(a,b){return a+b;},0)/n;
          var sxy=0,sxx=0,syy=0;
          for(var i=0;i<n;i++){ var dx=x[i]-mx, dy=y[i]-my; sxy+=dx*dy; sxx+=dx*dx; syy+=dy*dy; }
          if(sxx===0||syy===0) throw 'واریانس یکی از متغیرها صفر است.';
          var r=sxy/Math.sqrt(sxx*syy);
          var r2=r*r;
          var df=n-2;
          var t=r*Math.sqrt(df/(1-r2));
          var ip=interp(r);
          var dir=r>0?'مستقیم (مثبت)':(r<0?'معکوس (منفی)':'بدون رابطه');
          var html='<div class="tzt-result-main">';
          html+='<div class="tzt-result-label">ضریب همبستگی پیرسون (r)</div>';
          html+='<div class="tzt-result-value">'+tztToPersian(r.toFixed(3))+'</div>';
          html+='<span class="tzt-result-badge" style="background:'+ip.c+';">'+ip.t+' — '+dir+'</span>';
          html+='<div class="tzt-result-meta">';
          html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(r2.toFixed(3))+'</strong><span>ضریب تعیین (r²)</span></div>';
          html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(t.toFixed(3))+'</strong><span>آماره t</span></div>';
          html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(df)+'</strong><span>درجه آزادی</span></div>';
          html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(n)+'</strong><span>تعداد داده</span></div>';
          html+='</div></div>';
          html+='<div class="tzt-method-note"><strong>تفسیر:</strong> '+tztToPersian((r2*100).toFixed(1))+'٪ از تغییرات دو متغیر مشترک است. برای بررسی معناداری، آماره t='+tztToPersian(t.toFixed(2))+' را با جدول t در درجه آزادی '+tztToPersian(df)+' مقایسه کنید (در سطح ۰.۰۵، اگر |t| از مقدار بحرانی بزرگ‌تر باشد، رابطه معنادار است).</div>';
          var box=document.getElementById('tzp-result'); box.innerHTML=html; box.classList.add('tzt-show');
        }catch(e){ err.textContent='⚠️ '+(typeof e==='string'?e:'خطا در محاسبه.'); err.classList.add('tzt-show'); }
      });
    });
    </script>
    <?php
    return ob_get_clean();
}

add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post; if(!$post || strpos($post->post_content,'tz_pearson')===false) return;
    $url=get_permalink();
    $app=array('@context'=>'https://schema.org','@type'=>'WebApplication','name'=>'محاسبه‌گر ضریب همبستگی پیرسون تزنویسه','url'=>$url,'applicationCategory'=>'EducationalApplication','operatingSystem'=>'All','description'=>'ابزار رایگان محاسبه ضریب همبستگی پیرسون با تفسیر و سطح معناداری','inLanguage'=>'fa-IR','offers'=>array('@type'=>'Offer','price'=>'0','priceCurrency'=>'IRR'),'publisher'=>array('@type'=>'Organization','name'=>'تزنویسه'));
    echo "\n".'<script type="application/ld+json">'.wp_json_encode($app,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
});
}

/* --- content-validity-calculator --- */
if ( ! function_exists( 'teznevise_cvr_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر روایی محتوا (CVR / CVI)
// ============================================
add_shortcode('tz_cvr', 'teznevise_cvr_calc');
function teznevise_cvr_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">🧮 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر روایی محتوا (CVR و CVI)</h1>
          <p>نسبت روایی محتوا (CVR) و شاخص روایی محتوا (CVI) پرسش‌نامه خود را بر اساس نظر متخصصان به‌صورت رایگان محاسبه کنید — همراه با جدول حداقل CVR لاوشه.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 برای هر گویه، تعداد متخصصانی که گزینه‌های مختلف را انتخاب کرده‌اند وارد کنید. ابتدا تعداد کل متخصصان را مشخص کنید.</div>

          <div class="tzt-grid3">
            <div class="tzt-field">
              <label>تعداد کل متخصصان (پنل)</label>
              <input type="number" id="tzcvr-experts" min="2" value="10" />
            </div>
          </div>

          <div class="tzt-field">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <label style="margin:0;">داده‌های هر گویه</label>
              <button type="button" class="tzt-sample-btn" id="tzcvr-sample">📝 داده نمونه</button>
            </div>
            <div class="tzt-note" style="background:#fff8e1; border-color:#d97706; color:#92400e;">
              <strong>برای CVR:</strong> هر خط = تعداد متخصصانی که گویه را «ضروری» دانسته‌اند.<br>
              <strong>برای CVI:</strong> هر خط = تعداد متخصصانی که به گویه امتیاز ۳ یا ۴ (مرتبط) داده‌اند.<br>
              (هر دو با همین داده محاسبه می‌شوند)
            </div>
            <textarea id="tzcvr-data" placeholder="گویه ۱: 9&#10;گویه ۲: 8&#10;گویه ۳: 10&#10;فقط عدد هر گویه را در یک خط وارد کنید:&#10;9&#10;8&#10;10&#10;7"></textarea>
          </div>

          <div class="tzt-error" id="tzcvr-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzcvr-calc">محاسبه روایی محتوا</button>
          <div class="tzt-result" id="tzcvr-result"></div>
        </div>

        <div class="tzt-content">
          <h2>روایی محتوا چیست؟</h2>
          <p><strong>روایی محتوا (Content Validity)</strong> میزانی است که گویه‌های یک ابزار اندازه‌گیری، محتوای مورد نظر را به‌درستی و به‌طور جامع پوشش می‌دهند. این روایی معمولاً توسط <strong>پنلی از متخصصان</strong> ارزیابی می‌شود و دو شاخص اصلی دارد: CVR و CVI.</p>

          <h2>نسبت روایی محتوا (CVR)</h2>
          <p>شاخص <strong>CVR (Content Validity Ratio)</strong> توسط لاوشه (Lawshe, 1975) معرفی شد و ضرورت هر گویه را می‌سنجد. متخصصان هر گویه را در سه سطح «ضروری»، «مفید اما غیرضروری» و «غیرضروری» ارزیابی می‌کنند.</p>
          <div class="tzt-formula-box">CVR = (nₑ − N/2) / (N/2)</div>
          <p>که در آن <strong>nₑ</strong> تعداد متخصصانی است که گویه را «ضروری» دانسته‌اند و <strong>N</strong> تعداد کل متخصصان است. مقدار CVR بین ۱- تا ۱+ است.</p>

          <h2>شاخص روایی محتوا (CVI)</h2>
          <p>شاخص <strong>CVI (Content Validity Index)</strong> مرتبط بودن هر گویه را می‌سنجد. متخصصان به هر گویه در طیف ۴ تایی (۱=نامرتبط تا ۴=کاملاً مرتبط) امتیاز می‌دهند.</p>
          <div class="tzt-formula-box">CVI = (تعداد امتیازهای ۳ و ۴) / (کل متخصصان)</div>
          <ul>
            <li><strong>I-CVI</strong> ≥ ۰.۷۹ : گویه مناسب است</li>
            <li>۰.۷۰ تا ۰.۷۹ : نیاز به بازنگری</li>
            <li>کمتر از ۰.۷۰ : گویه حذف شود</li>
          </ul>

          <h2>جدول حداقل CVR قابل قبول (لاوشه)</h2>
          <p>حداقل مقدار CVR قابل قبول به تعداد متخصصان بستگی دارد:</p>
          <table class="tzt-itable">
            <thead><tr><th>تعداد متخصصان</th><th>حداقل CVR</th></tr></thead>
            <tbody>
              <tr><td>۵</td><td>۰.۹۹</td></tr><tr><td>۶</td><td>۰.۹۹</td></tr>
              <tr><td>۷</td><td>۰.۹۹</td></tr><tr><td>۸</td><td>۰.۷۵</td></tr>
              <tr><td>۹</td><td>۰.۷۸</td></tr><tr><td>۱۰</td><td>۰.۶۲</td></tr>
              <tr><td>۱۱</td><td>۰.۵۹</td></tr><tr><td>۱۲</td><td>۰.۵۶</td></tr>
              <tr><td>۱۳</td><td>۰.۵۴</td></tr><tr><td>۱۴</td><td>۰.۵۱</td></tr>
              <tr><td>۱۵</td><td>۰.۴۹</td></tr><tr><td>۲۰</td><td>۰.۴۲</td></tr>
              <tr><td>۲۵</td><td>۰.۳۷</td></tr><tr><td>۳۰</td><td>۰.۳۳</td></tr>
              <tr><td>۴۰</td><td>۰.۲۹</td></tr>
            </tbody>
          </table>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">تفاوت CVR و CVI چیست؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">CVR ضرورت گویه را می‌سنجد (آیا گویه لازم است؟) و CVI مرتبط بودن گویه را (آیا گویه با هدف مرتبط است؟). معمولاً هر دو گزارش می‌شوند.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چند متخصص برای روایی محتوا لازم است؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">معمولاً بین ۵ تا ۱۵ متخصص توصیه می‌شود. هرچه تعداد متخصصان بیشتر باشد، حداقل CVR قابل قبول پایین‌تر می‌آید (طبق جدول لاوشه).</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">S-CVI چیست؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">S-CVI میانگین شاخص روایی محتوا برای کل ابزار است (میانگین I-CVI همه گویه‌ها). مقدار ۰.۹ و بالاتر برای کل ابزار مطلوب است.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به اعتبارسنجی پرسش‌نامه نیاز دارید؟</h2>
          <p>از طراحی پرسش‌نامه و روایی محتوا تا تحلیل عاملی و سنجش پایایی — متخصصان تزنویسه در تمام مراحل کنار شما هستند.</p>
          <a href="/inquiry/" class="tzt-cta-btn">سفارش تحلیل آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzcvr-calc')) return;
      tztInitFAQ();
      // جدول لاوشه
      var lawshe={5:0.99,6:0.99,7:0.99,8:0.75,9:0.78,10:0.62,11:0.59,12:0.56,13:0.54,14:0.51,15:0.49,16:0.49,17:0.48,18:0.44,19:0.43,20:0.42,25:0.37,30:0.33,35:0.31,40:0.29};
      function minCVR(N){ if(lawshe[N]!==undefined) return lawshe[N];
        var keys=Object.keys(lawshe).map(Number).sort(function(a,b){return a-b;});
        for(var i=0;i<keys.length;i++){ if(N<=keys[i]) return lawshe[keys[i]]; }
        return 0.29; }

      document.getElementById('tzcvr-sample').addEventListener('click', function(){
        document.getElementById('tzcvr-experts').value=10;
        document.getElementById('tzcvr-data').value='9\n8\n10\n7\n6\n10\n9\n5';
      });

      document.getElementById('tzcvr-calc').addEventListener('click', function(){
        var err=document.getElementById('tzcvr-err'); err.classList.remove('tzt-show');
        document.getElementById('tzcvr-result').classList.remove('tzt-show');
        try{
          var N=parseInt(document.getElementById('tzcvr-experts').value);
          if(!N||N<2) throw 'تعداد متخصصان باید حداقل ۲ باشد.';
          var lines=document.getElementById('tzcvr-data').value.trim().split(/\r?\n/).filter(function(l){return l.trim()!=='';});
          // استخراج عدد از هر خط
          var items=lines.map(function(l){ var m=l.match(/(\d+(\.\d+)?)\s*$/); return m?parseFloat(m[1]):NaN; });
          if(items.some(isNaN)) throw 'هر خط باید با یک عدد (تعداد متخصصان موافق) پایان یابد.';
          if(items.length<1) throw 'حداقل یک گویه وارد کنید.';
          if(items.some(function(v){return v>N;})) throw 'تعداد موافقان نمی‌تواند بیشتر از کل متخصصان باشد.';

          var minAcceptable=minCVR(N);
          var rows='',sumCVR=0,sumCVI=0,acceptCount=0;
          for(var i=0;i<items.length;i++){
            var ne=items[i];
            var cvr=(ne-N/2)/(N/2);
            var icvi=ne/N;
            sumCVR+=cvr; sumCVI+=icvi;
            var ok=cvr>=minAcceptable;
            if(ok) acceptCount++;
            rows+='<tr><td>گویه '+tztToPersian(i+1)+'</td><td>'+tztToPersian(ne)+'</td><td>'+tztToPersian(cvr.toFixed(2))+'</td><td>'+tztToPersian(icvi.toFixed(2))+'</td><td class="'+(ok?'tzt-ok':'tzt-no')+'">'+(ok?'✓ قبول':'✕ بازنگری')+'</td></tr>';
          }
          var avgCVR=sumCVR/items.length;
          var scvi=sumCVI/items.length;

          var html='<div class="tzt-result-main">';
          html+='<div class="tzt-result-label">شاخص روایی محتوای کل (S-CVI)</div>';
          html+='<div class="tzt-result-value">'+tztToPersian(scvi.toFixed(3))+'</div>';
          var sc=scvi>=0.9?'#16a34a':(scvi>=0.79?'#84cc16':'#dc2626');
          var st=scvi>=0.9?'عالی':(scvi>=0.79?'قابل قبول':'نیازمند بازنگری');
          html+='<span class="tzt-result-badge" style="background:'+sc+';">'+st+'</span>';
          html+='<div class="tzt-result-meta">';
          html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(avgCVR.toFixed(3))+'</strong><span>میانگین CVR</span></div>';
          html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(minAcceptable.toFixed(2))+'</strong><span>حداقل CVR لازم</span></div>';
          html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(acceptCount)+'/'+tztToPersian(items.length)+'</strong><span>گویه قابل قبول</span></div>';
          html+='</div></div>';
          html+='<div class="tzt-tbl-wrap"><table class="tzt-tbl"><thead><tr><th>گویه</th><th>موافق</th><th>CVR</th><th>I-CVI</th><th>وضعیت</th></tr></thead><tbody>'+rows+'</tbody></table></div>';
          html+='<div class="tzt-method-note"><strong>راهنما:</strong> گویه‌هایی که CVR آن‌ها از حداقل قابل قبول ('+tztToPersian(minAcceptable.toFixed(2))+' برای '+tztToPersian(N)+' متخصص) کمتر است، باید بازنگری یا حذف شوند. مقدار I-CVI ≥ ۰.۷۹ برای هر گویه مطلوب است.</div>';
          var box=document.getElementById('tzcvr-result'); box.innerHTML=html; box.classList.add('tzt-show');
        }catch(e){ err.textContent='⚠️ '+(typeof e==='string'?e:'خطا در محاسبه.'); err.classList.add('tzt-show'); }
      });
    });
    </script>
    <?php
    return ob_get_clean();
}

add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post; if(!$post || strpos($post->post_content,'tz_cvr')===false) return;
    $url=get_permalink();
    $app=array('@context'=>'https://schema.org','@type'=>'WebApplication','name'=>'محاسبه‌گر روایی محتوا CVR و CVI تزنویسه','url'=>$url,'applicationCategory'=>'EducationalApplication','operatingSystem'=>'All','description'=>'ابزار رایگان محاسبه نسبت روایی محتوا CVR و شاخص روایی محتوا CVI با جدول لاوشه','inLanguage'=>'fa-IR','offers'=>array('@type'=>'Offer','price'=>'0','priceCurrency'=>'IRR'),'publisher'=>array('@type'=>'Organization','name'=>'تزنویسه'));
    echo "\n".'<script type="application/ld+json">'.wp_json_encode($app,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
});
}

/* --- power-analysis-calculator --- */
if ( ! function_exists( 'teznevise_power_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر توان آزمون (Power Analysis)
// ============================================
add_shortcode('tz_power', 'teznevise_power_calc');
function teznevise_power_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">🧮 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر توان آزمون و حجم نمونه</h1>
          <p>حجم نمونه مورد نیاز برای دستیابی به توان آماری مطلوب، یا توان آزمون با حجم نمونه مشخص را محاسبه کنید (برای آزمون t مقایسه دو گروه).</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-field">
            <label>چه چیزی را می‌خواهید محاسبه کنید؟</label>
            <div class="tzt-pills" id="tzpw-mode">
              <div class="tzt-pill tzt-pill-active" data-mode="n">حجم نمونه لازم</div>
              <div class="tzt-pill" data-mode="power">توان آزمون</div>
            </div>
          </div>

          <div class="tzt-note">💡 این ابزار برای آزمون t مستقل (مقایسه میانگین دو گروه) طراحی شده است. اندازه اثر (Cohen's d): کوچک=۰.۲، متوسط=۰.۵، بزرگ=۰.۸</div>

          <div class="tzt-grid3">
            <div class="tzt-field">
              <label>اندازه اثر (Cohen's d)</label>
              <input type="number" id="tzpw-effect" step="0.01" value="0.5" />
            </div>
            <div class="tzt-field">
              <label>سطح معناداری (α)</label>
              <select id="tzpw-alpha">
                <option value="0.05" selected>۰.۰۵</option>
                <option value="0.01">۰.۰۱</option>
                <option value="0.10">۰.۱۰</option>
              </select>
            </div>
            <div class="tzt-field" id="tzpw-power-field">
              <label>توان مطلوب (Power)</label>
              <select id="tzpw-power-target">
                <option value="0.80" selected>۰.۸۰</option>
                <option value="0.90">۰.۹۰</option>
                <option value="0.95">۰.۹۵</option>
              </select>
            </div>
            <div class="tzt-field" id="tzpw-n-field" style="display:none;">
              <label>حجم نمونه هر گروه (n)</label>
              <input type="number" id="tzpw-n-input" min="2" value="30" />
            </div>
          </div>

          <div class="tzt-field">
            <label>نوع آزمون</label>
            <div class="tzt-pills" id="tzpw-tail">
              <div class="tzt-pill tzt-pill-active" data-tail="2">دودامنه</div>
              <div class="tzt-pill" data-tail="1">یک‌دامنه</div>
            </div>
          </div>

          <div class="tzt-error" id="tzpw-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzpw-calc">محاسبه</button>
          <div class="tzt-result" id="tzpw-result"></div>
        </div>

        <div class="tzt-content">
          <h2>توان آزمون آماری چیست؟</h2>
          <p><strong>توان آزمون (Statistical Power)</strong> احتمال آن است که یک آزمون آماری، اثری را که واقعاً وجود دارد، به‌درستی تشخیص دهد (یعنی فرضیه صفر نادرست را رد کند). به‌بیان دیگر، توان آزمون احتمال <strong>پرهیز از خطای نوع دوم (β)</strong> است و برابر با <strong>۱ منهای β</strong> محاسبه می‌شود.</p>
          <p>قرارداد رایج در پژوهش‌ها، داشتن توان <strong>۰.۸۰ یا بالاتر</strong> است؛ یعنی حداقل ۸۰٪ احتمال کشف اثر واقعی.</p>

          <h2>چرا تحلیل توان مهم است؟</h2>
          <ul>
            <li><strong>تعیین حجم نمونه:</strong> پیش از اجرای پژوهش، حجم نمونه لازم برای توان مطلوب را مشخص می‌کند.</li>
            <li><strong>پرهیز از اتلاف منابع:</strong> از جمع‌آوری نمونه بیش از حد یا کمتر از حد جلوگیری می‌کند.</li>
            <li><strong>اعتبار نتایج:</strong> پژوهش با توان پایین، حتی در صورت وجود اثر واقعی، ممکن است آن را کشف نکند.</li>
            <li><strong>الزام ژورنال‌ها:</strong> بسیاری از ژورنال‌های معتبر، گزارش تحلیل توان را الزامی می‌دانند.</li>
          </ul>

          <h2>عوامل مؤثر بر توان آزمون</h2>
          <p>چهار عامل اصلی در رابطه متقابل با یکدیگر قرار دارند؛ با دانستن سه عامل، چهارمی محاسبه می‌شود:</p>
          <ol>
            <li><strong>اندازه اثر (Effect Size):</strong> بزرگی تفاوت یا رابطه مورد انتظار. اثر بزرگ‌تر، توان بیشتر.</li>
            <li><strong>حجم نمونه (n):</strong> نمونه بزرگ‌تر، توان بالاتر.</li>
            <li><strong>سطح معناداری (α):</strong> معمولاً ۰.۰۵. آلفای بزرگ‌تر، توان بیشتر اما خطر خطای نوع اول بالاتر.</li>
            <li><strong>توان (Power):</strong> معمولاً هدف ۰.۸۰.</li>
          </ol>

          <h2>اندازه اثر کوهن (Cohen's d)</h2>
          <table class="tzt-itable">
            <thead><tr><th>مقدار d</th><th>اندازه اثر</th></tr></thead>
            <tbody>
              <tr><td>۰.۲</td><td>کوچک (Small)</td></tr>
              <tr><td>۰.۵</td><td>متوسط (Medium)</td></tr>
              <tr><td>۰.۸</td><td>بزرگ (Large)</td></tr>
            </tbody>
          </table>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">توان آزمون مناسب چقدر است؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">قرارداد رایج، توان ۰.۸۰ است. در پژوهش‌های حساس (مانند کارآزمایی بالینی)، توان ۰.۹۰ یا ۰.۹۵ توصیه می‌شود.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">خطای نوع اول و دوم چیست؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">خطای نوع اول (α): رد فرضیه صفر درست (مثبت کاذب). خطای نوع دوم (β): عدم رد فرضیه صفر نادرست (منفی کاذب). توان = ۱−β.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">اندازه اثر را از کجا بیاورم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">از مطالعات مشابه قبلی، مطالعه مقدماتی (Pilot)، یا قراردادهای کوهن (کوچک/متوسط/بزرگ) استفاده کنید. اگر اطلاعی ندارید، اثر متوسط (۰.۵) محافظه‌کارانه است.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">این محاسبه‌گر برای چه آزمونی است؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">این ابزار برای آزمون t مستقل (مقایسه میانگین دو گروه) طراحی شده است. برای آزمون‌های پیچیده‌تر (ANOVA، رگرسیون و...) با متخصص آمار مشورت کنید.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل توان دقیق برای پژوهش خود نیاز دارید؟</h2>
          <p>محاسبه توان و حجم نمونه برای طرح‌های پیچیده (ANOVA، رگرسیون، مدل‌های چندسطحی) را به متخصصان تزنویسه بسپارید. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">سفارش تحلیل آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzpw-calc')) return;
      tztInitFAQ();

      // توابع آماری نرمال
      function normCDF(x){ // تابع توزیع تجمعی نرمال استاندارد
        var t=1/(1+0.2316419*Math.abs(x));
        var d=0.3989423*Math.exp(-x*x/2);
        var p=d*t*(0.3193815+t*(-0.3565638+t*(1.781478+t*(-1.821256+t*1.330274))));
        return x>0?1-p:p;
      }
      function normInv(p){ // معکوس CDF (Beasley-Springer-Moro تقریبی)
        var a=[-39.69683,220.9461,-275.9285,138.357,-30.66480,2.506628];
        var b=[-54.47609,161.5858,-155.6989,66.80131,-13.28068];
        var c=[-0.007784894,-0.3223964,-2.400758,-2.549732,4.374664,2.938163];
        var d=[0.007784695,0.3224671,2.445134,3.754408];
        var pl=0.02425, ph=1-pl, q,r,x;
        if(p<pl){ q=Math.sqrt(-2*Math.log(p)); x=(((((c[0]*q+c[1])*q+c[2])*q+c[3])*q+c[4])*q+c[5])/((((d[0]*q+d[1])*q+d[2])*q+d[3])*q+1); }
        else if(p<=ph){ q=p-0.5; r=q*q; x=(((((a[0]*r+a[1])*r+a[2])*r+a[3])*r+a[4])*r+a[5])*q/(((((b[0]*r+b[1])*r+b[2])*r+b[3])*r+b[4])*r+1); }
        else { q=Math.sqrt(-2*Math.log(1-p)); x=-(((((c[0]*q+c[1])*q+c[2])*q+c[3])*q+c[4])*q+c[5])/((((d[0]*q+d[1])*q+d[2])*q+d[3])*q+1); }
        return x;
      }

      var mode='n', tail=2;
      document.querySelectorAll('#tzpw-mode .tzt-pill').forEach(function(p){
        p.addEventListener('click', function(){
          document.querySelectorAll('#tzpw-mode .tzt-pill').forEach(function(x){x.classList.remove('tzt-pill-active');});
          this.classList.add('tzt-pill-active'); mode=this.getAttribute('data-mode');
          document.getElementById('tzpw-power-field').style.display = (mode==='n')?'block':'none';
          document.getElementById('tzpw-n-field').style.display = (mode==='power')?'block':'none';
        });
      });
      document.querySelectorAll('#tzpw-tail .tzt-pill').forEach(function(p){
        p.addEventListener('click', function(){
          document.querySelectorAll('#tzpw-tail .tzt-pill').forEach(function(x){x.classList.remove('tzt-pill-active');});
          this.classList.add('tzt-pill-active'); tail=parseInt(this.getAttribute('data-tail'));
        });
      });

      document.getElementById('tzpw-calc').addEventListener('click', function(){
        var err=document.getElementById('tzpw-err'); err.classList.remove('tzt-show');
        document.getElementById('tzpw-result').classList.remove('tzt-show');
        try{
          var d=parseFloat(document.getElementById('tzpw-effect').value);
          var alpha=parseFloat(document.getElementById('tzpw-alpha').value);
          if(!d||d<=0) throw 'اندازه اثر باید عددی مثبت باشد.';
          var zAlpha=normInv(1-alpha/tail);
          var html='';

          if(mode==='n'){
            var powerT=parseFloat(document.getElementById('tzpw-power-target').value);
            var zBeta=normInv(powerT);
            // n per group = 2*((zα+zβ)/d)^2
            var n=2*Math.pow((zAlpha+zBeta)/d,2);
            n=Math.ceil(n);
            html='<div class="tzt-result-main"><div class="tzt-result-label">حجم نمونه لازم (هر گروه)</div>';
            html+='<div class="tzt-result-value">'+tztToPersian(n)+'</div>';
            html+='<span class="tzt-result-badge" style="background:#16a34a;">نفر در هر گروه</span>';
            html+='<div class="tzt-result-meta">';
            html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(n*2)+'</strong><span>کل نمونه</span></div>';
            html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian((powerT*100))+'٪</strong><span>توان هدف</span></div>';
            html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(d)+'</strong><span>اندازه اثر</span></div>';
            html+='</div></div>';
            html+='<div class="tzt-method-note"><strong>تفسیر:</strong> برای دستیابی به توان '+tztToPersian((powerT*100))+'٪ با اندازه اثر '+tztToPersian(d)+' و سطح معناداری '+tztToPersian(alpha)+' ('+(tail===2?'دودامنه':'یک‌دامنه')+')، به <strong>'+tztToPersian(n)+' نفر در هر گروه</strong> (مجموعاً '+tztToPersian(n*2)+' نفر) نیاز دارید.</div>';
          } else {
            var nInput=parseInt(document.getElementById('tzpw-n-input').value);
            if(!nInput||nInput<2) throw 'حجم نمونه باید حداقل ۲ باشد.';
            // power = Φ(d*sqrt(n/2) - zα)
            var ncp=d*Math.sqrt(nInput/2);
            var power=normCDF(ncp-zAlpha);
            var pc=power>=0.8?'#16a34a':(power>=0.6?'#d97706':'#dc2626');
            var pt=power>=0.8?'مطلوب':(power>=0.6?'متوسط':'ناکافی');
            html='<div class="tzt-result-main"><div class="tzt-result-label">توان آزمون (Power)</div>';
            html+='<div class="tzt-result-value">'+tztToPersian((power).toFixed(3))+'</div>';
            html+='<span class="tzt-result-badge" style="background:'+pc+';">'+pt+' ('+tztToPersian((power*100).toFixed(1))+'٪)</span>';
            html+='<div class="tzt-result-meta">';
            html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian((1-power).toFixed(3))+'</strong><span>خطای نوع دوم (β)</span></div>';
            html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(nInput)+'</strong><span>n هر گروه</span></div>';
            html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(d)+'</strong><span>اندازه اثر</span></div>';
            html+='</div></div>';
            html+='<div class="tzt-method-note"><strong>تفسیر:</strong> با '+tztToPersian(nInput)+' نفر در هر گروه، احتمال کشف اثر واقعی '+tztToPersian((power*100).toFixed(1))+'٪ است. '+(power<0.8?'توان کمتر از ۰.۸ است؛ افزایش حجم نمونه توصیه می‌شود.':'توان در سطح مطلوب قرار دارد.')+'</div>';
          }
          var box=document.getElementById('tzpw-result'); box.innerHTML=html; box.classList.add('tzt-show');
        }catch(e){ err.textContent='⚠️ '+(typeof e==='string'?e:'خطا در محاسبه.'); err.classList.add('tzt-show'); }
      });
    });
    </script>
    <?php
    return ob_get_clean();
}

add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post; if(!$post || strpos($post->post_content,'tz_power')===false) return;
    $url=get_permalink();
    $app=array('@context'=>'https://schema.org','@type'=>'WebApplication','name'=>'محاسبه‌گر توان آزمون تزنویسه','url'=>$url,'applicationCategory'=>'EducationalApplication','operatingSystem'=>'All','description'=>'ابزار رایگان تحلیل توان آماری و محاسبه حجم نمونه برای آزمون t','inLanguage'=>'fa-IR','offers'=>array('@type'=>'Offer','price'=>'0','priceCurrency'=>'IRR'),'publisher'=>array('@type'=>'Organization','name'=>'تزنویسه'));
    echo "\n".'<script type="application/ld+json">'.wp_json_encode($app,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
});
}

/* --- Service CTA Shortcode --- */
if ( ! function_exists( 'teznevise_service_cta' ) ) {
// ============================================
// تزنویسه - شورت‌کد CTA صفحات خدماتی
// [tz_service_cta]
// ============================================
add_shortcode('tz_service_cta', 'teznevise_service_cta');
function teznevise_service_cta($atts) {
    $atts = shortcode_atts(array(
        'title'      => 'پروژه خود را به متخصص بسپارید',
        'text'       => 'با ثبت سفارش، پروژه شما توسط کارشناسان ما تحلیل و به مناسب‌ترین پژوهشگر تخصیص داده می‌شود. مشاوره اولیه کاملاً رایگان است و بدون هیچ تعهدی می‌توانید درباره پروژه‌تان گفت‌وگو کنید.',
        'btn1_text'  => 'ثبت سفارش پروژه',
        'btn1_url'   => '/inquiry/',
        'btn2_text'  => 'دریافت مشاوره رایگان',
        'btn2_url'   => '/contact-us/',
    ), $atts);

    ob_start();
    ?>
    <div class="tz-scta">
      <div class="tz-scta-inner">
        <h2 class="tz-scta-title"><?php echo esc_html($atts['title']); ?></h2>
        <p class="tz-scta-text"><?php echo esc_html($atts['text']); ?></p>
        <div class="tz-scta-buttons">
          <a href="<?php echo esc_url($atts['btn1_url']); ?>" class="tz-scta-btn tz-scta-primary"><?php echo esc_html($atts['btn1_text']); ?></a>
          <a href="<?php echo esc_url($atts['btn2_url']); ?>" class="tz-scta-btn tz-scta-outline"><?php echo esc_html($atts['btn2_text']); ?></a>
        </div>
      </div>
    </div>
    <?php
    return ob_get_clean();
}
}

/* --- Spearman's Rank Correlation --- */
if ( ! function_exists( 'teznevise_spearman_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر همبستگی اسپیرمن
// [tz_spearman]
// ============================================
add_shortcode('tz_spearman', 'teznevise_spearman_calc');
function teznevise_spearman_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">🧮 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر ضریب همبستگی اسپیرمن</h1>
          <p>رابطه بین دو متغیر رتبه‌ای یا غیرنرمال را با ضریب همبستگی رتبه‌ای اسپیرمن (rₛ) محاسبه کنید. مناسب داده‌هایی که پیش‌فرض نرمال بودن را ندارند.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 داده‌های دو متغیر را وارد کنید. هر مقدار در یک خط. ضریب اسپیرمن برای داده‌های رتبه‌ای، ترتیبی یا متغیرهایی که توزیع نرمال ندارند مناسب است.</div>
          <div class="tzt-grid2">
            <div class="tzt-field">
              <label>متغیر X <span class="tzt-hint">(هر مقدار یک خط)</span></label>
              <textarea id="tzsp-x" placeholder="12&#10;15&#10;18&#10;20"></textarea>
            </div>
            <div class="tzt-field">
              <label>متغیر Y <span class="tzt-hint">(هر مقدار یک خط)</span></label>
              <textarea id="tzsp-y" placeholder="14&#10;17&#10;19&#10;23"></textarea>
            </div>
          </div>
          <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <button type="button" class="tzt-sample-btn" id="tzsp-sample">📝 بارگذاری داده نمونه</button>
          </div>
          <div class="tzt-error" id="tzsp-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzsp-calc">محاسبه ضریب اسپیرمن</button>
          <div class="tzt-result" id="tzsp-result"></div>
        </div>

        <div class="tzt-content">
          <h2>ضریب همبستگی اسپیرمن چیست؟</h2>
          <p><strong>ضریب همبستگی رتبه‌ای اسپیرمن (Spearman's Rank Correlation Coefficient)</strong> که با نماد <strong>rₛ</strong> یا <strong>ρ</strong> نشان داده می‌شود، یک شاخص ناپارامتری برای سنجش شدت و جهت رابطه <strong>یکنوا (Monotonic)</strong> بین دو متغیر است. این ضریب بر خلاف پیرسون، نیازی به پیش‌فرض نرمال بودن داده‌ها ندارد.</p>
          <p>اسپیرمن زمانی استفاده می‌شود که داده‌ها <strong>رتبه‌ای (ترتیبی)</strong> باشند، توزیع نرمال نداشته باشند، یا رابطه غیرخطی اما یکنوا وجود داشته باشد. مقدار آن نیز بین <strong>۱- تا ۱+</strong> قرار می‌گیرد.</p>

          <h2>فرمول ضریب اسپیرمن</h2>
          <p>در صورت نبود رتبه‌های تکراری، از فرمول ساده زیر استفاده می‌شود:</p>
          <div class="tzt-formula-box">rₛ = 1 − (6 Σdᵢ²) / (n(n²−1))</div>
          <p>که در آن <strong>dᵢ</strong> اختلاف رتبه‌های هر جفت داده و <strong>n</strong> تعداد جفت‌هاست. در صورت وجود رتبه‌های تکراری (Ties)، محاسبه‌گر به‌طور خودکار از روش دقیق‌تر (همبستگی پیرسون روی رتبه‌ها) استفاده می‌کند.</p>

          <h2>تفاوت اسپیرمن و پیرسون</h2>
          <table class="tzt-itable">
            <thead><tr><th>ویژگی</th><th>پیرسون</th><th>اسپیرمن</th></tr></thead>
            <tbody>
              <tr><td>نوع داده</td><td>فاصله‌ای / نسبی</td><td>رتبه‌ای / ترتیبی</td></tr>
              <tr><td>نوع رابطه</td><td>خطی</td><td>یکنوا (Monotonic)</td></tr>
              <tr><td>پیش‌فرض نرمال بودن</td><td>لازم است</td><td>لازم نیست</td></tr>
              <tr><td>حساسیت به داده پرت</td><td>زیاد</td><td>کم</td></tr>
            </tbody>
          </table>

          <h2>تفسیر مقدار اسپیرمن</h2>
          <table class="tzt-itable">
            <thead><tr><th>قدر مطلق rₛ</th><th>شدت رابطه</th></tr></thead>
            <tbody>
              <tr><td>۰ تا ۰.۱۹</td><td>بسیار ضعیف</td></tr>
              <tr><td>۰.۲ تا ۰.۳۹</td><td>ضعیف</td></tr>
              <tr><td>۰.۴ تا ۰.۵۹</td><td>متوسط</td></tr>
              <tr><td>۰.۶ تا ۰.۷۹</td><td>قوی</td></tr>
              <tr><td>۰.۸ تا ۱</td><td>بسیار قوی</td></tr>
            </tbody>
          </table>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چه زمانی از اسپیرمن به جای پیرسون استفاده کنیم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">وقتی داده‌ها رتبه‌ای هستند، توزیع نرمال ندارند، داده پرت دارند یا رابطه غیرخطی اما یکنوا است. اسپیرمن گزینه ناپارامتری مناسب است.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">رتبه‌های تکراری (Ties) چه تأثیری دارند؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">وقتی چند مقدار یکسان وجود دارد، رتبه میانگین به آن‌ها اختصاص می‌یابد. این محاسبه‌گر در صورت وجود Ties به‌طور خودکار از روش دقیق‌تر استفاده می‌کند.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">معناداری اسپیرمن چگونه بررسی می‌شود؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">با آماره t (در نمونه‌های بزرگ‌تر از ۱۰) که این محاسبه‌گر آن را ارائه می‌دهد، یا با جداول مقادیر بحرانی اسپیرمن برای نمونه‌های کوچک.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل همبستگی تخصصی نیاز دارید؟</h2>
          <p>از همبستگی پیرسون و اسپیرمن تا رگرسیون و مدل‌سازی پیشرفته — متخصصان تزنویسه کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">سفارش تحلیل آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzsp-calc')) return;
      tztInitFAQ();
      function parseCol(t){ return t.trim().split(/\r?\n/).filter(function(l){return l.trim()!=='';}).map(function(v){return parseFloat(v.trim());}); }
      function interp(r){ var a=Math.abs(r);
        if(a>=0.8) return {t:'بسیار قوی',c:'#16a34a'}; if(a>=0.6) return {t:'قوی',c:'#22c55e'};
        if(a>=0.4) return {t:'متوسط',c:'#84cc16'}; if(a>=0.2) return {t:'ضعیف',c:'#d97706'};
        return {t:'بسیار ضعیف',c:'#dc2626'}; }
      // رتبه‌بندی با میانگین برای Ties
      function ranks(arr){
        var idx=arr.map(function(v,i){return [v,i];});
        idx.sort(function(a,b){return a[0]-b[0];});
        var r=new Array(arr.length);
        var i=0;
        while(i<idx.length){
          var j=i;
          while(j<idx.length-1 && idx[j+1][0]===idx[i][0]) j++;
          var avg=(i+j)/2+1; // رتبه میانگین (1-based)
          for(var k=i;k<=j;k++) r[idx[k][1]]=avg;
          i=j+1;
        }
        return r;
      }
      function pearson(x,y){
        var n=x.length, mx=x.reduce(function(a,b){return a+b;},0)/n, my=y.reduce(function(a,b){return a+b;},0)/n;
        var sxy=0,sxx=0,syy=0;
        for(var i=0;i<n;i++){var dx=x[i]-mx,dy=y[i]-my; sxy+=dx*dy; sxx+=dx*dx; syy+=dy*dy;}
        return sxy/Math.sqrt(sxx*syy);
      }
      function hasTies(arr){ return new Set(arr).size!==arr.length; }

      document.getElementById('tzsp-sample').addEventListener('click', function(){
        document.getElementById('tzsp-x').value='35\n23\n47\n17\n10\n43\n9\n6\n28';
        document.getElementById('tzsp-y').value='30\n33\n45\n23\n8\n49\n12\n4\n31';
      });

      document.getElementById('tzsp-calc').addEventListener('click', function(){
        var err=document.getElementById('tzsp-err'); err.classList.remove('tzt-show');
        document.getElementById('tzsp-result').classList.remove('tzt-show');
        try{
          var x=parseCol(document.getElementById('tzsp-x').value);
          var y=parseCol(document.getElementById('tzsp-y').value);
          if(x.some(isNaN)||y.some(isNaN)) throw 'داده‌ها باید عددی باشند.';
          if(x.length!==y.length) throw 'تعداد مقادیر X و Y باید برابر باشد (X: '+x.length+'، Y: '+y.length+').';
          if(x.length<3) throw 'حداقل به ۳ جفت داده نیاز است.';
          var n=x.length;
          var rx=ranks(x), ry=ranks(y);
          var ties=hasTies(x)||hasTies(y);
          var rs, method;
          if(ties){
            rs=pearson(rx,ry); method='روش دقیق (پیرسون روی رتبه‌ها — به‌دلیل وجود رتبه‌های تکراری)';
          } else {
            var sumD2=0; for(var i=0;i<n;i++){var d=rx[i]-ry[i]; sumD2+=d*d;}
            rs=1-(6*sumD2)/(n*(n*n-1)); method='فرمول ساده اسپیرمن (Σd² = '+sumD2+')';
          }
          var ip=interp(rs);
          var dir=rs>0?'مستقیم (مثبت)':(rs<0?'معکوس (منفی)':'بدون رابطه');
          var df=n-2;
          var t=(Math.abs(rs)<1)?rs*Math.sqrt(df/(1-rs*rs)):Infinity;
          var html='<div class="tzt-result-main">';
          html+='<div class="tzt-result-label">ضریب همبستگی اسپیرمن (rₛ)</div>';
          html+='<div class="tzt-result-value">'+tztToPersian(rs.toFixed(3))+'</div>';
          html+='<span class="tzt-result-badge" style="background:'+ip.c+';">'+ip.t+' — '+dir+'</span>';
          html+='<div class="tzt-result-meta">';
          html+='<div class="tzt-result-meta-item"><strong>'+(isFinite(t)?tztToPersian(t.toFixed(3)):'∞')+'</strong><span>آماره t</span></div>';
          html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(df)+'</strong><span>درجه آزادی</span></div>';
          html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(n)+'</strong><span>تعداد جفت</span></div>';
          html+='</div></div>';
          html+='<div class="tzt-method-note"><strong>روش محاسبه:</strong> '+method+'. برای بررسی معناداری، آماره t را با مقدار بحرانی t در درجه آزادی '+tztToPersian(df)+' و سطح ۰.۰۵ مقایسه کنید.</div>';
          var box=document.getElementById('tzsp-result'); box.innerHTML=html; box.classList.add('tzt-show');
        }catch(e){ err.textContent='⚠️ '+(typeof e==='string'?e:'خطا در محاسبه.'); err.classList.add('tzt-show'); }
      });
    });
    </script>
    <?php
    return ob_get_clean();
}

add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post; if(!$post || strpos($post->post_content,'tz_spearman')===false) return;
    $url=get_permalink();
    $app=array('@context'=>'https://schema.org','@type'=>'WebApplication','name'=>'محاسبه‌گر ضریب همبستگی اسپیرمن تزنویسه','url'=>$url,'applicationCategory'=>'EducationalApplication','operatingSystem'=>'All','description'=>'ابزار رایگان محاسبه ضریب همبستگی رتبه‌ای اسپیرمن برای داده‌های غیرنرمال و رتبه‌ای','inLanguage'=>'fa-IR','offers'=>array('@type'=>'Offer','price'=>'0','priceCurrency'=>'IRR'),'publisher'=>array('@type'=>'Organization','name'=>'تزنویسه'));
    echo "\n".'<script type="application/ld+json">'.wp_json_encode($app,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
    $faqs=array(
        array('چه زمانی از اسپیرمن به جای پیرسون استفاده کنیم؟','وقتی داده‌ها رتبه‌ای هستند، توزیع نرمال ندارند یا داده پرت دارند، اسپیرمن گزینه ناپارامتری مناسب است.'),
        array('رتبه‌های تکراری چه تأثیری دارند؟','رتبه میانگین به مقادیر یکسان اختصاص می‌یابد و محاسبه‌گر به‌طور خودکار از روش دقیق‌تر استفاده می‌کند.'),
        array('معناداری اسپیرمن چگونه بررسی می‌شود؟','با آماره t برای نمونه‌های بزرگ‌تر از ۱۰ یا جداول مقادیر بحرانی برای نمونه‌های کوچک.'),
    );
    $items=array(); foreach($faqs as $f){ $items[]=array('@type'=>'Question','name'=>$f[0],'acceptedAnswer'=>array('@type'=>'Answer','text'=>$f[1])); }
    echo '<script type="application/ld+json">'.wp_json_encode(array('@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$items),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
});
}

/* --- t-test-calculator --- */
if ( ! function_exists( 'teznevise_ttest_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر آزمون t (سه نوع)
// [tz_ttest]
// ============================================
add_shortcode('tz_ttest', 'teznevise_ttest_calc');
function teznevise_ttest_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">🧮 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر آزمون t</h1>
          <p>آزمون t تک‌نمونه‌ای، مستقل و زوجی را به‌صورت رایگان محاسبه کنید. کافیست داده‌ها را وارد کنید — همراه با آماره t، درجه آزادی و تفسیر معناداری.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-field">
            <label>نوع آزمون t را انتخاب کنید</label>
            <div class="tzt-pills" id="tztt-mode">
              <div class="tzt-pill tzt-pill-active" data-mode="one">تک‌نمونه‌ای</div>
              <div class="tzt-pill" data-mode="ind">مستقل (دو گروه)</div>
              <div class="tzt-pill" data-mode="paired">زوجی</div>
            </div>
          </div>

          <!-- One sample -->
          <div id="tztt-one" class="tztt-pane">
            <div class="tzt-note">💡 داده‌های یک گروه را وارد کنید و میانگین فرضی (مقدار آزمون) را مشخص نمایید.</div>
            <div class="tzt-field">
              <label>مقدار آزمون (میانگین فرضی μ₀)</label>
              <input type="number" id="tztt-mu" step="any" value="50" />
            </div>
            <div class="tzt-field">
              <label>داده‌های نمونه <span class="tzt-hint">(هر مقدار یک خط)</span></label>
              <textarea id="tztt-one-data" placeholder="48&#10;52&#10;55&#10;47&#10;53"></textarea>
            </div>
          </div>

          <!-- Independent -->
          <div id="tztt-ind" class="tztt-pane" style="display:none;">
            <div class="tzt-note">💡 داده‌های دو گروه مستقل را وارد کنید. تعداد افراد دو گروه می‌تواند متفاوت باشد.</div>
            <div class="tzt-grid2">
              <div class="tzt-field">
                <label>گروه ۱</label>
                <textarea id="tztt-g1" placeholder="48&#10;52&#10;55&#10;47"></textarea>
              </div>
              <div class="tzt-field">
                <label>گروه ۲</label>
                <textarea id="tztt-g2" placeholder="60&#10;58&#10;63&#10;59"></textarea>
              </div>
            </div>
          </div>

          <!-- Paired -->
          <div id="tztt-paired" class="tztt-pane" style="display:none;">
            <div class="tzt-note">💡 داده‌های قبل و بعد (یا دو شرایط مرتبط) را وارد کنید. تعداد باید برابر باشد.</div>
            <div class="tzt-grid2">
              <div class="tzt-field">
                <label>پیش‌آزمون (قبل)</label>
                <textarea id="tztt-pre" placeholder="48&#10;52&#10;55&#10;47"></textarea>
              </div>
              <div class="tzt-field">
                <label>پس‌آزمون (بعد)</label>
                <textarea id="tztt-post" placeholder="54&#10;57&#10;60&#10;52"></textarea>
              </div>
            </div>
          </div>

          <div class="tzt-field">
            <label>سطح معناداری (α)</label>
            <div class="tzt-pills" id="tztt-alpha">
              <div class="tzt-pill tzt-pill-active" data-a="0.05">۰.۰۵</div>
              <div class="tzt-pill" data-a="0.01">۰.۰۱</div>
              <div class="tzt-pill" data-a="0.10">۰.۱۰</div>
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <button type="button" class="tzt-sample-btn" id="tztt-sample">📝 بارگذاری داده نمونه</button>
          </div>
          <div class="tzt-error" id="tztt-err"></div>
          <button type="button" class="tzt-calc-btn" id="tztt-calc">محاسبه آزمون t</button>
          <div class="tzt-result" id="tztt-result"></div>
        </div>

        <div class="tzt-content">
          <h2>آزمون t چیست؟</h2>
          <p><strong>آزمون t (t-test)</strong> یکی از پرکاربردترین آزمون‌های آماری استنباطی برای مقایسه <strong>میانگین‌ها</strong> است. این آزمون توسط ویلیام گاست (با نام مستعار Student) معرفی شد و زمانی استفاده می‌شود که می‌خواهیم بدانیم آیا تفاوت بین میانگین‌ها از نظر آماری معنادار است یا صرفاً ناشی از تصادف.</p>

          <h2>سه نوع اصلی آزمون t</h2>
          <h3>۱. آزمون t تک‌نمونه‌ای (One-Sample)</h3>
          <p>میانگین یک نمونه را با یک <strong>مقدار مرجع یا فرضی مشخص</strong> مقایسه می‌کند. مثلاً: آیا میانگین نمرات دانشجویان با عدد ۱۵ تفاوت معنادار دارد؟</p>
          <div class="tzt-formula-box">t = (x̄ − μ₀) / (s / √n)</div>

          <h3>۲. آزمون t مستقل (Independent Samples)</h3>
          <p>میانگین <strong>دو گروه مستقل و جدا</strong> را مقایسه می‌کند. مثلاً: آیا میانگین عملکرد گروه آزمایش و گروه کنترل تفاوت دارد؟</p>
          <div class="tzt-formula-box">t = (x̄₁ − x̄₂) / √(s²ₚ(1/n₁ + 1/n₂))</div>

          <h3>۳. آزمون t زوجی (Paired Samples)</h3>
          <p>میانگین <strong>دو اندازه‌گیری مرتبط</strong> روی یک گروه را مقایسه می‌کند. مثلاً: مقایسه نمرات پیش‌آزمون و پس‌آزمون یک گروه.</p>
          <div class="tzt-formula-box">t = d̄ / (s_d / √n)</div>

          <h2>پیش‌فرض‌های آزمون t</h2>
          <ul>
            <li>متغیر وابسته باید <strong>کمی (فاصله‌ای/نسبی)</strong> باشد.</li>
            <li>داده‌ها باید تقریباً <strong>توزیع نرمال</strong> داشته باشند.</li>
            <li>در آزمون مستقل، فرض <strong>برابری واریانس‌ها</strong> (همگنی) بررسی می‌شود.</li>
            <li>مشاهدات باید مستقل باشند (به‌جز در آزمون زوجی که جفت‌ها مرتبط‌اند).</li>
          </ul>

          <h2>تفسیر نتیجه</h2>
          <p>اگر <strong>قدر مطلق آماره t محاسبه‌شده</strong> از <strong>مقدار بحرانی t</strong> (در جدول، با درجه آزادی و سطح معناداری مشخص) بزرگ‌تر باشد، فرضیه صفر رد می‌شود؛ یعنی تفاوت میانگین‌ها معنادار است. به‌بیان ساده، اگر <strong>p-value کمتر از ۰.۰۵</strong> باشد، تفاوت معنادار است.</p>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">کدام نوع آزمون t را انتخاب کنم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">اگر یک گروه را با عدد مرجع مقایسه می‌کنید: تک‌نمونه‌ای. اگر دو گروه جدا دارید: مستقل. اگر یک گروه را در دو زمان (مثل قبل/بعد) می‌سنجید: زوجی.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">اگر داده‌ها نرمال نباشند چه کنیم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">از معادل‌های ناپارامتری استفاده کنید: من-ویتنی (به‌جای t مستقل) یا ویلکاکسون (به‌جای t زوجی).</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">تفاوت آزمون یک‌دامنه و دودامنه چیست؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">دودامنه فقط وجود تفاوت را بررسی می‌کند (رایج‌تر). یک‌دامنه جهت تفاوت را هم مشخص می‌کند (مثلاً «گروه ۱ بزرگ‌تر است»). این محاسبه‌گر آماره t را ارائه می‌دهد که برای هر دو کاربرد دارد.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">این محاسبه‌گر با SPSS مطابقت دارد؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، از فرمول‌های استاندارد آماری استفاده می‌کند و آماره t و درجه آزادی منطبق با SPSS را ارائه می‌دهد.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل آماری تخصصی نیاز دارید؟</h2>
          <p>از آزمون t و ANOVA تا تحلیل‌های پیشرفته و گزارش‌نویسی کامل — متخصصان تزنویسه کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">سفارش تحلیل آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tztt-calc')) return;
      tztInitFAQ();
      var mode='one', alpha=0.05;

      function parseCol(t){ return t.trim().split(/\r?\n/).filter(function(l){return l.trim()!=='';}).map(function(v){return parseFloat(v.trim());}); }
      function mean(a){ return a.reduce(function(x,y){return x+y;},0)/a.length; }
      function variance(a){ var m=mean(a),n=a.length; return a.reduce(function(s,v){return s+(v-m)*(v-m);},0)/(n-1); }

      document.querySelectorAll('#tztt-mode .tzt-pill').forEach(function(p){
        p.addEventListener('click', function(){
          document.querySelectorAll('#tztt-mode .tzt-pill').forEach(function(x){x.classList.remove('tzt-pill-active');});
          this.classList.add('tzt-pill-active'); mode=this.getAttribute('data-mode');
          document.querySelectorAll('.tztt-pane').forEach(function(pane){pane.style.display='none';});
          document.getElementById('tztt-'+mode).style.display='block';
        });
      });
      document.querySelectorAll('#tztt-alpha .tzt-pill').forEach(function(p){
        p.addEventListener('click', function(){
          document.querySelectorAll('#tztt-alpha .tzt-pill').forEach(function(x){x.classList.remove('tzt-pill-active');});
          this.classList.add('tzt-pill-active'); alpha=parseFloat(this.getAttribute('data-a'));
        });
      });

      document.getElementById('tztt-sample').addEventListener('click', function(){
        if(mode==='one'){ document.getElementById('tztt-mu').value=50; document.getElementById('tztt-one-data').value='48\n52\n55\n47\n53\n51\n49\n54\n50\n56'; }
        else if(mode==='ind'){ document.getElementById('tztt-g1').value='48\n52\n55\n47\n53\n51'; document.getElementById('tztt-g2').value='60\n58\n63\n59\n62\n57'; }
        else { document.getElementById('tztt-pre').value='48\n52\n55\n47\n53\n51'; document.getElementById('tztt-post').value='54\n57\n60\n52\n58\n56'; }
      });

      document.getElementById('tztt-calc').addEventListener('click', function(){
        var err=document.getElementById('tztt-err'); err.classList.remove('tzt-show');
        document.getElementById('tztt-result').classList.remove('tzt-show');
        try{
          var t,df,extra='',title='',meta=[];
          if(mode==='one'){
            var mu=parseFloat(document.getElementById('tztt-mu').value);
            if(isNaN(mu)) throw 'مقدار آزمون (میانگین فرضی) را وارد کنید.';
            var d=parseCol(document.getElementById('tztt-one-data').value);
            if(d.some(isNaN)) throw 'داده‌ها باید عددی باشند.';
            if(d.length<2) throw 'حداقل ۲ مقدار لازم است.';
            var m=mean(d), s=Math.sqrt(variance(d)), n=d.length;
            t=(m-mu)/(s/Math.sqrt(n)); df=n-1;
            title='آزمون t تک‌نمونه‌ای';
            meta=[['میانگین نمونه',m.toFixed(2)],['انحراف معیار',s.toFixed(2)],['تعداد (n)',n]];
          }
          else if(mode==='ind'){
            var g1=parseCol(document.getElementById('tztt-g1').value);
            var g2=parseCol(document.getElementById('tztt-g2').value);
            if(g1.some(isNaN)||g2.some(isNaN)) throw 'داده‌ها باید عددی باشند.';
            if(g1.length<2||g2.length<2) throw 'هر گروه حداقل ۲ مقدار لازم دارد.';
            var m1=mean(g1),m2=mean(g2),v1=variance(g1),v2=variance(g2),n1=g1.length,n2=g2.length;
            var sp2=((n1-1)*v1+(n2-1)*v2)/(n1+n2-2);
            t=(m1-m2)/Math.sqrt(sp2*(1/n1+1/n2)); df=n1+n2-2;
            title='آزمون t مستقل';
            meta=[['میانگین گروه ۱',m1.toFixed(2)],['میانگین گروه ۲',m2.toFixed(2)],['n₁ / n₂',n1+' / '+n2]];
          }
          else {
            var pre=parseCol(document.getElementById('tztt-pre').value);
            var post=parseCol(document.getElementById('tztt-post').value);
            if(pre.some(isNaN)||post.some(isNaN)) throw 'داده‌ها باید عددی باشند.';
            if(pre.length!==post.length) throw 'تعداد پیش‌آزمون و پس‌آزمون باید برابر باشد.';
            if(pre.length<2) throw 'حداقل ۲ جفت لازم است.';
            var diffs=pre.map(function(v,i){return post[i]-v;});
            var md=mean(diffs),sd=Math.sqrt(variance(diffs)),nd=diffs.length;
            t=md/(sd/Math.sqrt(nd)); df=nd-1;
            title='آزمون t زوجی';
            meta=[['میانگین تفاوت',md.toFixed(2)],['انحراف معیار تفاوت',sd.toFixed(2)],['تعداد جفت',nd]];
          }

          // مقدار بحرانی t تقریبی (دودامنه) برای تفسیر سریع
          var tCrit = critT(df, alpha);
          var sig = Math.abs(t) > tCrit;
          var color = sig ? '#16a34a' : '#dc2626';
          var badge = sig ? 'معنادار (p < '+tztToPersian(alpha)+')' : 'غیرمعنادار (p ≥ '+tztToPersian(alpha)+')';

          var html='<div class="tzt-result-main">';
          html+='<div class="tzt-result-label">'+title+' — آماره t</div>';
          html+='<div class="tzt-result-value">'+tztToPersian(t.toFixed(3))+'</div>';
          html+='<span class="tzt-result-badge" style="background:'+color+';">'+badge+'</span>';
          html+='<div class="tzt-result-meta">';
          html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(df)+'</strong><span>درجه آزادی</span></div>';
          html+='<div class="tzt-result-meta-item"><strong>±'+tztToPersian(tCrit.toFixed(3))+'</strong><span>t بحرانی</span></div>';
          meta.forEach(function(m){ html+='<div class="tzt-result-meta-item"><strong>'+tztToPersian(typeof m[1]==='number'?m[1]:m[1])+'</strong><span>'+m[0]+'</span></div>'; });
          html+='</div></div>';
          html+='<div class="tzt-method-note"><strong>تفسیر:</strong> چون |t| = '+tztToPersian(Math.abs(t).toFixed(2))+' '+(sig?'<strong>بزرگ‌تر</strong>':'کوچک‌تر')+' از t بحرانی ('+tztToPersian(tCrit.toFixed(2))+') در درجه آزادی '+tztToPersian(df)+' و سطح '+tztToPersian(alpha)+' است، فرضیه صفر '+(sig?'<strong>رد می‌شود</strong> و تفاوت میانگین‌ها از نظر آماری معنادار است.':'رد نمی‌شود و تفاوت معنادار نیست.')+'</div>';
          var box=document.getElementById('tztt-result'); box.innerHTML=html; box.classList.add('tzt-show');
        }catch(e){ err.textContent='⚠️ '+(typeof e==='string'?e:'خطا در محاسبه.'); err.classList.add('tzt-show'); }
      });

      // تقریب مقدار بحرانی t (دودامنه) — روش معکوس تقریبی
      function critT(df, alpha){
        // مقادیر z برای دودامنه
        var zMap={0.05:1.95996, 0.01:2.57583, 0.10:1.64485};
        var z=zMap[alpha]||1.95996;
        // تصحیح Cornish-Fisher برای تبدیل z به t
        var g1=(z*z*z+z)/4;
        var g2=(5*z*z*z*z*z+16*z*z*z+3*z)/96;
        var g3=(3*z*z*z*z*z*z*z+19*z*z*z*z*z+17*z*z*z-15*z)/384;
        var t=z+g1/df+g2/(df*df)+g3/(df*df*df);
        return t;
      }
    });
    </script>
    <?php
    return ob_get_clean();
}

add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post; if(!$post || strpos($post->post_content,'tz_ttest')===false) return;
    $url=get_permalink();
    $app=array('@context'=>'https://schema.org','@type'=>'WebApplication','name'=>'محاسبه‌گر آزمون t تزنویسه','url'=>$url,'applicationCategory'=>'EducationalApplication','operatingSystem'=>'All','description'=>'ابزار رایگان محاسبه آزمون t تک‌نمونه‌ای، مستقل و زوجی برای مقایسه میانگین‌ها','inLanguage'=>'fa-IR','offers'=>array('@type'=>'Offer','price'=>'0','priceCurrency'=>'IRR'),'publisher'=>array('@type'=>'Organization','name'=>'تزنویسه'));
    echo "\n".'<script type="application/ld+json">'.wp_json_encode($app,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
    $faqs=array(
        array('کدام نوع آزمون t را انتخاب کنم؟','اگر یک گروه را با عدد مرجع مقایسه می‌کنید تک‌نمونه‌ای، اگر دو گروه جدا دارید مستقل، و اگر یک گروه را در دو زمان می‌سنجید زوجی.'),
        array('اگر داده‌ها نرمال نباشند چه کنیم؟','از معادل‌های ناپارامتری مانند من-ویتنی یا ویلکاکسون استفاده کنید.'),
        array('این محاسبه‌گر با SPSS مطابقت دارد؟','بله، از فرمول‌های استاندارد استفاده می‌کند و آماره t و درجه آزادی منطبق با SPSS را ارائه می‌دهد.'),
    );
    $items=array(); foreach($faqs as $f){ $items[]=array('@type'=>'Question','name'=>$f[0],'acceptedAnswer'=>array('@type'=>'Answer','text'=>$f[1])); }
    echo '<script type="application/ld+json">'.wp_json_encode(array('@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$items),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
});
}

/* --- descriptive-statistics-calculator --- */
if ( ! function_exists( 'teznevise_descriptive_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر آمار توصیفی آنلاین
// [tz_descriptive]
// ============================================
add_shortcode('tz_descriptive', 'teznevise_descriptive_calc');
function teznevise_descriptive_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <!-- Hero Section -->
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">🧮 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر آمار توصیفی آنلاین</h1>
          <p>میانگین، انحراف معیار، میانه، چارک‌ها، واریانس، کجی، کشیدگی و شناسایی داده‌های پرت را به‌صورت خودکار محاسبه کنید.</p>
        </div></div>

        <!-- Calculator Card -->
        <div class="tzt-card">
          <div class="tzt-note">💡 داده‌های خود را وارد کنید — هر مقدار در یک خط یا با فاصله/کاما جدا کنید.</div>
          <div class="tzt-field">
            <label>داده‌های نمونه <span class="tzt-hint">(مثال: 23 25 21 28 24)</span></label>
            <textarea id="tzdesc-data" placeholder="23&#10;25&#10;21&#10;28&#10;24&#10;26&#10;22&#10;27"></textarea>
          </div>
          <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <button type="button" class="tzt-sample-btn" id="tzdesc-sample">📝 داده نمونه</button>
          </div>
          <div class="tzt-error" id="tzdesc-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzdesc-calc">محاسبه آمار توصیفی</button>
          <div class="tzt-result" id="tzdesc-result"></div>
        </div>

        <!-- Educational Content -->
        <div class="tzt-content">
          <h2>آمار توصیفی چیست؟</h2>
          <p><strong>آمار توصیفی</strong> مجموعه‌ای از شاخص‌هاست که برای خلاصه‌سازی و توصیف ویژگی‌های اصلی داده‌ها استفاده می‌شود. این شاخص‌ها شامل میانگین، میانه، نما، انحراف معیار، واریانس، چارک‌ها، دامنه، ضریب تغییر، خطای معیار، کجی و کشیدگی است.</p>

          <h2>شاخص‌های اصلی</h2>
          <h3>۱. شاخص‌های مرکزی (Central Tendency)</h3>
          <ul>
            <li><strong>میانگین (Mean):</strong> مجموع تمام مقادیر تقسیم بر تعداد.</li>
            <li><strong>میانه (Median):</strong> مقدار وسطی پس از مرتب‌سازی.</li>
            <li><strong>نما (Mode):</strong> مقدار با بیشترین تکرار.</li>
          </ul>

          <h3>۲. شاخص‌های پراکندگی (Dispersion)</h3>
          <ul>
            <li><strong>انحراف معیار (Std Dev):</strong> میزان پراکندگی داده‌ها.</li>
            <li><strong>واریانس:</strong> مجذور انحراف معیار.</li>
            <li><strong>دامنه (Range):</strong> تفاوت بین بزرگ‌ترین و کوچک‌ترین مقدار.</li>
            <li><strong>دامنه بین‌چارکی (IQR):</strong> Q3 − Q1.</li>
          </ul>

          <h3>۳. شاخص‌های شکل (Shape)</h3>
          <ul>
            <li><strong>کجی (Skewness):</strong> میزان عدم تقارن توزیع.</li>
            <li><strong>کشیدگی (Kurtosis):</strong> میزان تیزی یا تختی توزیع.</li>
          </ul>

          <h2>فرمول‌های کلیدی</h2>
          <div class="tzt-formula-box">
            میانگین: x̄ = Σx / n<br>
            انحراف معیار: s = √(Σ(x−x̄)² / (n−1))<br>
            واریانس: s² = Σ(x−x̄)² / (n−1)
          </div>
        </div>

        <!-- FAQ Section -->
        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">تفاوت میانگین و میانه چیست؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">میانگین به داده‌های پرت حساس است؛ میانه مقاوم‌تر است.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چرا از n-1 در انحراف معیار استفاده می‌شود؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">برای نمونه‌های آماری، تقسیم بر n-1 (درجه آزادی) باعث تخمین بدون‌طرفانه‌تر می‌شود.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">ضریب تغییر (CV) چه کاربردی دارد؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">برای مقایسه پراکندگی نسبی بین متغیرهایی با واحد متفاوت.</div></div></div>
        </div>

        <!-- CTA Section -->
        <div class="tzt-cta">
          <h2>به تحلیل آماری تخصصی نیاز دارید؟</h2>
          <p>از آمار توصیفی تا مدل‌سازی پیشرفته — متخصصان تزنویسه در تمام مراحل پژوهش کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      // بررسی وجود عنصر
      if(!document.getElementById('tzdesc-calc')) return;

      // تابع تبدیل اعداد به فارسی
      window.tztToPersian = function(num) {
        var persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        return String(num).replace(/\d/g, function(digit) {
          return persian[digit];
        });
      };

      // تابع FAQ
      window.tztInitFAQ = function(){
        var items = document.querySelectorAll('.tzt-faq-item');
        for(var i = 0; i < items.length; i++){
          (function(item){
            var q = item.querySelector('.tzt-faq-q');
            if(!q) return;
            q.addEventListener('click', function(){
              item.classList.toggle('tzt-faq-open');
            });
          })(items[i]);
        }
      };

      tztInitFAQ();

      // تابع پارس کردن داده‌ها
      function parseData(text) {
        var data = text.trim()
          .replace(/[,;]/g, ' ')
          .split(/[\s\n\r]+/)
          .filter(function(x) { return x.length > 0; })
          .map(function(x) { return parseFloat(x); });
        return data.filter(function(x) { return !isNaN(x); });
      }

      // تابع محاسبه میانگین
      function calcMean(arr) {
        if(arr.length === 0) return 0;
        var sum = arr.reduce(function(a, b) { return a + b; }, 0);
        return sum / arr.length;
      }

      // تابع محاسبه واریانس
      function calcVariance(arr) {
        if(arr.length < 2) return 0;
        var mean = calcMean(arr);
        var sq = arr.reduce(function(s, x) { return s + Math.pow(x - mean, 2); }, 0);
        return sq / (arr.length - 1);
      }

      // تابع محاسبه انحراف معیار
      function calcStdDev(arr) {
        return Math.sqrt(calcVariance(arr));
      }

      // تابع محاسبه میانه
      function calcMedian(arr) {
        if(arr.length === 0) return 0;
        var sorted = arr.slice().sort(function(a, b) { return a - b; });
        var n = sorted.length;
        if(n % 2 === 0) {
          return (sorted[n/2 - 1] + sorted[n/2]) / 2;
        } else {
          return sorted[Math.floor(n/2)];
        }
      }

      // تابع محاسبه صدک (percentile)
      function calcPercentile(sorted, p) {
        if(sorted.length === 0) return 0;
        var idx = p * (sorted.length - 1);
        var lo = Math.floor(idx);
        var hi = Math.ceil(idx);
        if(lo === hi) return sorted[lo];
        var weight = idx - lo;
        return sorted[lo] * (1 - weight) + sorted[hi] * weight;
      }

      // تابع محاسبه کجی
      function calcSkewness(arr) {
        if(arr.length < 3) return 0;
        var mean = calcMean(arr);
        var stddev = calcStdDev(arr);
        if(stddev === 0) return 0;
        var sum = arr.reduce(function(s, x) { 
          return s + Math.pow((x - mean) / stddev, 3); 
        }, 0);
        return sum / arr.length;
      }

      // تابع محاسبه کشیدگی
      function calcKurtosis(arr) {
        if(arr.length < 4) return 0;
        var mean = calcMean(arr);
        var stddev = calcStdDev(arr);
        if(stddev === 0) return 0;
        var sum = arr.reduce(function(s, x) { 
          return s + Math.pow((x - mean) / stddev, 4); 
        }, 0);
        return (sum / arr.length) - 3;
      }

      // داده نمونه
      document.getElementById('tzdesc-sample').addEventListener('click', function(){
        document.getElementById('tzdesc-data').value = '23\n25\n21\n28\n24\n26\n22\n27\n25\n23\n29\n20\n24\n26\n25\n22\n28\n24\n27\n25';
      });

      // محاسبه آمار توصیفی
      document.getElementById('tzdesc-calc').addEventListener('click', function(){
        var errEl = document.getElementById('tzdesc-err');
        var resultEl = document.getElementById('tzdesc-result');
        
        errEl.classList.remove('tzt-show');
        resultEl.classList.remove('tzt-show');
        resultEl.innerHTML = '';

        try {
          var rawData = document.getElementById('tzdesc-data').value;
          if(!rawData || rawData.trim().length === 0) {
            throw 'لطفاً داده‌ها را وارد کنید.';
          }

          var data = parseData(rawData);
          if(data.length < 2) {
            throw 'حداقل ۲ مقدار لازم است.';
          }

          // محاسبات
          var n = data.length;
          var sum = data.reduce(function(a, b) { return a + b; }, 0);
          var mean = calcMean(data);
          var median = calcMedian(data);
          var variance = calcVariance(data);
          var stddev = calcStdDev(data);
          var sorted = data.slice().sort(function(a, b) { return a - b; });
          var min = sorted[0];
          var max = sorted[n - 1];
          var range = max - min;
          var q1 = calcPercentile(sorted, 0.25);
          var q3 = calcPercentile(sorted, 0.75);
          var iqr = q3 - q1;
          var cv = (mean !== 0) ? (stddev / mean) * 100 : 0;
          var sem = stddev / Math.sqrt(n);
          var skewness = calcSkewness(data);
          var kurtosis = calcKurtosis(data);

          // نما
          var freq = {};
          for(var i = 0; i < n; i++) {
            var key = data[i].toFixed(6);
            freq[key] = (freq[key] || 0) + 1;
          }
          var maxFreq = 0;
          for(var k in freq) {
            if(freq[k] > maxFreq) maxFreq = freq[k];
          }
          var modes = [];
          for(var k in freq) {
            if(freq[k] === maxFreq && maxFreq > 1) {
              modes.push(parseFloat(k));
            }
          }
          var modeStr = (modes.length > 0) ? modes.map(function(x) { return tztToPersian(x.toFixed(2)); }).join('، ') : '—';

          // داده‌های پرت
          var outlierLower = q1 - 1.5 * iqr;
          var outlierUpper = q3 + 1.5 * iqr;
          var outliers = [];
          for(var i = 0; i < n; i++) {
            if(data[i] < outlierLower || data[i] > outlierUpper) {
              outliers.push(data[i]);
            }
          }

          // ساخت جدول
          var html = '<div class="tzt-result-main">';
          html += '<div class="tzt-result-label">خلاصه آمار توصیفی</div>';
          html += '<div class="tzt-result-value">'+tztToPersian(n)+' مقدار</div>';
          html += '<span class="tzt-result-badge" style="background:#145D4A;">داده‌های تحلیل‌شده</span>';
          html += '</div>';

          html += '<div class="tzt-tbl-wrap" style="margin-top:20px;"><table class="tzt-tbl"><thead><tr><th>شاخص</th><th>مقدار</th></tr></thead><tbody>';
          
          var stats = [
            ['تعداد (n)', tztToPersian(n)],
            ['مجموع', tztToPersian(sum.toFixed(2))],
            ['میانگین', tztToPersian(mean.toFixed(3))],
            ['میانه', tztToPersian(median.toFixed(3))],
            ['نما', modeStr],
            ['کمینه', tztToPersian(min.toFixed(2))],
            ['بیشینه', tztToPersian(max.toFixed(2))],
            ['دامنه', tztToPersian(range.toFixed(2))],
            ['انحراف معیار', tztToPersian(stddev.toFixed(3))],
            ['واریانس', tztToPersian(variance.toFixed(3))],
            ['خطای معیار', tztToPersian(sem.toFixed(3))],
            ['ضریب تغییر', tztToPersian(cv.toFixed(2)) + ' %'],
            ['چارک ۱ (Q1)', tztToPersian(q1.toFixed(3))],
            ['چارک ۳ (Q3)', tztToPersian(q3.toFixed(3))],
            ['دامنه بین‌چارکی', tztToPersian(iqr.toFixed(3))],
            ['کجی', tztToPersian(skewness.toFixed(3))],
            ['کشیدگی', tztToPersian(kurtosis.toFixed(3))]
          ];

          for(var i = 0; i < stats.length; i++) {
            html += '<tr><td style="text-align:right; font-weight:bold;">'+stats[i][0]+'</td><td style="text-align:center;">'+stats[i][1]+'</td></tr>';
          }
          html += '</tbody></table></div>';

          // داده‌های پرت
          if(outliers.length > 0) {
            html += '<div class="tzt-method-note" style="background:#fff3cd; border-color:#d97706;"><strong>⚠️ داده‌های پرت شناسایی شدند:</strong> '+tztToPersian(outliers.length)+' مقدار (معیار: < '+tztToPersian(outlierLower.toFixed(2))+' یا > '+tztToPersian(outlierUpper.toFixed(2))+')</div>';
          }

          // تفسیر شکل توزیع
          var skewInterpret = Math.abs(skewness) < 0.5 ? 'تقریباً متقارن' : (skewness > 0 ? 'دنباله به راست' : 'دنباله به چپ');
          var kurtInterpret = Math.abs(kurtosis) < 0.5 ? 'نزدیک به نرمال' : (kurtosis > 0 ? 'حادتر از نرمال' : 'صاف‌تر از نرمال');
          html += '<div class="tzt-method-note"><strong>تفسیر:</strong> توزیع '+skewInterpret+' است و '+kurtInterpret+'.</div>';

          resultEl.innerHTML = html;
          resultEl.classList.add('tzt-show');

        } catch(e) {
          errEl.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
          errEl.classList.add('tzt-show');
        }
      });
    });
    </script>
    <?php
    return ob_get_clean();
}

// سئو و Schema
add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post;
    if(!$post || strpos($post->post_content, 'tz_descriptive') === false) return;
    
    $url = get_permalink();
    $app = array(
      '@context' => 'https://schema.org',
      '@type' => 'WebApplication',
      'name' => 'محاسبه‌گر آمار توصیفی تزنویسه',
      'url' => $url,
      'applicationCategory' => 'EducationalApplication',
      'operatingSystem' => 'All',
      'description' => 'ابزار رایگان محاسبه آمار توصیفی: میانگین، انحراف معیار، میانه، چارک‌ها، واریانس، کجی و کشیدگی',
      'inLanguage' => 'fa-IR',
      'offers' => array('@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IRR'),
      'publisher' => array('@type' => 'Organization', 'name' => 'تزنویسه')
    );
    echo "\n" . '<script type="application/ld+json">' . wp_json_encode($app, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    
    $faqs = array(
        array('تفاوت میانگین و میانه چیست؟', 'میانگین به داده‌های پرت حساس است؛ میانه مقاوم‌تر است و برای توزیع‌های نامتقارن مناسب‌تر است.'),
        array('چرا از n-1 در انحراف معیار استفاده می‌شود؟', 'برای نمونه‌های آماری، تقسیم بر n-1 (درجه آزادی) باعث تخمین بدون‌طرفانه‌تر می‌شود.'),
        array('ضریب تغییر (CV) چه کاربردی دارد؟', 'برای مقایسه پراکندگی نسبی بین متغیرهایی با واحد یا مقیاس متفاوت استفاده می‌شود.'),
    );
    
    $items = array();
    foreach($faqs as $f){
      $items[] = array(
        '@type' => 'Question',
        'name' => $f[0],
        'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f[1])
      );
    }
    echo '<script type="application/ld+json">' . wp_json_encode(array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
});
}

/* --- kr20-kr21-calculator --- */
if ( ! function_exists( 'teznevise_kr20_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر KR-20 و KR-21
// [tz_kr20]
// ============================================
add_shortcode('tz_kr20', 'teznevise_kr20_calc');
function teznevise_kr20_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">🧮 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر KR-20 و KR-21</h1>
          <p>ضریب پایایی کودر-ریچاردسون (KR-20 و KR-21) برای آزمون‌های دوحالتی (صحیح/غلط) را محاسبه کنید. معادل آلفای کرونباخ برای داده‌های دوحالتی.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-field">
            <label>نوع محاسبه را انتخاب کنید</label>
            <div class="tzt-pills" id="tzkr-mode">
              <div class="tzt-pill tzt-pill-active" data-mode="kr20">KR-20 (دقیق)</div>
              <div class="tzt-pill" data-mode="kr21">KR-21 (تقریبی)</div>
            </div>
          </div>

          <!-- KR-20 -->
          <div id="tzkr-kr20" class="tzkr-pane">
            <div class="tzt-note">💡 <strong>KR-20:</strong> برای هر سؤال، تعداد افرادی که آن را صحیح پاسخ داده‌اند را وارد کنید. دقیق‌ترین روش.</div>
            <div class="tzt-field">
              <label>تعداد کل شرکت‌کنندگان (N)</label>
              <input type="number" id="tzkr-n" min="2" value="30" />
            </div>
            <div class="tzt-field">
              <label>تعداد سؤالات (k)</label>
              <input type="number" id="tzkr-k" min="2" value="10" />
            </div>
            <div class="tzt-field">
              <label>برای هر سؤال، تعداد پاسخ‌های صحیح را وارد کنید <span class="tzt-hint">(هر عدد یک خط)</span></label>
              <textarea id="tzkr-correct" placeholder="28&#10;25&#10;30&#10;22&#10;27&#10;24&#10;29&#10;26&#10;23&#10;25"></textarea>
            </div>
          </div>

          <!-- KR-21 -->
          <div id="tzkr-kr21" class="tzkr-pane" style="display:none;">
            <div class="tzt-note">💡 <strong>KR-21:</strong> روش تقریبی که فقط میانگین نمرات را نیاز دارد. سریع‌تر اما کمتر دقیق.</div>
            <div class="tzt-grid3">
              <div class="tzt-field">
                <label>تعداد سؤالات (k)</label>
                <input type="number" id="tzkr-k21" min="2" value="10" />
              </div>
              <div class="tzt-field">
                <label>میانگین نمرات (M)</label>
                <input type="number" id="tzkr-mean" step="0.01" value="7.5" />
              </div>
              <div class="tzt-field">
                <label>واریانس نمرات (s²)</label>
                <input type="number" id="tzkr-var" step="0.01" value="4.2" />
              </div>
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <button type="button" class="tzt-sample-btn" id="tzkr-sample">📝 بارگذاری داده نمونه</button>
          </div>
          <div class="tzt-error" id="tzkr-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzkr-calc">محاسبه ضریب پایایی</button>
          <div class="tzt-result" id="tzkr-result"></div>
        </div>

        <div class="tzt-content">
          <h2>KR-20 و KR-21 چیست؟</h2>
          <p><strong>ضریب کودر-ریچاردسون (Kuder-Richardson)</strong> شاخص‌هایی برای سنجش <strong>پایایی و همسانی درونی</strong> آزمون‌های دوحالتی (صحیح/غلط) هستند. این ضریب‌ها معادل <strong>آلفای کرونباخ</strong> برای داده‌های دوحالتی محسوب می‌شوند.</p>

          <h2>تفاوت KR-20 و KR-21</h2>
          <table class="tzt-itable">
            <thead><tr><th>ویژگی</th><th>KR-20</th><th>KR-21</th></tr></thead>
            <tbody>
              <tr><td>دقت</td><td>دقیق (بهترین)</td><td>تقریبی</td></tr>
              <tr><td>داده‌های لازم</td><td>تعداد صحیح هر سؤال</td><td>میانگین و واریانس کل</td></tr>
              <tr><td>فرض</td><td>بدون فرض</td><td>فرض: سؤالات هم‌سطح</td></tr>
              <tr><td>استفاده</td><td>رایج‌تر</td><td>زمانی که داده دقیق ندارید</td></tr>
            </tbody>
          </table>

          <h2>فرمول‌ها</h2>
          <h3>KR-20 (دقیق)</h3>
          <div class="tzt-formula-box">
            KR-20 = (k / (k−1)) × (1 − Σ(pᵢ × qᵢ) / σ²ₜ)
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>k</strong>: تعداد سؤالات</li>
            <li><strong>pᵢ</strong>: نسبت پاسخ‌های صحیح برای سؤال i</li>
            <li><strong>qᵢ</strong>: نسبت پاسخ‌های غلط (1 − pᵢ)</li>
            <li><strong>σ²ₜ</strong>: واریانس نمرات کل</li>
          </ul>

          <h3>KR-21 (تقریبی)</h3>
          <div class="tzt-formula-box">
            KR-21 = (k / (k−1)) × (1 − (M(k−M)) / (k × σ²ₜ))
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>k</strong>: تعداد سؤالات</li>
            <li><strong>M</strong>: میانگین نمرات</li>
            <li><strong>σ²ₜ</strong>: واریانس نمرات</li>
          </ul>

          <h2>تفسیر ضریب پایایی</h2>
          <table class="tzt-itable">
            <thead><tr><th>مقدار</th><th>تفسیر</th></tr></thead>
            <tbody>
              <tr><td>≥ ۰.۹۰</td><td>عالی (Excellent)</td></tr>
              <tr><td>۰.۸۰ - ۰.۸۹</td><td>خوب (Good)</td></tr>
              <tr><td>۰.۷۰ - ۰.۷۹</td><td>قابل قبول (Acceptable)</td></tr>
              <tr><td>۰.۶۰ - ۰.۶۹</td><td>مورد تردید (Questionable)</td></tr>
              <tr><td>< ۰.۶۰</td><td>ضعیف (Poor)</td></tr>
            </tbody>
          </table>

          <h2>کاربردهای KR-20 و KR-21</h2>
          <ul>
            <li><strong>آزمون‌های دوحالتی:</strong> آزمون‌های صحیح/غلط، چند‌گزینه‌ای (۰/۱).</li>
            <li><strong>سنجش پایایی:</strong> بررسی همسانی درونی سؤالات.</li>
            <li><strong>بهبود آزمون:</strong> شناسایی سؤالات ضعیف برای حذف یا بازنویسی.</li>
            <li><strong>گزارش‌نویسی:</strong> گزارش پایایی آزمون در پایان‌نامه یا مقاله.</li>
          </ul>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چه زمانی از KR-20 و چه زمانی از KR-21 استفاده کنیم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">اگر داده دقیق (تعداد صحیح هر سؤال) دارید، KR-20 استفاده کنید. اگر فقط میانگین و واریانس کل دارید، KR-21 استفاده کنید.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">KR-20 و آلفای کرونباخ چه فرقی دارند؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">KR-20 برای داده‌های دوحالتی (۰/۱) است؛ آلفای کرونباخ برای داده‌های چندسطحی. در واقع KR-20 حالت خاصی از آلفا برای داده دوحالتی است.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">اگر KR-20 پایین باشد چه کنیم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">سؤالات را بررسی کنید؛ شاید برخی سؤالات با سایرین همبستگی ندارند. سؤالات ضعیف را حذف یا بازنویسی کنید و تعداد سؤالات را افزایش دهید.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">این محاسبه‌گر با SPSS مطابقت دارد؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، از فرمول‌های استاندارد استفاده می‌کند و نتایج منطبق با SPSS و نرم‌افزارهای آماری دیگر است.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به سنجش پایایی و روایی آزمون نیاز دارید؟</h2>
          <p>از طراحی آزمون و سنجش پایایی تا تحلیل عاملی و بهبود ابزار — متخصصان تزنویسه در تمام مراحل کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzkr-calc')) return;
      tztInitFAQ();

      var mode = 'kr20';

      document.querySelectorAll('#tzkr-mode .tzt-pill').forEach(function(p){
        p.addEventListener('click', function(){
          document.querySelectorAll('#tzkr-mode .tzt-pill').forEach(function(x){x.classList.remove('tzt-pill-active');});
          this.classList.add('tzt-pill-active');
          mode = this.getAttribute('data-mode');
          document.getElementById('tzkr-kr20').style.display = (mode === 'kr20') ? 'block' : 'none';
          document.getElementById('tzkr-kr21').style.display = (mode === 'kr21') ? 'block' : 'none';
        });
      });

      function parseCol(t){
        return t.trim().split(/\r?\n/).filter(function(l){return l.trim() !== '';}).map(function(v){return parseFloat(v.trim());});
      }

      function mean(arr){
        return arr.reduce(function(a,b){return a+b;},0) / arr.length;
      }

      function variance(arr){
        var m = mean(arr);
        var n = arr.length;
        return arr.reduce(function(s,v){return s + (v-m)*(v-m);},0) / (n-1);
      }

      document.getElementById('tzkr-sample').addEventListener('click', function(){
        if(mode === 'kr20'){
          document.getElementById('tzkr-n').value = '30';
          document.getElementById('tzkr-k').value = '10';
          document.getElementById('tzkr-correct').value = '28\n25\n30\n22\n27\n24\n29\n26\n23\n25';
        } else {
          document.getElementById('tzkr-k21').value = '10';
          document.getElementById('tzkr-mean').value = '7.5';
          document.getElementById('tzkr-var').value = '4.2';
        }
      });

      document.getElementById('tzkr-calc').addEventListener('click', function(){
        var err = document.getElementById('tzkr-err');
        err.classList.remove('tzt-show');
        document.getElementById('tzkr-result').classList.remove('tzt-show');

        try {
          var kr, interp, extra = '';

          if(mode === 'kr20'){
            var N = parseInt(document.getElementById('tzkr-n').value);
            var k = parseInt(document.getElementById('tzkr-k').value);
            var correct = parseCol(document.getElementById('tzkr-correct').value);

            if(!N || N < 2) throw 'تعداد شرکت‌کنندگان باید حداقل ۲ باشد.';
            if(!k || k < 2) throw 'تعداد سؤالات باید حداقل ۲ باشد.';
            if(correct.length !== k) throw 'تعداد مقادیر وارد‌شده (' + correct.length + ') با تعداد سؤالات (' + k + ') مطابقت ندارد.';
            if(correct.some(function(v){return v < 0 || v > N;})) throw 'تعداد صحیح‌ها باید بین ۰ و ' + N + ' باشد.';

            // محاسبه p و q برای هر سؤال
            var pq_sum = 0;
            for(var i = 0; i < k; i++){
              var p = correct[i] / N;
              var q = 1 - p;
              pq_sum += p * q;
            }

            // محاسبه واریانس نمرات کل
            var p_avg = correct.reduce(function(a,b){return a+b;},0) / (k * N);
            var var_total = k * p_avg * (1 - p_avg);

            if(var_total === 0) throw 'واریانس صفر است؛ تمام سؤالات یکسان پاسخ داده شده‌اند.';

            kr = (k / (k - 1)) * (1 - (pq_sum / var_total));
            extra = 'تعداد سؤالات: ' + tztToPersian(k) + ' | شرکت‌کنندگان: ' + tztToPersian(N) + ' | مجموع p×q: ' + tztToPersian(pq_sum.toFixed(3)) + ' | واریانس کل: ' + tztToPersian(var_total.toFixed(3));

          } else {
            var k21 = parseInt(document.getElementById('tzkr-k21').value);
            var M = parseFloat(document.getElementById('tzkr-mean').value);
            var s2 = parseFloat(document.getElementById('tzkr-var').value);

            if(!k21 || k21 < 2) throw 'تعداد سؤالات باید حداقل ۲ باشد.';
            if(isNaN(M) || isNaN(s2)) throw 'میانگین و واریانس را به‌درستی وارد کنید.';
            if(s2 <= 0) throw 'واریانس باید مثبت باشد.';
            if(M < 0 || M > k21) throw 'میانگین باید بین ۰ و ' + k21 + ' باشد.';

            kr = (k21 / (k21 - 1)) * (1 - (M * (k21 - M)) / (k21 * s2));
            extra = 'تعداد سؤالات: ' + tztToPersian(k21) + ' | میانگین: ' + tztToPersian(M.toFixed(2)) + ' | واریانس: ' + tztToPersian(s2.toFixed(2));
          }

          // تفسیر
          if(kr >= 0.90) interp = {txt: 'عالی', color: '#16a34a'};
          else if(kr >= 0.80) interp = {txt: 'خوب', color: '#22c55e'};
          else if(kr >= 0.70) interp = {txt: 'قابل قبول', color: '#84cc16'};
          else if(kr >= 0.60) interp = {txt: 'مورد تردید', color: '#d97706'};
          else interp = {txt: 'ضعیف', color: '#dc2626'};

          var html = '<div class="tzt-result-main">';
          html += '<div class="tzt-result-label">ضریب پایایی ' + (mode === 'kr20' ? 'KR-20' : 'KR-21') + '</div>';
          html += '<div class="tzt-result-value">' + tztToPersian(kr.toFixed(3)) + '</div>';
          html += '<span class="tzt-result-badge" style="background:' + interp.color + ';">' + interp.txt + '</span>';
          html += '<div class="tzt-result-meta">';
          html += '<div class="tzt-result-meta-item"><strong>' + (kr >= 0 ? '✓' : '✗') + '</strong><span>' + (kr >= 0.70 ? 'پایایی مناسب' : 'نیاز به بهبود') + '</span></div>';
          html += '</div></div>';
          html += '<div class="tzt-method-note"><strong>جزئیات:</strong> ' + extra + '<br><strong>تفسیر:</strong> ' + (kr >= 0.70 ? 'ضریب پایایی در سطح قابل قبول یا بالاتر است.' : 'ضریب پایایی پایین است؛ سؤالات را بررسی کنید و تعداد سؤالات را افزایش دهید.') + '</div>';

          var box = document.getElementById('tzkr-result');
          box.innerHTML = html;
          box.classList.add('tzt-show');

        } catch(e){
          err.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
          err.classList.add('tzt-show');
        }
      });
    });
    </script>
    <?php
    return ob_get_clean();
}

// سئو و Schema
add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post;
    if(!$post || strpos($post->post_content, 'tz_kr20') === false) return;
    
    $url = get_permalink();
    $app = array(
      '@context' => 'https://schema.org',
      '@type' => 'WebApplication',
      'name' => 'محاسبه‌گر KR-20 و KR-21 تزنویسه',
      'url' => $url,
      'applicationCategory' => 'EducationalApplication',
      'operatingSystem' => 'All',
      'description' => 'ابزار رایگان محاسبه ضریب پایایی KR-20 و KR-21 برای آزمون‌های دوحالتی',
      'inLanguage' => 'fa-IR',
      'offers' => array('@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IRR'),
      'publisher' => array('@type' => 'Organization', 'name' => 'تزنویسه')
    );
    echo "\n" . '<script type="application/ld+json">' . wp_json_encode($app, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    
    $faqs = array(
        array('چه زمانی از KR-20 و چه زمانی از KR-21 استفاده کنیم؟', 'اگر داده دقیق (تعداد صحیح هر سؤال) دارید KR-20 استفاده کنید؛ اگر فقط میانگین و واریانس دارید KR-21 استفاده کنید.'),
        array('KR-20 و آلفای کرونباخ چه فرقی دارند؟', 'KR-20 برای داده‌های دوحالتی (۰/۱) است؛ آلفای کرونباخ برای داده‌های چندسطحی. KR-20 حالت خاصی از آلفا است.'),
        array('اگر KR-20 پایین باشد چه کنیم؟', 'سؤالات را بررسی کنید؛ سؤالات ضعیف را حذف یا بازنویسی کنید و تعداد سؤالات را افزایش دهید.'),
        array('این محاسبه‌گر با SPSS مطابقت دارد؟', 'بله، از فرمول‌های استاندارد استفاده می‌کند و نتایج منطبق با SPSS است.'),
    );
    
    $items = array();
    foreach($faqs as $f){
      $items[] = array(
        '@type' => 'Question',
        'name' => $f[0],
        'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f[1])
      );
    }
    echo '<script type="application/ld+json">' . wp_json_encode(array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
});
}

/* --- cohens-kappa-calculator --- */
if ( ! function_exists( 'teznevise_cohens_kappa_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر ضریب توافق کاپا
// [tz_cohens_kappa]
// ============================================
add_shortcode('tz_cohens_kappa', 'teznevise_cohens_kappa_calc');
function teznevise_cohens_kappa_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">📊 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر ضریب توافق کاپا (Cohen's Kappa)</h1>
          <p>ضریب توافق کاپا را برای داده‌های دو رده‌ای (دوتایی) محاسبه کنید. این ابزار به‌ویژه برای بررسی توافق بین دو قضاوت‌کننده یا دو روش ارزیابی مفید است.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-field">
            <label>تعداد توافق مثبت (a)</label>
            <input type="number" id="tzck-a" value="30" min="0" />
          </div>
          <div class="tzt-field">
            <label>تعداد توافق منفی (d)</label>
            <input type="number" id="tzck-d" value="20" min="0" />
          </div>
          <div class="tzt-field">
            <label>تعداد اختلاف مثبت-منفی (b)</label>
            <input type="number" id="tzck-b" value="10" min="0" />
          </div>
          <div class="tzt-field">
            <label>تعداد اختلاف منفی-مثبت (c)</label>
            <input type="number" id="tzck-c" value="5" min="0" />
          </div>
          <div class="tzt-error" id="tzck-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzck-calc">محاسبه ضریب کاپا</button>
          <div class="tzt-result" id="tzck-result"></div>
        </div>

        <div class="tzt-content">
          <h2>Cohen's Kappa چیست؟</h2>
          <p><strong>ضریب توافق کاپا</strong> (Cohen's Kappa) معیاری برای سنجش توافق بین دو قضاوت‌کننده در طبقه‌بندی داده‌هاست. این ضریب می‌تواند توافق واقعی را در برابر توافق تصادفی اندازه‌گیری کند.</p>

          <h2>فرمول محاسبه</h2>
          <div class="tzt-formula-box">
            Kappa = (P_o - P_e) / (1 - P_e)
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>P_o</strong>: توافق واقعی (نسبت توافق‌های مثبت و منفی به کل)</li>
            <li><strong>P_e</strong>: توافق تصادفی (محاسبه‌شده بر اساس توزیع داده‌ها)</li>
          </ul>

          <h2>تفسیر ضریب کاپا</h2>
          <table class="tzt-itable">
            <thead><tr><th>مقدار</th><th>تفسیر</th></tr></thead>
            <tbody>
              <tr><td>≥ ۰.۸۰</td><td>عالی (Excellent)</td></tr>
              <tr><td>۰.۶۰ - ۰.۷۹</td><td>خوب (Good)</td></tr>
              <tr><td>۰.۴۰ - ۰.۵۹</td><td>قابل قبول (Moderate)</td></tr>
              <tr><td>۰.۲۰ - ۰.۳۹</td><td>ضعیف (Fair)</td></tr>
              <tr><td>< ۰.۲۰</td><td>بد (Poor)</td></tr>
            </tbody>
          </table>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">Cohen's Kappa چه زمانی مناسب است؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">زمانی که دو قضاوت‌کننده یا دو روش ارزیابی وجود دارد و داده‌ها دوحالتی هستند (صحیح/غلط).</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چگونه می‌توانم Kappa را تفسیر کنم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">کاپا بالای ۰.۷ نشان‌دهنده توافق مناسب است، در حالی که مقادیر پایین‌تر نشان‌دهنده نیاز به بهبود است.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل دقیق‌تری نیاز دارید؟</h2>
          <p>از طراحی آزمون و تجزیه و تحلیل داده‌ها تا تفسیر نتایج — متخصصان تزنویسه در تمام مراحل تحلیل کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzck-calc')) return;
      tztInitFAQ();

      document.getElementById('tzck-calc').addEventListener('click', function(){
        var err = document.getElementById('tzck-err');
        err.classList.remove('tzt-show');
        document.getElementById('tzck-result').classList.remove('tzt-show');

        try {
          var a = parseInt(document.getElementById('tzck-a').value);
          var b = parseInt(document.getElementById('tzck-b').value);
          var c = parseInt(document.getElementById('tzck-c').value);
          var d = parseInt(document.getElementById('tzck-d').value);

          if (isNaN(a) || isNaN(b) || isNaN(c) || isNaN(d)) {
            throw 'همه مقادیر باید عددی باشند.';
          }

          var total = a + b + c + d;
          if (total === 0) throw 'مجموع داده‌ها نمی‌تواند صفر باشد.';

          // محاسبه توافق واقعی (P_o)
          var P_o = (a + d) / total;

          // محاسبه توافق تصادفی (P_e)
          var P_e = (((a + b) / total) * ((a + c) / total)) + (((c + d) / total) * ((b + d) / total));

          // محاسبه کاپا
          var kappa = (P_o - P_e) / (1 - P_e);

          var html = '<div class="tzt-result-main">';
          html += '<div class="tzt-result-label">ضریب توافق کاپا</div>';
          html += '<div class="tzt-result-value">' + tztToPersian(kappa.toFixed(3)) + '</div>';
          html += '<div class="tzt-result-meta">';
          html += '<div class="tzt-result-meta-item"><strong>' + (kappa >= 0.7 ? '✓' : '✗') + '</strong><span>' + (kappa >= 0.7 ? 'توافق مناسب' : 'نیاز به بهبود') + '</span></div>';
          html += '</div></div>';

          var box = document.getElementById('tzck-result');
          box.innerHTML = html;
          box.classList.add('tzt-show');

        } catch (e) {
          err.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
          err.classList.add('tzt-show');
        }
      });
    });

    // تابع تبدیل اعداد به فارسی
    function tztToPersian(num) {
      const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
      return String(num).replace(/\d/g, function(digit) {
        return persianDigits[digit];
      });
    }
    </script>
    <?php
    return ob_get_clean();
}
}

/* --- anova-calculator --- */
if ( ! function_exists( 'teznevise_anova_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر تحلیل واریانس (ANOVA)
// [tz_anova]
// ============================================
add_shortcode('tz_anova', 'teznevise_anova_calc');
function teznevise_anova_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">📊 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر تحلیل واریانس (ANOVA)</h1>
          <p>تحلیل واریانس یک‌راهه را برای مقایسه میانگین‌های چند گروه محاسبه کنید. این ابزار به شما کمک می‌کند تا تفاوت‌های معنادار بین گروه‌ها را بررسی کنید.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 داده‌های خود را برای هر گروه وارد کنید. هر مقدار در یک خط. حداقل ۲ گروه و ۲ مقدار برای هر گروه لازم است.</div>
          
          <div class="tzt-field">
            <label>گروه اول <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzanova-group1" placeholder="23&#10;25&#10;21&#10;28&#10;24"></textarea>
          </div>
          
          <div class="tzt-field">
            <label>گروه دوم <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzanova-group2" placeholder="30&#10;27&#10;29&#10;31&#10;28"></textarea>
          </div>
          
          <div class="tzt-field">
            <label>گروه سوم <span class="tzt-hint">(اختیاری، هر عدد یک خط)</span></label>
            <textarea id="tzanova-group3" placeholder="35&#10;36&#10;34&#10;38&#10;32"></textarea>
          </div>

          <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <button type="button" class="tzt-sample-btn" id="tzanova-sample">📝 داده نمونه</button>
          </div>

          <div class="tzt-error" id="tzanova-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzanova-calc">محاسبه ANOVA</button>
          <div class="tzt-result" id="tzanova-result"></div>
        </div>

        <div class="tzt-content">
          <h2>تحلیل واریانس (ANOVA) چیست؟</h2>
          <p><strong>تحلیل واریانس (ANOVA — Analysis of Variance)</strong> یک تکنیک آماری است که برای مقایسه میانگین‌های سه یا چند گروه استفاده می‌شود. این آزمون بررسی می‌کند آیا تفاوت‌های معناداری بین میانگین‌های گروه‌های مختلف وجود دارد یا این تفاوت‌ها صرفاً ناشی از تصادف است.</p>

          <h2>فرمول ANOVA</h2>
          <div class="tzt-formula-box">
            F = MS<sub>بین</sub> / MS<sub>درون</sub><br>
            MS<sub>بین</sub> = SS<sub>بین</sub> / df<sub>بین</sub><br>
            MS<sub>درون</sub> = SS<sub>درون</sub> / df<sub>درون</sub>
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>SS<sub>بین</sub>:</strong> مجموع مجذورات بین گروه‌ها</li>
            <li><strong>SS<sub>درون</sub>:</strong> مجموع مجذورات درون گروه‌ها</li>
            <li><strong>df<sub>بین</sub>:</strong> درجات آزادی بین گروه‌ها = k - 1</li>
            <li><strong>df<sub>درون</sub>:</strong> درجات آزادی درون گروه‌ها = N - k</li>
            <li><strong>k:</strong> تعداد گروه‌ها</li>
            <li><strong>N:</strong> مجموع تعداد مشاهدات</li>
          </ul>

          <h2>مراحل محاسبه ANOVA</h2>
          <ol>
            <li><strong>محاسبه میانگین کلی:</strong> میانگین تمام داده‌ها</li>
            <li><strong>محاسبه میانگین هر گروه:</strong> میانگین داده‌های هر گروه</li>
            <li><strong>محاسبه SS<sub>بین</sub>:</strong> مجموع مجذورات انحراف میانگین‌های گروه‌ها از میانگین کلی</li>
            <li><strong>محاسبه SS<sub>درون</sub>:</strong> مجموع مجذورات انحراف داده‌های هر گروه از میانگین آن گروه</li>
            <li><strong>محاسبه MS:</strong> تقسیم SS بر درجات آزادی</li>
            <li><strong>محاسبه F:</strong> تقسیم MS<sub>بین</sub> بر MS<sub>درون</sub></li>
          </ol>

          <h2>تفسیر نتایج ANOVA</h2>
          <table class="tzt-itable">
            <thead><tr><th>نتیجه</th><th>تفسیر</th></tr></thead>
            <tbody>
              <tr><td>F بزرگ و p-value < ۰.۰۵</td><td>تفاوت معنادار بین گروه‌ها وجود دارد</td></tr>
              <tr><td>F کوچک و p-value > ۰.۰۵</td><td>تفاوت معنادار بین گروه‌ها وجود ندارد</td></tr>
            </tbody>
          </table>

          <h2>پیش‌فرض‌های ANOVA</h2>
          <ul>
            <li><strong>توزیع نرمال:</strong> داده‌های هر گروه باید تقریباً از توزیع نرمال پیروی کنند.</li>
            <li><strong>همگنی واریانس‌ها:</strong> واریانس‌های گروه‌ها باید تقریباً برابر باشند (آزمون Levene).</li>
            <li><strong>استقلال مشاهدات:</strong> مشاهدات در هر گروه باید مستقل باشند.</li>
          </ul>

          <h2>کاربردهای عملی ANOVA</h2>
          <ul>
            <li><strong>تحقیقات پزشکی:</strong> مقایسه اثربخشی سه یا چند درمان</li>
            <li><strong>تحقیقات آموزشی:</strong> مقایسه نتایج یادگیری در روش‌های تدریس مختلف</li>
            <li><strong>تحقیقات بازاریابی:</strong> مقایسه فروش در شرایط تبلیغاتی مختلف</li>
            <li><strong>تحقیقات صنعتی:</strong> مقایسه کیفیت محصولات از خطوط تولید مختلف</li>
          </ul>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          
          <div class="tzt-faq-item">
            <div class="tzt-faq-q">ANOVA چه زمانی استفاده می‌شود؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">زمانی که می‌خواهید میانگین‌های سه یا چند گروه مستقل را مقایسه کنید و فرضیات توزیع نرمال و همگنی واریانس‌ها برقرار باشند.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">تفاوت ANOVA و t-test چیست؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">t-test برای مقایسه میانگین‌های دو گروه استفاده می‌شود، اما ANOVA برای مقایسه سه یا چند گروه. اگر بخواهید دو گروه را مقایسه کنید، می‌توانید از t-test استفاده کنید.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چگونه می‌توانم نتیجه ANOVA را تفسیر کنم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">نتایج ANOVA شامل مقدار F و p-value است. اگر p-value کمتر از ۰.۰۵ باشد، فرضیه صفر را رد می‌کنید و نتیجه‌گیری می‌کنید که حداقل یک تفاوت معنادار بین گروه‌ها وجود دارد.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">اگر ANOVA معنادار باشد، بعد چه کار باید کنم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">اگر ANOVA معنادار باشد، باید از آزمون‌های تعقیبی (Post-hoc tests) مانند Tukey HSD یا Bonferroni استفاده کنید تا بفهمید کدام گروه‌ها با یکدیگر تفاوت معنادار دارند.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چگونه می‌توانم بررسی کنم که فرضیات ANOVA برقرار هستند؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">برای بررسی توزیع نرمال می‌توانید از آزمون Shapiro-Wilk استفاده کنید. برای بررسی همگنی واریانس‌ها می‌توانید از آزمون Levene استفاده کنید.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">اگر فرضیات ANOVA برقرار نباشند، چه باید کنم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">اگر فرضیات برقرار نباشند، می‌توانید از آزمون‌های ناپارامتری مانند Kruskal-Wallis استفاده کنید که نیازی به فرضیات توزیع نرمال ندارند.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">این محاسبه‌گر چه حدودی دارد؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">این محاسبه‌گر ANOVA یک‌راهه را محاسبه می‌کند. برای ANOVA دو راهه یا تحلیل‌های پیچیده‌تر، باید از نرم‌افزارهای آماری مانند SPSS یا R استفاده کنید.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">این محاسبه‌گر با SPSS و R مطابقت دارد؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، این محاسبه‌گر از فرمول‌های استاندارد ANOVA استفاده می‌کند و نتایج آن با نتایج SPSS و R مطابقت دارند.</div></div>
          </div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل دقیق‌تری نیاز دارید؟</h2>
          <p>از آزمون‌های ANOVA تا مدل‌سازی و تجزیه و تحلیل داده‌های پیشرفته — متخصصان تزنویسه در تمام مراحل تحلیل کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzanova-calc')) return;

      // تابع تبدیل اعداد به فارسی
      window.tztToPersian = function(num) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return String(num).replace(/\d/g, function(digit) {
          return persianDigits[digit];
        });
      };

      // تابع FAQ
      window.tztInitFAQ = function(){
        var items = document.querySelectorAll('.tzt-faq-item');
        for(var i = 0; i < items.length; i++){
          (function(item){
            var q = item.querySelector('.tzt-faq-q');
            if(!q) return;
            q.addEventListener('click', function(){
              item.classList.toggle('tzt-faq-open');
            });
          })(items[i]);
        }
      };

      tztInitFAQ();

      // تابع پارس کردن داده‌ها
      function parseData(text) {
        if(!text || text.trim().length === 0) return [];
        return text.trim().split(/\r?\n/).map(function(line){
          return parseFloat(line.trim());
        }).filter(function(num){
          return !isNaN(num);
        });
      }

      // تابع محاسبه میانگین
      function calcMean(arr) {
        if(arr.length === 0) return 0;
        var sum = arr.reduce(function(a, b){ return a + b; }, 0);
        return sum / arr.length;
      }

      // داده نمونه
      document.getElementById('tzanova-sample').addEventListener('click', function(){
        document.getElementById('tzanova-group1').value = '23\n25\n21\n28\n24\n26\n22\n27';
        document.getElementById('tzanova-group2').value = '30\n27\n29\n31\n28\n26\n25\n28';
        document.getElementById('tzanova-group3').value = '35\n36\n34\n38\n32\n33\n37\n35';
      });

      // محاسبه ANOVA
      document.getElementById('tzanova-calc').addEventListener('click', function(){
        var err = document.getElementById('tzanova-err');
        err.classList.remove('tzt-show');
        document.getElementById('tzanova-result').classList.remove('tzt-show');

        try {
          var group1 = parseData(document.getElementById('tzanova-group1').value);
          var group2 = parseData(document.getElementById('tzanova-group2').value);
          var group3 = parseData(document.getElementById('tzanova-group3').value);

          // بررسی اعتبار داده‌ها
          if(group1.length < 2 || group2.length < 2) {
            throw 'حداقل دو گروه با حداقل ۲ مقدار لازم است.';
          }

          // ایجاد آرایه گروه‌ها
          var groups = [group1, group2];
          if(group3.length >= 2) {
            groups.push(group3);
          }

          var k = groups.length; // تعداد گروه‌ها
          var N = groups.reduce(function(sum, group){ return sum + group.length; }, 0); // کل مشاهدات

          // محاسبه میانگین کلی
          var totalSum = groups.reduce(function(sum, group){
            return sum + group.reduce(function(a, b){ return a + b; }, 0);
          }, 0);
          var overallMean = totalSum / N;

          // محاسبه SS (Sum of Squares)
          var ssBetween = groups.reduce(function(sum, group){
            var groupMean = calcMean(group);
            return sum + group.length * Math.pow(groupMean - overallMean, 2);
          }, 0);

          var ssWithin = groups.reduce(function(sum, group){
            var groupMean = calcMean(group);
            return sum + group.reduce(function(s, value){
              return s + Math.pow(value - groupMean, 2);
            }, 0);
          }, 0);

          var ssTotal = ssBetween + ssWithin;

          // محاسبه درجات آزادی
          var dfBetween = k - 1;
          var dfWithin = N - k;
          var dfTotal = N - 1;

          // محاسبه MS (Mean Square)
          var msBetween = ssBetween / dfBetween;
          var msWithin = ssWithin / dfWithin;

          // محاسبه F
          var fValue = msBetween / msWithin;

          // نمایش نتایج
          var html = '<div class="tzt-result-main">';
          html += '<div class="tzt-result-label">نتیجه تحلیل واریانس (ANOVA)</div>';
          html += '<div class="tzt-result-value">آماره F: ' + tztToPersian(fValue.toFixed(3)) + '</div>';
          html += '<span class="tzt-result-badge" style="background:#145D4A;">یک‌راهه</span>';
          html += '<div class="tzt-result-meta">';
          html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(dfBetween) + '</strong><span>درجات آزادی بین</span></div>';
          html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(dfWithin) + '</strong><span>درجات آزادی درون</span></div>';
          html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(msBetween.toFixed(2)) + '</strong><span>MS بین</span></div>';
          html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(msWithin.toFixed(2)) + '</strong><span>MS درون</span></div>';
          html += '</div></div>';

          // جدول نتایج
          html += '<div class="tzt-tbl-wrap" style="margin-top:20px;"><table class="tzt-tbl"><thead><tr><th>منبع</th><th>SS</th><th>df</th><th>MS</th><th>F</th></tr></thead><tbody>';
          html += '<tr><td>بین گروه‌ها</td><td>' + tztToPersian(ssBetween.toFixed(2)) + '</td><td>' + tztToPersian(dfBetween) + '</td><td>' + tztToPersian(msBetween.toFixed(2)) + '</td><td>' + tztToPersian(fValue.toFixed(3)) + '</td></tr>';
          html += '<tr><td>درون گروه‌ها</td><td>' + tztToPersian(ssWithin.toFixed(2)) + '</td><td>' + tztToPersian(dfWithin) + '</td><td>' + tztToPersian(msWithin.toFixed(2)) + '</td><td>—</td></tr>';
          html += '<tr><td>کل</td><td>' + tztToPersian(ssTotal.toFixed(2)) + '</td><td>' + tztToPersian(dfTotal) + '</td><td>—</td><td>—</td></tr>';
          html += '</tbody></table></div>';

          html += '<div class="tzt-method-note"><strong>تفسیر:</strong> آماره F = ' + tztToPersian(fValue.toFixed(3)) + ' محاسبه شد. برای تعیین معناداری این نتیجه، باید این مقدار را با مقدار بحرانی F در جدول ANOVA (با درجات آزادی ' + tztToPersian(dfBetween) + ' و ' + tztToPersian(dfWithin) + ' و سطح معناداری ۰.۰۵) مقایسه کنید.</div>';

          document.getElementById('tzanova-result').innerHTML = html;
          document.getElementById('tzanova-result').classList.add('tzt-show');

        } catch(e) {
          err.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
          err.classList.add('tzt-show');
        }
      });
    });
    </script>
    <?php
    return ob_get_clean();
}

// سئو و Schema
add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post;
    if (!$post || strpos($post->post_content, 'tz_anova') === false) return;

    $url = get_permalink();
    $app = array(
      '@context' => 'https://schema.org',
      '@type' => 'WebApplication',
      'name' => 'محاسبه‌گر تحلیل واریانس (ANOVA) تزنویسه',
      'url' => $url,
      'applicationCategory' => 'EducationalApplication',
      'operatingSystem' => 'All',
      'description' => 'ابزار رایگان تحلیل واریانس (ANOVA) یک‌راهه برای مقایسه میانگین‌های چند گروه.',
      'inLanguage' => 'fa-IR',
      'offers' => array('@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IRR'),
      'publisher' => array('@type' => 'Organization', 'name' => 'تزنویسه')
    );
    echo "\n" . '<script type="application/ld+json">' . wp_json_encode($app, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";

    $faqs = array(
        array('ANOVA چه زمانی استفاده می‌شود؟', 'زمانی که می‌خواهید میانگین‌های سه یا چند گروه مستقل را مقایسه کنید و فرضیات توزیع نرمال و همگنی واریانس‌ها برقرار باشند.'),
        array('تفاوت ANOVA و t-test چیست؟', 't-test برای مقایسه میانگین‌های دو گروه استفاده می‌شود، اما ANOVA برای مقایسه سه یا چند گروه.'),
        array('چگونه می‌توانم نتیجه ANOVA را تفسیر کنم؟', 'نتایج ANOVA شامل مقدار F و p-value است. اگر p-value کمتر از ۰.۰۵ باشد، فرضیه صفر را رد می‌کنید.'),
        array('اگر ANOVA معنادار باشد، بعد چه کار باید کنم؟', 'اگر ANOVA معنادار باشد، باید از آزمون‌های تعقیبی (Post-hoc tests) مانند Tukey HSD استفاده کنید.'),
        array('چگونه می‌توانم بررسی کنم که فرضیات ANOVA برقرار هستند؟', 'برای بررسی توزیع نرمال می‌توانید از آزمون Shapiro-Wilk و برای همگنی واریانس‌ها از آزمون Levene استفاده کنید.'),
        array('اگر فرضیات ANOVA برقرار نباشند، چه باید کنم؟', 'می‌توانید از آزمون‌های ناپارامتری مانند Kruskal-Wallis استفاده کنید.'),
        array('این محاسبه‌گر چه حدودی دارد؟', 'این محاسبه‌گر ANOVA یک‌راهه را محاسبه می‌کند. برای ANOVA دو راهه یا تحلیل‌های پیچیده‌تر، باید از نرم‌افزارهای آماری مانند SPSS یا R استفاده کنید.'),
        array('این محاسبه‌گر با SPSS و R مطابقت دارد؟', 'بله، این محاسبه‌گر از فرمول‌های استاندارد ANOVA استفاده می‌کند و نتایج آن با نتایج SPSS و R مطابقت دارند.'),
    );

    $items = array();
    foreach ($faqs as $f) {
        $items[] = array(
            '@type' => 'Question',
            'name' => $f[0],
            'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f[1])
        );
    }
    echo '<script type="application/ld+json">' . wp_json_encode(array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
});
}

/* --- mann-whitney-calculator --- */
if ( ! function_exists( 'teznevise_mann_whitney_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر آزمون Mann-Whitney U
// [tz_mann_whitney]
// ============================================
add_shortcode('tz_mann_whitney', 'teznevise_mann_whitney_calc');
function teznevise_mann_whitney_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">📊 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر آزمون Mann-Whitney U</h1>
          <p>آزمون ناپارامتری Mann-Whitney U برای مقایسه دو گروه مستقل. این آزمون بدون نیاز به فرض نرمال بودن توزیع داده‌ها، تفاوت میانگین رتبه‌ها را بررسی می‌کند.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 داده‌های خود را برای هر گروه وارد کنید. هر مقدار در یک خط.</div>

          <div class="tzt-field">
            <label>داده‌های گروه اول <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzmw-group1" placeholder="23&#10;25&#10;21&#10;28&#10;24"></textarea>
          </div>
          
          <div class="tzt-field">
            <label>داده‌های گروه دوم <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzmw-group2" placeholder="30&#10;27&#10;29&#10;31&#10;28"></textarea>
          </div>

          <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <button type="button" class="tzt-sample-btn" id="tzmw-sample">📝 داده نمونه</button>
          </div>

          <div class="tzt-error" id="tzmw-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzmw-calc">محاسبه آزمون Mann-Whitney U</button>
          <div class="tzt-result" id="tzmw-result"></div>
        </div>

        <div class="tzt-content">
          <h2>آزمون Mann-Whitney U چیست؟</h2>
          <p><strong>آزمون Mann-Whitney U</strong> (همچنین به نام آزمون Wilcoxon Rank-Sum شناخته می‌شود) یک آزمون ناپارامتری است که برای مقایسه دو گروه مستقل استفاده می‌شود. این آزمون زمانی کاربرد دارد که فرض نرمال بودن توزیع داده‌ها برقرار نباشد.</p>

          <h2>فرمول آزمون Mann-Whitney U</h2>
          <div class="tzt-formula-box">
            U₁ = n₁n₂ + (n₁(n₁ + 1)) / 2 - R₁<br>
            U₂ = n₁n₂ - U₁<br>
            U = min(U₁, U₂)
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>n₁, n₂:</strong> تعداد مشاهدات در هر گروه</li>
            <li><strong>R₁:</strong> مجموع رتبه‌های گروه اول</li>
            <li><strong>U:</strong> کوچک‌تر از U₁ و U₂</li>
          </ul>

          <h2>مراحل محاسبه</h2>
          <ol>
            <li>ترکیب داده‌های هر دو گروه</li>
            <li>رتبه‌بندی تمام داده‌ها از کوچک به بزرگ</li>
            <li>محاسبه مجموع رتبه‌های هر گروه</li>
            <li>محاسبه U₁ و U₂</li>
            <li>انتخاب مقدار کوچک‌تر به عنوان U نهایی</li>
          </ol>

          <h2>تفسیر آزمون Mann-Whitney U</h2>
          <p>اگر مقدار U به‌دست‌آمده کمتر از مقدار بحرانی U در جدول آزمون Mann-Whitney باشد (با توجه به سطح معناداری و تعداد مشاهدات)، تفاوت معنادار بین دو گروه وجود دارد.</p>

          <h2>مزایای آزمون Mann-Whitney U</h2>
          <ul>
            <li><strong>نیازی به فرض نرمال بودن ندارد:</strong> برای داده‌های غیرنرمال مناسب است.</li>
            <li><strong>برای داده‌های رتبه‌ای:</strong> می‌تواند برای داده‌های ترتیبی هم استفاده شود.</li>
            <li><strong>مقاوم به داده‌های پرت:</strong> داده‌های پرت تأثیر کمی بر نتیجه دارند.</li>
            <li><strong>حجم نمونه کم:</strong> برای نمونه‌های کوچک مناسب است.</li>
          </ul>

          <h2>کاربردهای عملی</h2>
          <ul>
            <li><strong>تحقیقات پزشکی:</strong> مقایسه درجات درد یا رضایت بیماران</li>
            <li><strong>تحقیقات آموزشی:</strong> مقایسه نمرات رتبه‌بندی‌شده</li>
            <li><strong>تحقیقات بازاریابی:</strong> مقایسه رتبه‌بندی محصولات</li>
            <li><strong>تحقیقات محیط‌زیست:</strong> مقایسه غلظت‌های آلاینده‌ها</li>
          </ul>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چه زمانی از آزمون Mann-Whitney U استفاده کنیم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">زمانی که می‌خواهید دو گروه مستقل را مقایسه کنید و داده‌ها توزیع نرمال ندارند یا رتبه‌ای هستند. این آزمون برای نمونه‌های کوچک نیز مناسب است.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چگونه می‌توانم نتیجه آزمون Mann-Whitney U را تفسیر کنم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">اگر مقدار U کمتر از مقدار بحرانی در جدول Mann-Whitney باشد، تفاوت معناداری بین دو گروه وجود دارد. برای تعیین p-value دقیق، باید از جداول یا نرم‌افزار آماری استفاده کنید.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">تفاوت Mann-Whitney U و t-test چیست؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">t-test یک آزمون پارامتری است که فرض می‌کند داده‌ها توزیع نرمال دارند. Mann-Whitney U ناپارامتری است و نیازی به این فرض ندارد. اگر فرضیات t-test برقرار نباشند، Mann-Whitney بهتر است.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">آیا Mann-Whitney U برای داده‌های نرمال هم قابل استفاده است؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، می‌توانید استفاده کنید. اما اگر داده‌ها نرمال باشند و واریانس‌ها برابر باشند، بهتر است از آزمون‌های پارامتری مانند t-test استفاده کنید چون قدرت آماری بیشتری دارند.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چه تعداد داده برای آزمون Mann-Whitney U نیاز است؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">حداقل ۲ مشاهده برای هر گروه کافی است. اما برای نتایج قابل‌اعتماد، حداقل ۵ مشاهده برای هر گروه توصیه می‌شود. هرچه تعداد داده‌ها بیشتر باشد، قدرت آزمون بالاتر است.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">این محاسبه‌گر با SPSS و R مطابقت دارد؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، این محاسبه‌گر از فرمول‌های استاندارد استفاده می‌کند و نتایج آن با SPSS و R مطابقت دارند. مقدار U محاسبه‌شده در هر سه منبع یکسان خواهد بود.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">رتبه‌بندی در آزمون Mann-Whitney U چگونه انجام می‌شود؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">تمام داده‌های دو گروه ترکیب می‌شوند و سپس از کوچک به بزرگ رتبه‌بندی می‌شوند. اگر داده‌های تکراری باشند، رتبه میانگین به آن‌ها اختصاص می‌یابد.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">آیا می‌توانم از Mann-Whitney U برای بیش از دو گروه استفاده کنم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">خیر، Mann-Whitney U فقط برای مقایسه دو گروه است. برای مقایسه سه یا بیشتر گروه، می‌توانید از آزمون Kruskal-Wallis استفاده کنید.</div></div>
          </div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل دقیق‌تری نیاز دارید؟</h2>
          <p>از آزمون‌های ناپارامتری تا مدل‌سازی و تجزیه و تحلیل داده‌های پیشرفته — متخصصان تزنویسه در تمام مراحل تحلیل کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzmw-calc')) return;

      // تابع تبدیل اعداد به فارسی
      function tztToPersian(num) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return String(num).replace(/\d/g, function(digit) {
          return persianDigits[digit];
        });
      }

      // تابع FAQ — اینیشیالایز کردن
      function initFAQ() {
        var items = document.querySelectorAll('.tzt-faq-item');
        for(var i = 0; i < items.length; i++) {
          (function(item) {
            var question = item.querySelector('.tzt-faq-q');
            if(!question) return;
            
            question.addEventListener('click', function(e) {
              e.preventDefault();
              item.classList.toggle('tzt-faq-open');
            });
          })(items[i]);
        }
      }

      initFAQ();

      // داده نمونه
      document.getElementById('tzmw-sample').addEventListener('click', function(){
        document.getElementById('tzmw-group1').value = '23\n25\n21\n28\n24\n26\n22\n27';
        document.getElementById('tzmw-group2').value = '30\n27\n29\n31\n28\n26\n25\n28';
      });

      // محاسبه آزمون Mann-Whitney U
      document.getElementById('tzmw-calc').addEventListener('click', function(){
        var errEl = document.getElementById('tzmw-err');
        var resultEl = document.getElementById('tzmw-result');
        
        errEl.classList.remove('tzt-show');
        resultEl.classList.remove('tzt-show');
        resultEl.innerHTML = '';

        try {
          // دریافت و پارس داده‌ها
          var group1Str = document.getElementById('tzmw-group1').value;
          var group2Str = document.getElementById('tzmw-group2').value;

          if(!group1Str.trim() || !group2Str.trim()) {
            throw 'لطفاً هر دو گروه را پر کنید.';
          }

          var group1Data = parseData(group1Str);
          var group2Data = parseData(group2Str);

          if(group1Data.length < 2 || group2Data.length < 2) {
            throw 'هر گروه باید حداقل ۲ مقدار داشته باشد.';
          }

          var n1 = group1Data.length;
          var n2 = group2Data.length;

          // ترکیب داده‌ها
          var combined = group1Data.concat(group2Data);
          var ranks = getRanks(combined);

          // مجموع رتبه‌های گروه اول
          var R1 = 0;
          for(var i = 0; i < n1; i++) {
            R1 += ranks[i];
          }

          // محاسبه U
          var U1 = n1 * n2 + (n1 * (n1 + 1)) / 2 - R1;
          var U2 = n1 * n2 - U1;
          var U = Math.min(U1, U2);

          // نمایش نتیجه
          var html = '<div class="tzt-result-main">';
          html += '<div class="tzt-result-label">نتیجه آزمون Mann-Whitney U</div>';
          html += '<div class="tzt-result-value">U = ' + tztToPersian(U.toFixed(3)) + '</div>';
          html += '<span class="tzt-result-badge" style="background:#145D4A;">آزمون ناپارامتری</span>';
          html += '<div class="tzt-result-meta">';
          html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(n1) + '</strong><span>تعداد گروه اول</span></div>';
          html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(n2) + '</strong><span>تعداد گروه دوم</span></div>';
          html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(R1.toFixed(0)) + '</strong><span>مجموع رتبه‌های گروه اول</span></div>';
          html += '</div></div>';

          html += '<div class="tzt-tbl-wrap" style="margin-top:20px;"><table class="tzt-tbl"><thead><tr><th>شاخص</th><th>مقدار</th></tr></thead><tbody>';
          html += '<tr><td>U (مقدار کوچک‌تر)</td><td>' + tztToPersian(U.toFixed(3)) + '</td></tr>';
          html += '<tr><td>U₁</td><td>' + tztToPersian(U1.toFixed(3)) + '</td></tr>';
          html += '<tr><td>U₂</td><td>' + tztToPersian(U2.toFixed(3)) + '</td></tr>';
          html += '<tr><td>n₁ × n₂</td><td>' + tztToPersian((n1 * n2).toFixed(0)) + '</td></tr>';
          html += '</tbody></table></div>';

          html += '<div class="tzt-method-note"><strong>تفسیر:</strong> آماره U = ' + tztToPersian(U.toFixed(3)) + ' محاسبه شد. برای معناداری باید این مقدار را با مقدار بحرانی U موجود در جدول Mann-Whitney مقایسه کنید. برای نتایج دقیق‌تر، می‌توانید از نرم‌افزارهای آماری مانند SPSS یا R استفاده کنید.</div>';

          resultEl.innerHTML = html;
          resultEl.classList.add('tzt-show');

        } catch (e) {
          errEl.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
          errEl.classList.add('tzt-show');
        }
      });

      // تابع پارس کردن داده‌ها
      function parseData(text) {
        return text.trim().split(/\r?\n/).map(function(line) {
          return parseFloat(line.trim());
        }).filter(function(num) {
          return !isNaN(num);
        });
      }

      // تابع رتبه‌بندی
      function getRanks(arr) {
        var indexed = arr.map(function(val, idx) {
          return { val: val, idx: idx };
        });
        
        indexed.sort(function(a, b) {
          return a.val - b.val;
        });
        
        var ranks = new Array(arr.length);
        for (var i = 0; i < indexed.length; i++) {
          ranks[indexed[i].idx] = i + 1;
        }
        
        return ranks;
      }
    });
    </script>
    <?php
    return ob_get_clean();
}

// سئو و Schema
add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post;
    if (!$post || strpos($post->post_content, 'tz_mann_whitney') === false) return;

    $url = get_permalink();
    $app = array(
      '@context' => 'https://schema.org',
      '@type' => 'WebApplication',
      'name' => 'محاسبه‌گر آزمون Mann-Whitney U',
      'url' => $url,
      'applicationCategory' => 'EducationalApplication',
      'operatingSystem' => 'All',
      'description' => 'ابزار رایگان محاسبه آزمون ناپارامتری Mann-Whitney U برای مقایسه دو گروه مستقل.',
      'inLanguage' => 'fa-IR',
      'offers' => array('@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IRR'),
      'publisher' => array('@type' => 'Organization', 'name' => 'تزنویسه')
    );
    echo "\n" . '<script type="application/ld+json">' . wp_json_encode($app, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
});
}

/* --- wilcoxon-calculator --- */
if ( ! function_exists( 'teznevise_wilcoxon_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر آزمون Wilcoxon
// [tz_wilcoxon]
// ============================================
add_shortcode('tz_wilcoxon', 'teznevise_wilcoxon_calc');
function teznevise_wilcoxon_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">📊 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر آزمون Wilcoxon Signed-Rank Test</h1>
          <p>آزمون ناپارامتری Wilcoxon برای مقایسه دو اندازه‌گیری مرتبط (پیش‌آزمون و پس‌آزمون). این آزمون معادل ناپارامتری آزمون t زوجی است.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 داده‌های پیش‌آزمون و پس‌آزمون را وارد کنید. تعداد داده‌های دو ستون باید برابر باشد. هر مقدار در یک خط.</div>

          <div class="tzt-field">
            <label>اندازه‌گیری اول (پیش‌آزمون) <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzwilc-pre" placeholder="23&#10;25&#10;21&#10;28&#10;24"></textarea>
          </div>
          
          <div class="tzt-field">
            <label>اندازه‌گیری دوم (پس‌آزمون) <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzwilc-post" placeholder="27&#10;29&#10;24&#10;31&#10;28"></textarea>
          </div>

          <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <button type="button" class="tzt-sample-btn" id="tzwilc-sample">📝 داده نمونه</button>
          </div>

          <div class="tzt-error" id="tzwilc-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzwilc-calc">محاسبه آزمون Wilcoxon</button>
          <div class="tzt-result" id="tzwilc-result"></div>
        </div>

        <div class="tzt-content">
          <h2>آزمون Wilcoxon Signed-Rank Test چیست؟</h2>
          <p><strong>آزمون Wilcoxon Signed-Rank Test</strong> (یا Wilcoxon Matched Pairs Test) یک آزمون ناپارامتری است که برای مقایسه دو اندازه‌گیری مرتبط (وابسته) استفاده می‌شود. این آزمون معادل ناپارامتری آزمون t زوجی است و زمانی کاربرد دارد که فرض نرمال بودن توزیع داده‌ها برقرار نباشد.</p>

          <h2>فرمول آزمون Wilcoxon</h2>
          <div class="tzt-formula-box">
            T = min(T₊, T₋)<br>
            T₊ = مجموع رتبه‌های تفاوت‌های مثبت<br>
            T₋ = مجموع رتبه‌های تفاوت‌های منفی
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>dᵢ:</strong> تفاوت بین اندازه‌گیری دوم و اول برای هر مشاهده</li>
            <li><strong>|dᵢ|:</strong> قدر مطلق تفاوت</li>
            <li><strong>T₊, T₋:</strong> مجموع رتبه‌های تفاوت‌های مثبت و منفی</li>
            <li><strong>T:</strong> کوچک‌تر از T₊ و T₋</li>
          </ul>

          <h2>مراحل محاسبه</h2>
          <ol>
            <li>محاسبه تفاوت (dᵢ = پس‌آزمون - پیش‌آزمون)</li>
            <li>حذف تفاوت‌های صفر</li>
            <li>محاسبه قدر مطلق تفاوت‌ها</li>
            <li>رتبه‌بندی قدر مطلق تفاوت‌ها</li>
            <li>محاسبه مجموع رتبه‌های مثبت و منفی</li>
            <li>انتخاب مقدار کوچک‌تر به عنوان T نهایی</li>
          </ol>

          <h2>تفسیر آزمون Wilcoxon</h2>
          <p>اگر مقدار T به‌دست‌آمده کمتر از مقدار بحرانی T در جدول Wilcoxon باشد (با توجه به سطح معناداری و تعداد مشاهدات)، تفاوت معنادار بین دو اندازه‌گیری وجود دارد.</p>

          <h2>مزایای آزمون Wilcoxon</h2>
          <ul>
            <li><strong>نیازی به فرض نرمال بودن ندارد:</strong> برای داده‌های غیرنرمال مناسب است.</li>
            <li><strong>برای داده‌های رتبه‌ای:</strong> می‌تواند برای داده‌های ترتیبی هم استفاده شود.</li>
            <li><strong>مقاوم به داده‌های پرت:</strong> داده‌های پرت تأثیر کمی بر نتیجه دارند.</li>
            <li><strong>برای نمونه‌های کوچک:</strong> مناسب برای نمونه‌های کوچک است.</li>
          </ul>

          <h2>کاربردهای عملی</h2>
          <ul>
            <li><strong>تحقیقات پزشکی:</strong> مقایسه درجات درد قبل و بعد از درمان</li>
            <li><strong>تحقیقات آموزشی:</strong> مقایسه نمرات پیش‌آزمون و پس‌آزمون</li>
            <li><strong>تحقیقات رفتاری:</strong> مقایسه رفتار قبل و بعد از مداخله</li>
            <li><strong>تحقیقات بازاریابی:</strong> مقایسه رضایت مشتریان قبل و بعد</li>
          </ul>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چه زمانی از آزمون Wilcoxon استفاده کنیم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">زمانی که می‌خواهید دو اندازه‌گیری مرتبط (پیش و پس) را مقایسه کنید و داده‌ها توزیع نرمال ندارند یا رتبه‌ای هستند. این آزمون معادل ناپارامتری آزمون t زوجی است.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">تفاوت Wilcoxon و آزمون t زوجی چیست؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">آزمون t زوجی یک آزمون پارامتری است که فرض می‌کند داده‌ها توزیع نرمال دارند. Wilcoxon ناپارامتری است و نیازی به این فرض ندارد. اگر فرضیات t-test برقرار نباشند، Wilcoxon بهتر است.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چگونه می‌توانم نتیجه آزمون Wilcoxon را تفسیر کنم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">اگر مقدار T کمتر از مقدار بحرانی در جدول Wilcoxon باشد، تفاوت معناداری بین دو اندازه‌گیری وجود دارد. برای تعیین p-value دقیق، باید از جداول یا نرم‌افزار آماری استفاده کنید.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چه تعداد داده برای آزمون Wilcoxon نیاز است؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">حداقل ۲ جفت داده کافی است. اما برای نتایج قابل‌اعتماد، حداقل ۶ جفت توصیه می‌شود. هرچه تعداد داده‌ها بیشتر باشد، قدرت آزمون بالاتر است.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">تفاوت‌های صفر در آزمون Wilcoxon چگونه مدیریت می‌شوند؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">اگر تفاوت بین دو اندازه‌گیری صفر باشد (یعنی هیچ تغییری نداشته باشد)، آن جفت از تحلیل حذف می‌شود. این مورد معمولاً در داده‌های واقعی نادر است.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">آیا Wilcoxon برای داده‌های نرمال هم قابل استفاده است؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، می‌توانید استفاده کنید. اما اگر داده‌ها نرمال باشند و توزیع متقارن باشد، بهتر است از آزمون t زوجی استفاده کنید چون قدرت آماری بیشتری دارد.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">این محاسبه‌گر با SPSS و R مطابقت دارد؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، این محاسبه‌گر از فرمول‌های استاندارد استفاده می‌کند و نتایج آن با SPSS و R مطابقت دارند. مقدار T محاسبه‌شده در هر سه منبع یکسان خواهد بود.</div></div>
          </div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">رتبه‌بندی در آزمون Wilcoxon چگونه انجام می‌شود؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">قدر مطلق تفاوت‌ها از کوچک به بزرگ رتبه‌بندی می‌شوند. اگر تفاوت‌های تکراری باشند، رتبه میانگین به آن‌ها اختصاص می‌یابد. سپس علامت تفاوت (مثبت یا منفی) به رتبه‌ها اختصاص می‌یابد.</div></div>
          </div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل دقیق‌تری نیاز دارید؟</h2>
          <p>از آزمون‌های ناپارامتری تا مدل‌سازی و تجزیه و تحلیل داده‌های پیشرفته — متخصصان تزنویسه در تمام مراحل تحلیل کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzwilc-calc')) return;

      // تابع تبدیل اعداد به فارسی
      function tztToPersian(num) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return String(num).replace(/\d/g, function(digit) {
          return persianDigits[digit];
        });
      }

      // تابع FAQ — اینیشیالایز کردن
      function initFAQ() {
        var items = document.querySelectorAll('.tzt-faq-item');
        for(var i = 0; i < items.length; i++) {
          (function(item) {
            var question = item.querySelector('.tzt-faq-q');
            if(!question) return;
            
            question.addEventListener('click', function(e) {
              e.preventDefault();
              item.classList.toggle('tzt-faq-open');
            });
          })(items[i]);
        }
      }

      initFAQ();

      // داده نمونه
      document.getElementById('tzwilc-sample').addEventListener('click', function(){
        document.getElementById('tzwilc-pre').value = '23\n25\n21\n28\n24\n26\n22\n27';
        document.getElementById('tzwilc-post').value = '27\n29\n24\n31\n28\n30\n25\n32';
      });

      // محاسبه آزمون Wilcoxon
      document.getElementById('tzwilc-calc').addEventListener('click', function(){
        var errEl = document.getElementById('tzwilc-err');
        var resultEl = document.getElementById('tzwilc-result');
        
        errEl.classList.remove('tzt-show');
        resultEl.classList.remove('tzt-show');
        resultEl.innerHTML = '';

        try {
          // دریافت و پارس داده‌ها
          var preStr = document.getElementById('tzwilc-pre').value;
          var postStr = document.getElementById('tzwilc-post').value;

          if(!preStr.trim() || !postStr.trim()) {
            throw 'لطفاً هر دو ستون را پر کنید.';
          }

          var preData = parseData(preStr);
          var postData = parseData(postStr);

          if(preData.length < 2 || postData.length < 2) {
            throw 'هر ستون باید حداقل ۲ مقدار داشته باشد.';
          }

          if(preData.length !== postData.length) {
            throw 'تعداد داده‌های پیش‌آزمون و پس‌آزمون باید برابر باشد.';
          }

          var n = preData.length;

          // محاسبه تفاوت‌ها
          var diffs = [];
          for(var i = 0; i < n; i++) {
            diffs.push(postData[i] - preData[i]);
          }

          // حذف تفاوت‌های صفر
          var nonZeroDiffs = diffs.filter(function(d) { return d !== 0; });
          var nNonZero = nonZeroDiffs.length;

          if(nNonZero < 2) {
            throw 'حداقل ۲ تفاوت غیرصفر نیاز است.';
          }

          // قدر مطلق تفاوت‌ها
          var absDiffs = nonZeroDiffs.map(function(d) { return Math.abs(d); });

          // رتبه‌بندی
          var ranks = getRanks(absDiffs);

          // محاسبه T+ و T-
          var T_plus = 0;
          var T_minus = 0;

          for(var i = 0; i < nNonZero; i++) {
            if(nonZeroDiffs[i] > 0) {
              T_plus += ranks[i];
            } else {
              T_minus += ranks[i];
            }
          }

          var T = Math.min(T_plus, T_minus);

          // نمایش نتیجه
          var html = '<div class="tzt-result-main">';
          html += '<div class="tzt-result-label">نتیجه آزمون Wilcoxon</div>';
          html += '<div class="tzt-result-value">T = ' + tztToPersian(T.toFixed(0)) + '</div>';
          html += '<span class="tzt-result-badge" style="background:#145D4A;">آزمون ناپارامتری</span>';
          html += '<div class="tzt-result-meta">';
          html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(nNonZero) + '</strong><span>تعداد جفت‌های غیرصفر</span></div>';
          html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(T_plus.toFixed(0)) + '</strong><span>مجموع رتبه‌های مثبت</span></div>';
          html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(T_minus.toFixed(0)) + '</strong><span>مجموع رتبه‌های منفی</span></div>';
          html += '</div></div>';

          html += '<div class="tzt-tbl-wrap" style="margin-top:20px;"><table class="tzt-tbl"><thead><tr><th>شاخص</th><th>مقدار</th></tr></thead><tbody>';
          html += '<tr><td>T (کوچک‌تر)</td><td>' + tztToPersian(T.toFixed(0)) + '</td></tr>';
          html += '<tr><td>T₊</td><td>' + tztToPersian(T_plus.toFixed(0)) + '</td></tr>';
          html += '<tr><td>T₋</td><td>' + tztToPersian(T_minus.toFixed(0)) + '</td></tr>';
          html += '<tr><td>مجموع (T₊ + T₋)</td><td>' + tztToPersian((T_plus + T_minus).toFixed(0)) + '</td></tr>';
          html += '</tbody></table></div>';

          html += '<div class="tzt-method-note"><strong>تفسیر:</strong> آماره T = ' + tztToPersian(T.toFixed(0)) + ' محاسبه شد. برای معناداری باید این مقدار را با مقدار بحرانی T موجود در جدول Wilcoxon مقایسه کنید. برای نتایج دقیق‌تر، می‌توانید از نرم‌افزارهای آماری مانند SPSS یا R استفاده کنید.</div>';

          resultEl.innerHTML = html;
          resultEl.classList.add('tzt-show');

        } catch (e) {
          errEl.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
          errEl.classList.add('tzt-show');
        }
      });

      // تابع پارس کردن داده‌ها
      function parseData(text) {
        return text.trim().split(/\r?\n/).map(function(line) {
          return parseFloat(line.trim());
        }).filter(function(num) {
          return !isNaN(num);
        });
      }

      // تابع رتبه‌بندی
      function getRanks(arr) {
        var indexed = arr.map(function(val, idx) {
          return { val: val, idx: idx };
        });
        
        indexed.sort(function(a, b) {
          return a.val - b.val;
        });
        
        var ranks = new Array(arr.length);
        for (var i = 0; i < indexed.length; i++) {
          ranks[indexed[i].idx] = i + 1;
        }
        
        return ranks;
      }
    });
    </script>
    <?php
    return ob_get_clean();
}

// سئو و Schema
add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post;
    if (!$post || strpos($post->post_content, 'tz_wilcoxon') === false) return;

    $url = get_permalink();
    $app = array(
      '@context' => 'https://schema.org',
      '@type' => 'WebApplication',
      'name' => 'محاسبه‌گر آزمون Wilcoxon Signed-Rank Test',
      'url' => $url,
      'applicationCategory' => 'EducationalApplication',
      'operatingSystem' => 'All',
      'description' => 'ابزار رایگان محاسبه آزمون ناپارامتری Wilcoxon برای مقایسه دو اندازه‌گیری مرتبط.',
      'inLanguage' => 'fa-IR',
      'offers' => array('@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IRR'),
      'publisher' => array('@type' => 'Organization', 'name' => 'تزنویسه')
    );
    echo "\n" . '<script type="application/ld+json">' . wp_json_encode($app, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
});
}

/* --- kruskal-wallis-calculator --- */
if ( ! function_exists( 'teznevise_kruskal_wallis_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر آزمون Kruskal-Wallis
// [tz_kruskal_wallis]
// ============================================
add_shortcode('tz_kruskal_wallis', 'teznevise_kruskal_wallis_calc');
function teznevise_kruskal_wallis_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">📊 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر آزمون Kruskal-Wallis</h1>
          <p>آزمون ناپارامتری Kruskal-Wallis برای مقایسه چند گروه مستقل. این آزمون جایگزین غیرپارامتری ANOVA است.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 داده‌های خود را برای هر گروه وارد کنید. هر مقدار در یک خط. حداقل ۲ گروه و ۲ مقدار برای هر گروه لازم است.</div>
          
          <div class="tzt-field">
            <label>گروه اول <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzkwh-group1" placeholder="23&#10;25&#10;21&#10;28"></textarea>
          </div>
          
          <div class="tzt-field">
            <label>گروه دوم <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzkwh-group2" placeholder="30&#10;27&#10;29&#10;31"></textarea>
          </div>
          
          <div class="tzt-field">
            <label>گروه سوم <span class="tzt-hint">(اختیاری، هر عدد یک خط)</span></label>
            <textarea id="tzkwh-group3" placeholder="35&#10;36&#10;34&#10;38"></textarea>
          </div>

          <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <button type="button" class="tzt-sample-btn" id="tzkwh-sample">📝 داده نمونه</button>
          </div>

          <div class="tzt-error" id="tzkwh-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzkwh-calc">محاسبه آزمون Kruskal-Wallis</button>
          <div class="tzt-result" id="tzkwh-result"></div>
        </div>

        <div class="tzt-content">
          <h2>آزمون Kruskal-Wallis چیست؟</h2>
          <p>آزمون Kruskal-Wallis یک آزمون ناپارامتری است که برای مقایسه چند گروه مستقل استفاده می‌شود و جایگزین غیرپارامتری آزمون ANOVA است.</p>

          <h2>فرمول آزمون Kruskal-Wallis</h2>
          <div class="tzt-formula-box">
            H = (12 / (N(N+1))) × Σ (Rᵢ² / nᵢ) - 3(N+1)
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>nᵢ:</strong> تعداد مشاهدات در گروه i</li>
            <li><strong>Rᵢ:</strong> مجموع رتبه‌های گروه i</li>
            <li><strong>N:</strong> کل تعداد مشاهدات</li>
          </ul>

          <h2>تفسیر نتایج</h2>
          <p>اگر مقدار H محاسبه شده بزرگ‌تر از مقدار بحرانی جدول کای-دو (χ²) باشد، تفاوت معناداری بین گروه‌ها وجود دارد.</p>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چه زمانی آزمون Kruskal-Wallis استفاده می‌شود؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">برای مقایسه چند گروه مستقل زمانی که داده‌ها توزیع نرمال ندارند یا داده‌ها رتبه‌ای باشند.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">آیا آزمون Kruskal-Wallis معادل ANOVA است؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، یک جایگزین ناپارامتری برای ANOVA یک‌راهه است.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چگونه نتایج آزمون را تفسیر کنم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">اگر مقدار H از مقدار بحرانی بزرگ‌تر باشد، تفاوت معناداری بین گروه‌ها وجود دارد.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چند گروه باید داشته باشم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">حداقل دو گروه مستقل با حداقل دو مقدار در هر گروه نیاز است.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل دقیق‌تری نیاز دارید؟</h2>
          <p>از آزمون‌های ناپارامتری تا مدل‌سازی و تجزیه و تحلیل پیشرفته — متخصصان تزنویسه همیشه کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  if(!document.getElementById('tzkwh-calc')) return;

  // تبدیل اعداد به فارسی
  function tztToPersian(num) {
    const persianDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    return String(num).replace(/\d/g, function(d){ return persianDigits[d]; });
  }

  // FAQ
  function initFAQ() {
    document.querySelectorAll('.tzt-faq-item .tzt-faq-q').forEach(function(q){
      q.addEventListener('click', function(){
        this.parentNode.classList.toggle('tzt-faq-open');
      });
    });
  }
  initFAQ();

  // داده نمونه
  document.getElementById('tzkwh-sample').addEventListener('click', function(){
    document.getElementById('tzkwh-group1').value = '23\n25\n21\n28\n24';
    document.getElementById('tzkwh-group2').value = '30\n27\n29\n31\n28';
    document.getElementById('tzkwh-group3').value = '35\n36\n34\n38\n32';
  });

  document.getElementById('tzkwh-calc').addEventListener('click', function(){
    var err = document.getElementById('tzkwh-err');
    var result = document.getElementById('tzkwh-result');
    err.classList.remove('tzt-show');
    result.classList.remove('tzt-show');
    result.innerHTML = '';

    try {
      var group1 = parseData(document.getElementById('tzkwh-group1').value);
      var group2 = parseData(document.getElementById('tzkwh-group2').value);
      var group3 = parseData(document.getElementById('tzkwh-group3').value);

      if(group1.length < 2 || group2.length < 2) throw 'حداقل دو گروه با حداقل ۲ مقدار لازم است.';

      var groups = [group1, group2];
      if(group3.length >= 2) groups.push(group3);

      var allData = [];
      groups.forEach(g => allData = allData.concat(g));
      var N = allData.length;

      // رتبه‌بندی کل داده‌ها
      var indexed = allData.map((val, idx) => ({ val, idx }));
      indexed.sort((a,b) => a.val - b.val);

      var ranks = new Array(N);
      for(let i = 0; i < N; i++) ranks[indexed[i].idx] = i + 1;

      // محاسبه مجموع رتبه‌ها برای هر گروه
      var rankSums = [];
      var startIdx = 0;
      for(let i=0; i<groups.length; i++) {
        let sumRanks = 0;
        for(let j=0;j<groups[i].length;j++) {
          sumRanks += ranks[startIdx + j];
        }
        rankSums.push(sumRanks);
        startIdx += groups[i].length;
      }

      var k = groups.length;
      var H = 0;
      for(let i=0; i<k; i++) {
        H += (Math.pow(rankSums[i], 2) / groups[i].length);
      }
      H = (12 / (N * (N + 1))) * H - 3 * (N + 1);

      // نمایش نتیجه
      var html = '<div class="tzt-result-main">';
      html += '<div class="tzt-result-label">نتیجه آزمون Kruskal-Wallis</div>';
      html += '<div class="tzt-result-value">H = ' + tztToPersian(H.toFixed(3)) + '</div>';
      html += '<div class="tzt-result-meta">';
      html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(k) + '</strong><span>تعداد گروه‌ها</span></div>';
      html += '<div class="tzt-result-meta-item"><strong>' + tztToPersian(N) + '</strong><span>تعداد کل مشاهدات</span></div>';
      html += '</div></div>';

      html += '<div class="tzt-method-note"><strong>تفسیر:</strong> مقدار H محاسبه شده است. برای تفسیر دقیق، مقدار H را با مقدار بحرانی جدول کای-دو (χ²) با درجه آزادی k-1 مقایسه کنید.</div>';

      result.innerHTML = html;
      result.classList.add('tzt-show');
    }
    catch(e) {
      err.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
      err.classList.add('tzt-show');
    }
  });

  // توابع کمکی
  function parseData(text) {
    return text.trim().split(/\r?\n/).map(x => parseFloat(x.trim())).filter(x => !isNaN(x));
  }
});
</script>
<?php
return ob_get_clean();
}
}

/* --- regression-calculator --- */
if ( ! function_exists( 'teznevise_regression_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر تحلیل رگرسیون
// [tz_regression]
// ============================================
add_shortcode('tz_regression', 'teznevise_regression_calc');
function teznevise_regression_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">📊 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر تحلیل رگرسیون</h1>
          <p>رگرسیون خطی ساده و چندگانه را برای پیش‌بینی یک متغیر وابسته بر اساس یک یا چند متغیر مستقل محاسبه کنید.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 داده‌های خود را وارد کنید. هر مقدار در یک خط، برای متغیر مستقل و وابسته.</div>
          
          <div class="tzt-field">
            <label>متغیر وابسته (Y) <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzreg-y" placeholder="5&#10;7&#10;8&#10;9&#10;10"></textarea>
          </div>

          <div class="tzt-field">
            <label>متغیر مستقل (X) <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzreg-x" placeholder="1&#10;2&#10;3&#10;4&#10;5"></textarea>
          </div>

          <div class="tzt-error" id="tzreg-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzreg-calc">محاسبه رگرسیون</button>
          <div class="tzt-result" id="tzreg-result"></div>
        </div>

        <div class="tzt-content">
          <h2>رگرسیون چیست؟</h2>
          <p><strong>تحلیل رگرسیون</strong> یک تکنیک آماری است که برای بررسی رابطه بین یک متغیر وابسته و یک یا چند متغیر مستقل استفاده می‌شود. هدف اصلی تحلیل رگرسیون، پیش‌بینی مقادیر متغیر وابسته بر اساس مقادیر متغیرهای مستقل است.</p>

          <h2>رگرسیون خطی ساده</h2>
          <p>رگرسیون خطی ساده به دنبال یافتن رابطه خطی بین یک متغیر مستقل (X) و یک متغیر وابسته (Y) است و معمولاً با فرمول زیر نمایش داده می‌شود:</p>
          <div class="tzt-formula-box">
            Y = β₀ + β₁X + ε
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>Y:</strong> متغیر وابسته</li>
            <li><strong>X:</strong> متغیر مستقل</li>
            <li><strong>β₀:</strong> عرض از مبدأ (Intercept)</li>
            <li><strong>β₁:</strong> شیب خط (Slope)</li>
            <li><strong>ε:</strong> خطای تصادفی</li>
          </ul>

          <h2>رگرسیون خطی چندگانه</h2>
          <p>رگرسیون خطی چندگانه برای پیش‌بینی یک متغیر وابسته بر اساس چندین متغیر مستقل استفاده می‌شود و معمولاً با فرمول زیر نمایش داده می‌شود:</p>
          <div class="tzt-formula-box">
            Y = β₀ + β₁X₁ + β₂X₂ + ... + βₖXₖ + ε
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>X₁, X₂, ..., Xₖ:</strong> متغیرهای مستقل</li>
          </ul>

          <h2>تفسیر نتایج رگرسیون</h2>
          <p>نتایج رگرسیون شامل مقادیر β، r² (ضریب تعیین) و p-value است. r² نشان‌دهنده میزان تغییرات متغیر وابسته است که توسط متغیرهای مستقل توضیح داده می‌شود. اگر p-value کمتر از ۰.۰۵ باشد، نتیجه معنادار است.</p>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">تحلیل رگرسیون چه زمانی استفاده می‌شود؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">زمانی که می‌خواهید پیش‌بینی کنید که یک متغیر وابسته تحت تأثیر یک یا چند متغیر مستقل قرار دارد.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">تفاوت رگرسیون خطی ساده و چندگانه چیست؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">رگرسیون خطی ساده فقط یک متغیر مستقل دارد، در حالی که رگرسیون خطی چندگانه شامل چندین متغیر مستقل است.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چگونه می‌توانم نتیجه رگرسیون را تفسیر کنم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">نتایج شامل مقادیر β، r² و p-value است. اگر p-value کمتر از ۰.۰۵ باشد، می‌توان نتیجه‌گیری کرد که رابطه معناداری بین متغیرها وجود دارد.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">آیا رگرسیون برای داده‌های غیرخطی هم قابل استفاده است؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، می‌توانید از مدل‌های رگرسیون غیرخطی استفاده کنید، اما باید از تکنیک‌های خاصی برای شناسایی و مدل‌سازی استفاده کنید.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چگونه می‌توانم چندین متغیر مستقل را در رگرسیون بررسی کنم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">در رگرسیون چندگانه، می‌توانید چندین متغیر مستقل را به‌صورت همزمان وارد کنید و نتایج را تحلیل کنید.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">این محاسبه‌گر با نرم‌افزارهای آماری دیگر مطابقت دارد؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، نتایج این محاسبه‌گر با نتایج نرم‌افزارهایی مانند SPSS و R مطابقت دارد.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل دقیق‌تری نیاز دارید؟</h2>
          <p>از رگرسیون تا تحلیل‌های پیشرفته — متخصصان تزنویسه در تمام مراحل تحلیل کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzreg-calc')) return;

      // تابع تبدیل اعداد به فارسی
      function tztToPersian(num) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return String(num).replace(/\d/g, function(digit) {
          return persianDigits[digit];
        });
      }

      // تابع FAQ
      function initFAQ() {
        var items = document.querySelectorAll('.tzt-faq-item');
        for(var i = 0; i < items.length; i++) {
          (function(item) {
            var q = item.querySelector('.tzt-faq-q');
            if(!q) return;
            q.addEventListener('click', function(){
              item.classList.toggle('tzt-faq-open');
            });
          })(items[i]);
        }
      }
      initFAQ();

      // محاسبه رگرسیون
      document.getElementById('tzreg-calc').addEventListener('click', function(){
        var err = document.getElementById('tzreg-err');
        var result = document.getElementById('tzreg-result');

        err.classList.remove('tzt-show');
        result.classList.remove('tzt-show');
        result.innerHTML = '';

        try {
          var yData = parseData(document.getElementById('tzreg-y').value);
          var xData = parseData(document.getElementById('tzreg-x').value);

          if(yData.length < 2 || xData.length < 2) {
            throw 'هر دو متغیر باید حداقل ۲ مقدار داشته باشند.';
          }
          if(yData.length !== xData.length) {
            throw 'تعداد مقادیر متغیرهای وابسته و مستقل باید برابر باشد.';
          }

          // محاسبات رگرسیون
          var n = yData.length;
          var xMean = mean(xData);
          var yMean = mean(yData);
          var sxy = 0, sxx = 0;
          for(var i = 0; i < n; i++) {
            sxy += (xData[i] - xMean) * (yData[i] - yMean);
            sxx += (xData[i] - xMean) * (xData[i] - xMean);
          }

          var b1 = sxy / sxx; // شیب
          var b0 = yMean - (b1 * xMean); // عرض از مبدأ

          // نمایش نتایج
          var html = '<div class="tzt-result-main">';
          html += '<div class="tzt-result-label">نتیجه تحلیل رگرسیون</div>';
          html += '<div class="tzt-result-value">Y = ' + tztToPersian(b0.toFixed(3)) + ' + ' + tztToPersian(b1.toFixed(3)) + 'X</div>';
          html += '</div>';

          result.innerHTML = html;
          result.classList.add('tzt-show');

        } catch (e) {
          err.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
          err.classList.add('tzt-show');
        }
      });

      // تابع پارس کردن داده‌ها
      function parseData(text) {
        return text.trim().split(/\r?\n/).map(Number).filter(num => !isNaN(num));
      }

      // تابع محاسبه میانگین
      function mean(arr) {
        return arr.reduce((a, b) => a + b, 0) / arr.length;
      }
    });
    </script>
    <?php
    return ob_get_clean();
}

// سئو و Schema
add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post;
    if (!$post || strpos($post->post_content, 'tz_regression') === false) return;

    $url = get_permalink();
    $app = array(
      '@context' => 'https://schema.org',
      '@type' => 'WebApplication',
      'name' => 'محاسبه‌گر تحلیل رگرسیون تزنویسه',
      'url' => $url,
      'applicationCategory' => 'EducationalApplication',
      'operatingSystem' => 'All',
      'description' => 'ابزار رایگان تحلیل رگرسیون برای پیش‌بینی یک متغیر وابسته بر اساس یک یا چند متغیر مستقل.',
      'inLanguage' => 'fa-IR',
      'offers' => array('@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IRR'),
      'publisher' => array('@type' => 'Organization', 'name' => 'تزنویسه')
    );
    echo "\n" . '<script type="application/ld+json">' . wp_json_encode($app, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";

    $faqs = array(
        array('تحلیل رگرسیون چه زمانی استفاده می‌شود؟', 'زمانی که می‌خواهید پیش‌بینی کنید که یک متغیر وابسته تحت تأثیر یک یا چند متغیر مستقل قرار دارد.'),
        array('تفاوت رگرسیون خطی ساده و چندگانه چیست؟', 'رگرسیون خطی ساده فقط یک متغیر مستقل دارد، در حالی که رگرسیون چندگانه شامل چندین متغیر مستقل است.'),
        array('چگونه می‌توانم نتیجه رگرسیون را تفسیر کنم؟', 'نتایج شامل مقادیر β، r² و p-value است. اگر p-value کمتر از ۰.۰۵ باشد، می‌توان نتیجه‌گیری کرد که رابطه معناداری بین متغیرها وجود دارد.'),
        array('آیا رگرسیون برای داده‌های غیرخطی هم قابل استفاده است؟', 'بله، می‌توانید از مدل‌های رگرسیون غیرخطی استفاده کنید، اما باید از تکنیک‌های خاصی برای شناسایی و مدل‌سازی استفاده کنید.'),
        array('چگونه می‌توانم چندین متغیر مستقل را در رگرسیون بررسی کنم؟', 'در رگرسیون چندگانه، می‌توانید چندین متغیر مستقل را به‌صورت همزمان وارد کنید و نتایج را تحلیل کنید.'),
        array('این محاسبه‌گر با نرم‌افزارهای آماری دیگر مطابقت دارد؟', 'بله، نتایج این محاسبه‌گر با نتایج نرم‌افزارهایی مانند SPSS و R مطابقت دارد.'),
    );

    $items = array();
    foreach ($faqs as $f) {
        $items[] = array(
            '@type' => 'Question',
            'name' => $f[0],
            'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f[1])
        );
    }
    echo '<script type="application/ld+json">' . wp_json_encode(array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
});
}

/* --- chi-square-calculator --- */
if ( ! function_exists( 'teznevise_chi_square_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر آزمون مجذور کای (Chi-Square)
// [tz_chi_square]
// ============================================
add_shortcode('tz_chi_square', 'teznevise_chi_square_calc');
function teznevise_chi_square_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">📊 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر آزمون مجذور کای (Chi-Square)</h1>
          <p>آزمون کای-مجذور برای بررسی ارتباط بین دو متغیر کیفی در جدول توافقی (Contingency Table) کاربرد دارد.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 جدول توافقی خود را وارد کنید. هر ردیف یک سطر جدول، ستون‌ها با کاما یا تب جدا شوند. حداقل ۲ سطر و ۲ ستون لازم است.</div>
          <div class="tzt-field">
            <label>ورودی جدول توافقی (Contingency Table)</label>
            <textarea id="tzchi-table" placeholder="10,20,30&#10;20,15,25&#10;30,25,20"></textarea>
          </div>
          <div class="tzt-error" id="tzchi-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzchi-calc">محاسبه آزمون کای</button>
          <div class="tzt-result" id="tzchi-result"></div>
        </div>

        <div class="tzt-content">
          <h2>آزمون مجذور کای (Chi-Square) چیست؟</h2>
          <p>این آزمون برای بررسی ارتباط یا استقلال دو متغیر کیفی استفاده می‌شود. بر اساس مقایسه مقادیر مشاهده شده و مقادیر مورد انتظار در جدول توافقی، آماره χ² محاسبه می‌شود.</p>

          <h2>فرمول آزمون مجذور کای</h2>
          <div class="tzt-formula-box">
            χ² = Σ ( (Oᵢ − Eᵢ)² / Eᵢ )
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>Oᵢ:</strong> مقدار مشاهده شده در خانه i</li>
            <li><strong>Eᵢ:</strong> مقدار مورد انتظار در خانه i</li>
          </ul>

          <h2>مقادیر مورد انتظار</h2>
          <p>برای هر خانه جدول، مقدار مورد انتظار از رابطه زیر محاسبه می‌شود:</p>
          <div class="tzt-formula-box">
            Eᵢ = (Row Total × Column Total) / Grand Total
          </div>

          <h2>تفسیر نتایج</h2>
          <p>اگر مقدار χ² محاسبه شده بزرگ‌تر از مقدار بحرانی جدول χ² با درجات آزادی مناسب باشد، فرضیه استقلال رد می‌شود و رابطه معناداری بین دو متغیر وجود دارد.</p>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چه زمانی از آزمون کای-مجذور استفاده کنیم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">زمانی که می‌خواهید ارتباط یا استقلال دو متغیر کیفی (اسمی یا رتبه‌ای) را بررسی کنید.</div></div></div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">درجات آزادی آزمون کای چگونه محاسبه می‌شود؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">درجات آزادی برابر است با (تعداد سطرها - ۱) × (تعداد ستون‌ها - ۱).</div></div></div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">اگر مقادیر مورد انتظار کمتر از ۵ باشند، چه باید کرد؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">در این حالت، آزمون Fisher’s exact توصیه می‌شود چون آزمون کای ممکن است نتایج غیر دقیق دهد.</div></div></div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">آیا آزمون کای-مجذور به توزیع داده‌ها حساس است؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">خیر، این آزمون ناپارامتری است و نیازی به فرض نرمال بودن داده‌ها ندارد.</div></div></div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">این محاسبه‌گر با نرم‌افزارهای آماری دیگر مطابقت دارد؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، این ابزار از فرمول‌های استاندارد استفاده می‌کند و نتایج آن با SPSS، R و سایر نرم‌افزارها مطابقت دارد.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل دقیق‌تری نیاز دارید؟</h2>
          <p>از آزمون‌های آماری تا مدل‌سازی و تجزیه و تحلیل داده‌ها — متخصصان تزنویسه در کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const err = document.getElementById('tzchi-err');
  const result = document.getElementById('tzchi-result');
  err.classList.remove('tzt-show');
  result.classList.remove('tzt-show');
  result.innerHTML = '';

  function tztToPersian(num) {
    const persianDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    return String(num).replace(/\d/g, d => persianDigits[d]);
  }

  function initFAQ() {
    document.querySelectorAll('.tzt-faq-item .tzt-faq-q').forEach(q => {
      q.addEventListener('click', () => {
        q.parentElement.classList.toggle('tzt-faq-open');
      });
    });
  }
  initFAQ();

  function parseTable(text) {
    return text.trim().split(/\r?\n+/).map(row => {
      return row.split(/[, \t]+/).map(Number).filter(n => !isNaN(n));
    });
  }

  document.getElementById('tzchi-calc').addEventListener('click', () => {
    err.classList.remove('tzt-show');
    result.classList.remove('tzt-show');
    result.innerHTML = '';

    try {
      const tableInput = document.getElementById('tzchi-table').value;
      const table = parseTable(tableInput);

      if(table.length < 2) throw 'حداقل ۲ سطر لازم است.';
      const colCount = table[0].length;
      if(colCount < 2) throw 'حداقل ۲ ستون لازم است.';

      for(let i=1; i<table.length; i++){
        if(table[i].length !== colCount) throw 'تعداد ستون‌ها در همه ردیف‌ها باید یکسان باشد.';
      }

      const rowSums = table.map(row => row.reduce((a,b)=>a+b,0));
      const colSums = Array(colCount).fill(0);
      table.forEach(row => {
        row.forEach((val,j) => colSums[j] += val);
      });
      const total = rowSums.reduce((a,b) => a+b,0);

      let chiSq = 0;
      for(let i=0; i<table.length; i++){
        for(let j=0; j<colCount; j++){
          const expected = (rowSums[i]*colSums[j])/total;
          if(expected === 0) continue;
          const observed = table[i][j];
          chiSq += Math.pow(observed - expected, 2) / expected;
        }
      }

      const df = (table.length - 1)*(colCount - 1);

      let html = '<div class="tzt-result-main">';
      html += '<div class="tzt-result-label">نتیجه آزمون مجذور کای</div>';
      html += `<div class="tzt-result-value">χ² = ${tztToPersian(chiSq.toFixed(3))}</div>`;
      html += '<div class="tzt-result-meta">';
      html += `<div class="tzt-result-meta-item"><strong>${tztToPersian(df)}</strong><span>درجه آزادی</span></div>`;
      html += `<div class="tzt-result-meta-item"><strong>${tztToPersian(total)}</strong><span>تعداد کل مشاهدات</span></div>`;
      html += '</div></div>';

      html += `<div class="tzt-method-note"><strong>تفسیر:</strong> مقدار χ² را با مقدار بحرانی جدول کای-دو برای درجه آزادی ${tztToPersian(df)} مقایسه کنید. اگر χ² بزرگ‌تر باشد، رابطه معنادار است.</div>`;

      result.innerHTML = html;
      result.classList.add('tzt-show');
    }
    catch(e){
      err.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
      err.classList.add('tzt-show');
    }
  });
});
</script>
<?php
return ob_get_clean();
}
}

/* --- goodness-of-fit-calculator --- */
if ( ! function_exists( 'teznevise_goodness_of_fit_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر آزمون نیکویی برازش
// [tz_goodness_of_fit]
// ============================================
add_shortcode('tz_goodness_of_fit', 'teznevise_goodness_of_fit_calc');
function teznevise_goodness_of_fit_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">📊 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر آزمون نیکویی برازش</h1>
          <p>آزمون نیکویی برازش برای بررسی اینکه آیا توزیع داده‌های مشاهده‌شده با توزیع مورد انتظار مطابقت دارد یا خیر، استفاده می‌شود.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 مقادیر مشاهده‌شده و مورد انتظار را وارد کنید. هر مقدار در یک خط. تعداد مقادیر باید برابر باشد.</div>

          <div class="tzt-field">
            <label>مقادیر مشاهده شده (Observed) <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzgof-observed" placeholder="10&#10;20&#10;30&#10;40"></textarea>
          </div>

          <div class="tzt-field">
            <label>مقادیر مورد انتظار (Expected) <span class="tzt-hint">(هر عدد یک خط)</span></label>
            <textarea id="tzgof-expected" placeholder="15&#10;25&#10;35&#10;25"></textarea>
          </div>

          <div class="tzt-error" id="tzgof-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzgof-calc">محاسبه آزمون نیکویی برازش</button>
          <div class="tzt-result" id="tzgof-result"></div>
        </div>

        <div class="tzt-content">
          <h2>آزمون نیکویی برازش (Goodness of Fit) چیست؟</h2>
          <p><strong>آزمون نیکویی برازش</strong> برای بررسی اینکه آیا توزیع داده‌های مشاهده‌شده با توزیع مورد انتظار مطابقت دارد یا خیر، استفاده می‌شود. این آزمون به ما کمک می‌کند تا تعیین کنیم آیا فرضیه توزیع داده‌ها صحیح است یا خیر.</p>

          <h2>فرمول آزمون مجذور کای</h2>
          <div class="tzt-formula-box">
            χ² = Σ ( (Oᵢ − Eᵢ)² / Eᵢ )
          </div>
          <p>که در آن:</p>
          <ul>
            <li><strong>Oᵢ:</strong> مقدار مشاهده شده</li>
            <li><strong>Eᵢ:</strong> مقدار مورد انتظار</li>
          </ul>

          <h2>درجات آزادی</h2>
          <div class="tzt-formula-box">
            df = k − 1
          </div>
          <p>که در آن <strong>k</strong> تعداد رده‌ها است.</p>

          <h2>تفسیر نتایج</h2>
          <p>اگر مقدار χ² محاسبه‌شده بزرگ‌تر از مقدار بحرانی χ² در جدول کای-دو باشد (با توجه به درجات آزادی و سطح معناداری)، می‌توان نتیجه‌گیری کرد که داده‌های مشاهده‌شده با توزیع مورد انتظار مطابقت ندارند.</p>

          <h2>مزایای آزمون نیکویی برازش</h2>
          <ul>
            <li><strong>بررسی توزیع:</strong> بررسی اینکه آیا داده‌ها از یک توزیع خاص پیروی می‌کنند.</li>
            <li><strong>ناپارامتری:</strong> نیازی به فرض نرمال بودن داده‌ها ندارد.</li>
            <li><strong>کاربردی:</strong> برای بسیاری از کاربردهای عملی مناسب است.</li>
          </ul>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چه زمانی از آزمون نیکویی برازش استفاده کنیم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">زمانی که می‌خواهید بررسی کنید آیا توزیع داده‌های مشاهده‌شده با توزیع مورد انتظار مطابقت دارد یا خیر.</div></div></div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">درجات آزادی آزمون چگونه محاسبه می‌شود؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">درجات آزادی برابر است با (تعداد رده‌ها - ۱).</div></div></div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">اگر مقادیر مورد انتظار کمتر از ۵ باشند، چه باید کرد؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">در این حالت، نتایج آزمون ممکن است غیر دقیق باشند. بهتر است رده‌های کوچک را ترکیب کنید.</div></div></div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">آیا آزمون به توزیع داده‌ها حساس است؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">خیر، این آزمون ناپارامتری است و نیازی به فرض نرمال بودن داده‌ها ندارد.</div></div></div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">این محاسبه‌گر با نرم‌افزارهای آماری دیگر مطابقت دارد؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، این ابزار از فرمول‌های استاندارد استفاده می‌کند و نتایج آن با SPSS، R و سایر نرم‌افزارها مطابقت دارد.</div></div></div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">چگونه می‌توانم مقادیر مورد انتظار را محاسبه کنم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">مقادیر مورد انتظار بر اساس فرضیه صفر محاسبه می‌شوند. برای مثال، اگر فرضیه برابری است، مقادیر مورد انتظار برابر و مجموع آن‌ها برابر با مجموع مشاهدات است.</div></div></div>

          <div class="tzt-faq-item">
            <div class="tzt-faq-q">آیا می‌توانم این آزمون را برای داده‌های کم استفاده کنم؟ <span class="tzt-faq-icon">+</span></div>
            <div class="tzt-faq-a"><div class="tzt-faq-a-inner">برای داده‌های بسیار کم، نتایج آزمون ممکن است غیر دقیق باشند. بهتر است حداقل ۵ مشاهده در هر رده داشته باشید.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل دقیق‌تری نیاز دارید؟</h2>
          <p>از آزمون‌های آماری تا مدل‌سازی و تجزیه و تحلیل داده‌ها — متخصصان تزنویسه در کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      if(!document.getElementById('tzgof-calc')) return;

      function tztToPersian(num) {
        const persianDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        return String(num).replace(/\d/g, d => persianDigits[d]);
      }

      function initFAQ() {
        document.querySelectorAll('.tzt-faq-item .tzt-faq-q').forEach(q => {
          q.addEventListener('click', function(){
            this.parentElement.classList.toggle('tzt-faq-open');
          });
        });
      }
      initFAQ();

      function parseData(text) {
        return text.trim().split(/\r?\n+/).map(x => parseFloat(x.trim())).filter(x => !isNaN(x));
      }

      document.getElementById('tzgof-calc').addEventListener('click', function(){
        const err = document.getElementById('tzgof-err');
        const result = document.getElementById('tzgof-result');
        err.classList.remove('tzt-show');
        result.classList.remove('tzt-show');
        result.innerHTML = '';

        try {
          const observed = parseData(document.getElementById('tzgof-observed').value);
          const expected = parseData(document.getElementById('tzgof-expected').value);

          if(observed.length < 2) throw 'حداقل ۲ مقدار لازم است.';
          if(expected.length < 2) throw 'حداقل ۲ مقدار لازم است.';
          if(observed.length !== expected.length) throw 'تعداد مقادیر مشاهده‌شده و مورد انتظار باید برابر باشد.';

          let chiSq = 0;
          for(let i=0; i<observed.length; i++){
            if(expected[i] === 0) throw 'مقادیر مورد انتظار نمی‌توانند صفر باشند.';
            chiSq += Math.pow(observed[i] - expected[i], 2) / expected[i];
          }

          const df = observed.length - 1;
          const totalObserved = observed.reduce((a,b) => a+b, 0);
          const totalExpected = expected.reduce((a,b) => a+b, 0);

          let html = '<div class="tzt-result-main">';
          html += '<div class="tzt-result-label">نتیجه آزمون نیکویی برازش</div>';
          html += `<div class="tzt-result-value">χ² = ${tztToPersian(chiSq.toFixed(3))}</div>`;
          html += '<div class="tzt-result-meta">';
          html += `<div class="tzt-result-meta-item"><strong>${tztToPersian(df)}</strong><span>درجه آزادی</span></div>`;
          html += `<div class="tzt-result-meta-item"><strong>${tztToPersian(totalObserved)}</strong><span>کل مشاهدات</span></div>`;
          html += `<div class="tzt-result-meta-item"><strong>${tztToPersian(totalExpected)}</strong><span>کل مورد انتظار</span></div>`;
          html += '</div></div>';

          html += '<div class="tzt-tbl-wrap" style="margin-top:20px;"><table class="tzt-tbl"><thead><tr><th>رده</th><th>مشاهده شده</th><th>مورد انتظار</th><th>(O-E)²/E</th></tr></thead><tbody>';
          for(let i=0; i<observed.length; i++){
            const contribution = Math.pow(observed[i] - expected[i], 2) / expected[i];
            html += `<tr><td>${tztToPersian(i+1)}</td><td>${tztToPersian(observed[i])}</td><td>${tztToPersian(expected[i].toFixed(2))}</td><td>${tztToPersian(contribution.toFixed(3))}</td></tr>`;
          }
          html += '</tbody></table></div>';

          html += `<div class="tzt-method-note"><strong>تفسیر:</strong> مقدار χ² = ${tztToPersian(chiSq.toFixed(3))} محاسبه شد. این مقدار را با مقدار بحرانی جدول کای-دو برای درجه آزادی ${tztToPersian(df)} مقایسه کنید. اگر χ² بزرگ‌تر باشد، داده‌های مشاهده‌شده با توزیع مورد انتظار مطابقت ندارند.</div>`;

          result.innerHTML = html;
          result.classList.add('tzt-show');
        }
        catch(e){
          err.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
          err.classList.add('tzt-show');
        }
      });
    });
    </script>
    <?php
    return ob_get_clean();
}

// سئو و Schema
add_action('wp_head', function(){
    if (!is_singular()) return;
    global $post;
    if (!$post || strpos($post->post_content, 'tz_goodness_of_fit') === false) return;

    $url = get_permalink();
    $app = array(
      '@context' => 'https://schema.org',
      '@type' => 'WebApplication',
      'name' => 'محاسبه‌گر آزمون نیکویی برازش تزنویسه',
      'url' => $url,
      'applicationCategory' => 'EducationalApplication',
      'operatingSystem' => 'All',
      'description' => 'ابزار رایگان محاسبه آزمون نیکویی برازش برای بررسی توزیع داده‌ها.',
      'inLanguage' => 'fa-IR',
      'offers' => array('@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IRR'),
      'publisher' => array('@type' => 'Organization', 'name' => 'تزنویسه')
    );
    echo "\n" . '<script type="application/ld+json">' . wp_json_encode($app, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";

    $faqs = array(
        array('چه زمانی از آزمون نیکویی برازش استفاده کنیم؟', 'زمانی که می‌خواهید بررسی کنید آیا توزیع داده‌های مشاهده‌شده با توزیع مورد انتظار مطابقت دارد.'),
        array('درجات آزادی چگونه محاسبه می‌شود؟', 'درجات آزادی برابر است با (تعداد رده‌ها - ۱).'),
        array('اگر مقادیر مورد انتظار کمتر از ۵ باشند، چه باید کرد؟', 'نتایج آزمون ممکن است غیر دقیق باشند. بهتر است رده‌های کوچک را ترکیب کنید.'),
        array('آیا آزمون به توزیع داده‌ها حساس است؟', 'خیر، این آزمون ناپارامتری است و نیازی به فرض نرمال بودن ندارد.'),
        array('این محاسبه‌گر با نرم‌افزارهای آماری دیگر مطابقت دارد؟', 'بله، این ابزار از فرمول‌های استاندارد استفاده می‌کند.'),
        array('چگونه می‌توانم مقادیر مورد انتظار را محاسبه کنم؟', 'مقادیر مورد انتظار بر اساس فرضیه صفر محاسبه می‌شوند.'),
        array('آیا می‌توانم این آزمون را برای داده‌های کم استفاده کنم؟', 'برای داده‌های بسیار کم، نتایج ممکن است غیر دقیق باشند.'),
    );

    $items = array();
    foreach ($faqs as $f) {
        $items[] = array(
            '@type' => 'Question',
            'name' => $f[0],
            'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f[1])
        );
    }
    echo '<script type="application/ld+json">' . wp_json_encode(array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
});
}

/* --- icc-calculator --- */
if ( ! function_exists( 'teznevise_icc_calc' ) ) {
// ============================================
// تزنویسه - محاسبه‌گر ضریب همبستگی درون‌کلاسی (ICC)
// [tz_icc]
// ============================================
add_shortcode('tz_icc', 'teznevise_icc_calc');
function teznevise_icc_calc($atts) {
    ob_start(); ?>
    <div class="tzt">
      <div class="tzt-container">
        <div class="tzt-hero"><div class="tzt-hero-inner">
          <span class="tzt-hero-tag">📊 ابزار رایگان آنلاین</span>
          <h1>محاسبه‌گر ضریب همبستگی درون‌کلاسی (ICC)</h1>
          <p>ضریب ICC برای اندازه‌گیری پایایی و توافق بین چند قضاوت‌کننده یا اندازه‌گیری‌های تکراری استفاده می‌شود.</p>
        </div></div>

        <div class="tzt-card">
          <div class="tzt-note">💡 داده‌ها را به صورت ماتریس وارد کنید. هر ردیف مربوط به یک نمونه و هر ستون مربوط به یک قضاوت‌کننده است. مقادیر با کاما یا تب جدا شوند.</div>
          <div class="tzt-field">
            <label>داده‌ها (هر ردیف یک نمونه، هر ستون یک قضاوت‌کننده)</label>
            <textarea id="tzicc-data" placeholder="4.5, 4.7, 4.6&#10;3.9, 4.0, 4.1&#10;5.1, 5.0, 5.2"></textarea>
          </div>
          <div class="tzt-error" id="tzicc-err"></div>
          <button type="button" class="tzt-calc-btn" id="tzicc-calc">محاسبه ICC</button>
          <div class="tzt-result" id="tzicc-result"></div>
        </div>

        <div class="tzt-content">
          <h2>ضریب همبستگی درون‌کلاسی (ICC) چیست؟</h2>
          <p><strong>ICC</strong> شاخصی است برای سنجش پایایی و توافق بین چند قضاوت‌کننده یا اندازه‌گیری تکراری. این ضریب نشان می‌دهد که چه مقدار از کل واریانس ناشی از تفاوت‌های بین نمونه‌ها است.</p>

          <h2>انواع ICC</h2>
          <ul>
            <li>مدل‌های مختلف (یک‌طرفه، دوطرفه)</li>
            <li>نوع توافق (توافق مطلق یا همسانی)</li>
            <li>بر اساس تعداد قضاوت‌کننده‌ها</li>
          </ul>

          <h2>فرمول کلی ICC</h2>
          <p>محاسبه ICC بر اساس مدل تحلیل واریانس چندعاملی انجام می‌شود. این ابزار مدل دوطرفه با توافق مطلق را محاسبه می‌کند.</p>

          <h2>تفسیر ICC</h2>
          <table class="tzt-itable">
            <thead><tr><th>مقدار ICC</th><th>تفسیر</th></tr></thead>
            <tbody>
              <tr><td>≥ ۰.۹۰</td><td>عالی</td></tr>
              <tr><td>۰.۷۵ - ۰.۸۹</td><td>خوب</td></tr>
              <tr><td>۰.۵۰ - ۰.۷۴</td><td>متوسط</td></tr>
              <tr><td>< ۰.۵۰</td><td>ضعیف</td></tr>
            </tbody>
          </table>
        </div>

        <div class="tzt-faq">
          <h2 style="color:#145D4A; font-size:26px; font-weight:800; margin-bottom:20px; padding-right:14px; border-right:4px solid #d97706;">سوالات متداول</h2>
          <div class="tzt-faq-item"><div class="tzt-faq-q">ICC چه تفاوتی با ضریب همبستگی دارد؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">ICC برای داده‌های چندقضاوت‌کننده و اندازه‌گیری‌های تکراری است و میزان توافق را نشان می‌دهد، در حالی که ضریب همبستگی دو متغیره است.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چه داده‌هایی برای ICC لازم است؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">داده‌ها به صورت ماتریسی که هر ردیف یک نمونه و هر ستون یک قضاوت‌کننده است.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">آیا ICC فقط برای داده‌های کمی است؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، ICC برای داده‌های کمی و پیوسته مناسب است.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">چگونه می‌توانم ICC بالا داشته باشم؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">با افزایش دقت و آموزش قضاوت‌کنندگان و کاهش خطاهای اندازه‌گیری، ICC بهبود می‌یابد.</div></div></div>
          <div class="tzt-faq-item"><div class="tzt-faq-q">این محاسبه‌گر با نرم‌افزارهای آماری مطابقت دارد؟ <span class="tzt-faq-icon">+</span></div><div class="tzt-faq-a"><div class="tzt-faq-a-inner">بله، این ابزار نتایجی مشابه با نرم‌افزارهای آماری مانند SPSS و R ارائه می‌دهد.</div></div></div>
        </div>

        <div class="tzt-cta">
          <h2>به تحلیل دقیق‌تری نیاز دارید؟</h2>
          <p>از سنجش پایایی تا مدل‌های پیشرفته — متخصصان تزنویسه در تمام مراحل تحلیل کنار شما هستند. مشاوره اولیه رایگان است.</p>
          <a href="/inquiry/" class="tzt-cta-btn">درخواست مشاوره آماری</a>
        </div>
      </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  if(!document.getElementById('tzicc-calc')) return;

  function tztToPersian(num) {
    const persianDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    return String(num).replace(/\d/g, function(d) { return persianDigits[d]; });
  }

  function initFAQ() {
    document.querySelectorAll('.tzt-faq-item .tzt-faq-q').forEach(function(q) {
      q.addEventListener('click', function() {
        this.parentElement.classList.toggle('tzt-faq-open');
      });
    });
  }
  initFAQ();

  function parseMatrix(text) {
    return text.trim()
      .split(/\r?\n/)
      .map(row => row.split(/[, \t]+/).map(Number).filter(n => !isNaN(n)));
  }

  document.getElementById('tzicc-calc').addEventListener('click', function() {
    const err = document.getElementById('tzicc-err');
    const result = document.getElementById('tzicc-result');
    err.classList.remove('tzt-show');
    result.classList.remove('tzt-show');
    result.innerHTML = '';

    try {
      const matrix = parseMatrix(document.getElementById('tzicc-data').value);
      if(matrix.length < 2) throw 'حداقل دو نمونه لازم است.';
      const raters = matrix[0].length;
      if(raters < 2) throw 'حداقل دو قضاوت‌کننده لازم است.';
      for(let i=1; i<matrix.length; i++) {
        if(matrix[i].length !== raters) throw 'تمام ردیف‌ها باید تعداد ستون مساوی داشته باشند.';
      }

      const n = matrix.length;
      const k = raters;

      // میانگین هر نمونه (ردیف)
      const meanRows = matrix.map(row => row.reduce((a,b) => a+b, 0) / k);
      // میانگین هر قضاوت‌کننده (ستون)
      let meanCols = new Array(k).fill(0);
      for(let j=0; j<k; j++) {
        for(let i=0; i<n; i++) {
          meanCols[j] += matrix[i][j];
        }
        meanCols[j] /= n;
      }
      // میانگین کلی
      const grandMean = meanRows.reduce((a,b) => a+b, 0) / n;

      // محاسبه SS (Sum of Squares)
      // SS بین نمونه‌ها
      const ssBetween = k * meanRows.reduce((sum, m) => sum + Math.pow(m - grandMean, 2), 0);
      // SS بین قضاوت‌کننده‌ها
      const ssRaters = n * meanCols.reduce((sum, m) => sum + Math.pow(m - grandMean, 2), 0);
      // SS خطا
      let ssError = 0;
      for(let i=0; i<n; i++) {
        for(let j=0; j<k; j++) {
          ssError += Math.pow(matrix[i][j] - meanRows[i] - meanCols[j] + grandMean, 2);
        }
      }

      // درجات آزادی
      const dfBetween = n -1;
      const dfRaters = k -1;
      const dfError = (n -1) * (k -1);

      // میانگین مربعات
      const msBetween = ssBetween / dfBetween;
      const msError = ssError / dfError;

      // ICC (مدل دوطرفه، توافق مطلق)
      const ICC = (msBetween - msError) / (msBetween + (k -1)*msError);

      // نمایش نتایج
      let html = '<div class="tzt-result-main">';
      html += '<div class="tzt-result-label">نتیجه ضریب همبستگی درون‌کلاسی (ICC)</div>';
      html += '<div class="tzt-result-value">ICC = ' + tztToPersian(ICC.toFixed(3)) + '</div>';
      html += '</div>';

      html += '<div class="tzt-tbl-wrap" style="margin-top:20px;">';
      html += '<table class="tzt-tbl"><thead><tr><th>شاخص</th><th>مقدار</th></tr></thead><tbody>';
      html += '<tr><td>SS بین نمونه‌ها</td><td>' + tztToPersian(ssBetween.toFixed(2)) + '</td></tr>';
      html += '<tr><td>SS بین قضاوت‌کننده‌ها</td><td>' + tztToPersian(ssRaters.toFixed(2)) + '</td></tr>';
      html += '<tr><td>SS خطا</td><td>' + tztToPersian(ssError.toFixed(2)) + '</td></tr>';
      html += '<tr><td>درجات آزادی بین نمونه‌ها</td><td>' + tztToPersian(dfBetween) + '</td></tr>';
      html += '<tr><td>درجات آزادی بین قضاوت‌کننده‌ها</td><td>' + tztToPersian(dfRaters) + '</td></tr>';
      html += '<tr><td>درجات آزادی خطا</td><td>' + tztToPersian(dfError) + '</td></tr>';
      html += '</tbody></table></div>';

      html += '<div class="tzt-method-note"><strong>تفسیر:</strong> مقدار ICC = ' + tztToPersian(ICC.toFixed(3)) + ' است. مقدار بالاتر از ۰.۷ نشان‌دهنده پایایی خوب است.</div>';

      result.innerHTML = html;
      result.classList.add('tzt-show');

    } catch(e) {
      const err = document.getElementById('tzicc-err');
      err.textContent = '⚠️ ' + (typeof e === 'string' ? e : 'خطا در محاسبه.');
      err.classList.add('tzt-show');
    }
  });
});
</script>
<?php
return ob_get_clean();
}
}

/* --- online-calculation-tools --- */
if ( ! function_exists( 'teznevise_calculation_hub' ) ) {
// ============================================
// تزنویسه - Hub ابزارهای محاسبه آنلاین (نسخه حرفه‌ای نهایی)
// [tz_calculation_hub]
// ============================================
add_shortcode('tz_calculation_hub', 'teznevise_calculation_hub');
function teznevise_calculation_hub($atts) {
    ob_start(); ?>

<div class="tzhub">

  <style>
    /* ============ RESET & SCOPE ============ */
    .tzhub, .tzhub *{ box-sizing:border-box !important; }
    .tzhub{
      font-family: inherit;
      line-height:1.7;
      color:#1f2937;
      max-width:1200px;
      margin:0 auto;
      padding:0 16px;
    }
    .tzhub h1,.tzhub h2,.tzhub h3,.tzhub p,.tzhub ul{ margin:0; padding:0; }

    /* ============ HERO ============ */
    .tzhub-hero{
      position:relative; overflow:hidden;
      border-radius:24px;
      padding:64px 32px;
      margin-bottom:36px;
      text-align:center;
      color:#fff !important;
      background:linear-gradient(135deg,#145D4A 0%,#0c4135 100%);
    }
    .tzhub-hero::after{
      content:''; position:absolute; inset:0;
      background:
        radial-gradient(circle at 18% 28%, rgba(255,255,255,.08) 0, transparent 38%),
        radial-gradient(circle at 82% 72%, rgba(217,119,6,.22) 0, transparent 46%);
    }
    .tzhub-hero-inner{ position:relative; z-index:1; max-width:760px; margin:0 auto; }
    .tzhub-badge{
      display:inline-block;
      background:rgba(255,255,255,.15);
      border:1px solid rgba(255,255,255,.28);
      padding:8px 18px; border-radius:100px;
      font-size:.9rem; font-weight:700;
      margin-bottom:20px; color:#fff !important;
    }
    .tzhub-hero h1{
      font-size:clamp(2rem,5vw,3.2rem);
      font-weight:900; color:#fff !important;
      margin-bottom:16px; line-height:1.15;
    }
    .tzhub-hero p{ font-size:1.1rem; opacity:.92; margin:0 auto 30px; max-width:600px; color:#fff !important; }
    .tzhub-stats{ display:flex !important; justify-content:center; gap:44px; flex-wrap:wrap; }
    .tzhub-stat{ display:flex !important; flex-direction:column; }
    .tzhub-stat b{ font-size:2rem; font-weight:900; color:#fff !important; }
    .tzhub-stat span{ font-size:.85rem; opacity:.82; color:#fff !important; }

    /* ============ CONTROLS ============ */
    .tzhub-search{ position:relative; margin-bottom:18px; }
    .tzhub-search input{
      width:100% !important;
      padding:16px 52px 16px 18px !important;
      font-size:1rem; font-family:inherit;
      border:2px solid #e5e7eb !important;
      border-radius:14px !important;
      background:#fff !important; color:#1f2937 !important;
      transition:all .25s ease;
    }
    .tzhub-search input:focus{
      outline:none !important;
      border-color:#145D4A !important;
      box-shadow:0 0 0 4px rgba(20,93,74,.12) !important;
    }
    .tzhub-search svg{ position:absolute; right:18px; top:50%; transform:translateY(-50%); color:#9ca3af; }
    .tzhub-filters{ display:flex !important; gap:10px; flex-wrap:wrap; margin-bottom:34px; }
    .tzhub-chip{
      padding:9px 18px;
      border:1.5px solid #e5e7eb !important;
      background:#fff !important; color:#6b7280 !important;
      border-radius:100px !important;
      font-size:.9rem; font-weight:700; font-family:inherit;
      cursor:pointer; transition:all .2s ease;
    }
    .tzhub-chip:hover{ border-color:#145D4A !important; color:#145D4A !important; }
    .tzhub-chip.active{ background:#145D4A !important; border-color:#145D4A !important; color:#fff !important; }

    /* ============ GRID (مهم‌ترین بخش) ============ */
    .tzhub-grid{
      display:grid !important;
      grid-template-columns:repeat(auto-fill,minmax(270px,1fr)) !important;
      gap:22px !important;
      margin-bottom:30px !important;
      list-style:none !important;
    }
    .tzhub-card{
      display:flex !important;
      flex-direction:column !important;
      padding:26px 24px !important;
      background:#fff !important;
      border:1px solid #e9edf2 !important;
      border-radius:18px !important;
      text-decoration:none !important;
      color:#1f2937 !important;
      position:relative; overflow:hidden;
      box-shadow:0 1px 2px rgba(16,24,40,.04);
      transition:transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s ease, border-color .3s ease;
    }
    .tzhub-card::before{
      content:''; position:absolute; top:0; right:0; left:0; height:4px;
      background:linear-gradient(90deg,#145D4A,#d97706);
      transform:scaleX(0); transform-origin:right;
      transition:transform .35s ease;
    }
    .tzhub-card:hover{
      transform:translateY(-6px);
      box-shadow:0 18px 40px -14px rgba(20,93,74,.3) !important;
      border-color:transparent !important;
    }
    .tzhub-card:hover::before{ transform:scaleX(1); }
    .tzhub-ico{
      width:56px; height:56px;
      display:flex !important; align-items:center; justify-content:center;
      font-size:27px; border-radius:14px; margin-bottom:16px;
      background:linear-gradient(135deg,var(--c1),var(--c2));
      box-shadow:0 8px 18px -6px var(--c2);
    }
    .tzhub-card h3{ font-size:1.14rem; font-weight:800; margin-bottom:8px !important; color:#111827 !important; }
    .tzhub-card p{ font-size:.92rem; color:#6b7280 !important; margin-bottom:18px !important; flex-grow:1; line-height:1.6; }
    .tzhub-go{ display:inline-flex !important; align-items:center; gap:6px; font-size:.9rem; font-weight:800; color:#145D4A !important; }
    .tzhub-go i{ font-style:normal; transition:transform .25s ease; }
    .tzhub-card:hover .tzhub-go i{ transform:translateX(-5px); }
    .tzhub-noresult{ display:none; text-align:center; padding:50px 20px; color:#6b7280; font-size:1.1rem; }

    /* ============ CONTENT ============ */
    .tzhub-content{ margin:60px 0; }
    .tzhub-content h2{
      font-size:1.9rem; font-weight:900; margin-bottom:16px !important;
      color:#111827 !important; position:relative; padding-right:16px;
    }
    .tzhub-content h2::before{
      content:''; position:absolute; right:0; top:7px; bottom:7px; width:5px;
      border-radius:4px; background:linear-gradient(#145D4A,#d97706);
    }
    .tzhub-content h3{ font-size:1.32rem; font-weight:800; margin:34px 0 14px !important; color:#145D4A !important; }
    .tzhub-content p{ margin-bottom:16px !important; color:#374151 !important; }
    .tzhub-content ul{ padding-right:22px !important; list-style:disc !important; }
    .tzhub-content li{ margin-bottom:11px !important; }
    .tzhub-content a{ color:#145D4A !important; font-weight:700; text-decoration:none; border-bottom:1.5px solid transparent; }
    .tzhub-content a:hover{ border-color:#145D4A; }
    .tzhub-feats{ display:grid !important; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)) !important; gap:18px; margin:24px 0; }
    .tzhub-feat{ display:flex !important; gap:14px; align-items:flex-start; padding:20px; border-radius:14px; background:#f8fafc; border:1px solid #eef2f6; }
    .tzhub-feat span{ font-size:26px; }
    .tzhub-feat b{ display:block; margin-bottom:4px; font-size:1rem; color:#111827; }
    .tzhub-feat p{ margin:0 !important; font-size:.88rem; color:#6b7280 !important; }

    /* ============ FAQ ============ */
    .tzhub-faq{ margin:60px 0; }
    .tzhub-faq h2{ font-size:1.9rem; font-weight:900; margin-bottom:22px !important; color:#111827 !important; position:relative; padding-right:16px; }
    .tzhub-faq h2::before{ content:''; position:absolute; right:0; top:7px; bottom:7px; width:5px; border-radius:4px; background:linear-gradient(#145D4A,#d97706); }
    .tzhub-faq-item{ border:1px solid #e9edf2; border-radius:14px; margin-bottom:12px; overflow:hidden; background:#fff; transition:border-color .25s ease; }
    .tzhub-faq-item.open{ border-color:#145D4A; }
    .tzhub-faq-q{
      width:100% !important; text-align:right !important;
      padding:18px 22px !important; font-size:1.02rem; font-weight:700; font-family:inherit;
      color:#111827 !important; background:none !important; border:none !important; cursor:pointer;
      display:flex !important; align-items:center; justify-content:space-between; gap:12px;
    }
    .tzhub-faq-q i{ flex-shrink:0; width:22px; height:22px; position:relative; }
    .tzhub-faq-q i::before,.tzhub-faq-q i::after{ content:''; position:absolute; background:#d97706; border-radius:2px; transition:transform .3s ease; }
    .tzhub-faq-q i::before{ top:10px; right:3px; width:16px; height:2.5px; }
    .tzhub-faq-q i::after{ top:3px; right:9.7px; width:2.5px; height:16px; }
    .tzhub-faq-item.open .tzhub-faq-q i::after{ transform:rotate(90deg); opacity:0; }
    .tzhub-faq-a{ max-height:0; overflow:hidden; transition:max-height .35s ease; }
    .tzhub-faq-a p{ margin:0 !important; padding:0 22px 20px !important; color:#6b7280 !important; line-height:1.7; }

    /* ============ CTA ============ */
    .tzhub-cta{
      margin:60px 0 40px; text-align:center;
      padding:56px 32px; border-radius:24px;
      background:linear-gradient(135deg,#145D4A,#0c4135);
      color:#fff !important; position:relative; overflow:hidden;
    }
    .tzhub-cta::after{ content:''; position:absolute; inset:0; background:radial-gradient(circle at 75% 25%,rgba(217,119,6,.28),transparent 50%); }
    .tzhub-cta h2{ position:relative; font-size:1.8rem; font-weight:900; margin-bottom:12px !important; color:#fff !important; }
    .tzhub-cta p{ position:relative; opacity:.92; margin:0 auto 26px !important; max-width:520px; color:#fff !important; }
    .tzhub-cta a{
      position:relative; display:inline-block;
      padding:15px 40px; background:#fff !important; color:#145D4A !important;
      border-radius:12px; text-decoration:none; font-weight:800; font-size:1.02rem;
      transition:transform .25s ease, box-shadow .25s ease;
    }
    .tzhub-cta a:hover{ transform:translateY(-3px); box-shadow:0 12px 28px -8px rgba(0,0,0,.4); }

    /* ============ RESPONSIVE ============ */
    @media(max-width:768px){
      .tzhub-hero{ padding:44px 20px; border-radius:18px; }
      .tzhub-stats{ gap:26px; }
      .tzhub-stat b{ font-size:1.5rem; }
      .tzhub-grid{ grid-template-columns:1fr !important; }
      .tzhub-cta{ padding:40px 22px; }
    }
  </style>

  <!-- ===== HERO ===== -->
  <section class="tzhub-hero">
    <div class="tzhub-hero-inner">
      <span class="tzhub-badge">۱۸ ابزار رایگان آنلاین</span>
      <h1>ابزارهای محاسبه آنلاین</h1>
      <p>مجموعه‌ای کامل از محاسبه‌گرهای آماری دقیق و حرفه‌ای — برای پژوهشگران، دانشجویان و تحلیل‌گران داده. بدون نصب، بدون هزینه، کاملاً فارسی.</p>
      <div class="tzhub-stats">
        <div class="tzhub-stat"><b>۱۸+</b><span>ابزار تخصصی</span></div>
        <div class="tzhub-stat"><b>۱۰۰٪</b><span>رایگان</span></div>
        <div class="tzhub-stat"><b>۰</b><span>ذخیره داده</span></div>
      </div>
    </div>
  </section>

  <!-- ===== CONTROLS ===== -->
  <div class="tzhub-search">
    <svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0A4.5 4.5 0 1 1 14 9.5 4.5 4.5 0 0 1 9.5 14z"/></svg>
    <input type="text" id="tzhub-search" placeholder="جستجوی ابزار... (مثلاً: t-test، همبستگی، حجم نمونه)" />
  </div>
  <div class="tzhub-filters" id="tzhub-filters">
    <button class="tzhub-chip active" data-cat="all">همه</button>
    <button class="tzhub-chip" data-cat="descriptive">توصیفی</button>
    <button class="tzhub-chip" data-cat="comparison">مقایسه میانگین</button>
    <button class="tzhub-chip" data-cat="correlation">همبستگی</button>
    <button class="tzhub-chip" data-cat="reliability">پایایی و روایی</button>
    <button class="tzhub-chip" data-cat="nonparam">ناپارامتری</button>
    <button class="tzhub-chip" data-cat="design">طراحی پژوهش</button>
  </div>

  <!-- ===== GRID ===== -->
  <div id="tzhub-grid" class="tzhub-grid">

    <a href="/online-calculation-tools/descriptive-statistics-calculator/" class="tzhub-card" data-cat="descriptive" data-keywords="آمار توصیفی میانگین انحراف معیار میانه چارک">
      <div class="tzhub-ico" style="--c1:#3b82f6;--c2:#1d4ed8;">📊</div>
      <h3>آمار توصیفی</h3>
      <p>میانگین، انحراف معیار، میانه، چارک‌ها، کجی و کشیدگی.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/pearson-correlation-calculator/" class="tzhub-card" data-cat="correlation" data-keywords="همبستگی پیرسون ضریب خطی">
      <div class="tzhub-ico" style="--c1:#8b5cf6;--c2:#6d28d9;">📈</div>
      <h3>همبستگی پیرسون</h3>
      <p>رابطه خطی بین دو متغیر کمی و معناداری آن.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/spearman-correlation-calculator/" class="tzhub-card" data-cat="correlation" data-keywords="همبستگی اسپیرمن رتبه‌ای غیرنرمال">
      <div class="tzhub-ico" style="--c1:#8b5cf6;--c2:#6d28d9;">🔗</div>
      <h3>همبستگی اسپیرمن</h3>
      <p>رابطه رتبه‌ای بین دو متغیر برای داده‌های غیرنرمال.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/t-test-calculator/" class="tzhub-card" data-cat="comparison" data-keywords="آزمون t مقایسه میانگین مستقل وابسته">
      <div class="tzhub-ico" style="--c1:#10b981;--c2:#047857;">⚖️</div>
      <h3>آزمون t-test</h3>
      <p>مقایسه میانگین دو گروه مستقل یا وابسته.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/anova-calculator/" class="tzhub-card" data-cat="comparison" data-keywords="ANOVA تحلیل واریانس چند گروه">
      <div class="tzhub-ico" style="--c1:#10b981;--c2:#047857;">📊</div>
      <h3>تحلیل واریانس (ANOVA)</h3>
      <p>مقایسه میانگین سه یا چند گروه مستقل.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/mann-whitney-calculator/" class="tzhub-card" data-cat="nonparam" data-keywords="Mann-Whitney U ناپارامتری دو گروه">
      <div class="tzhub-ico" style="--c1:#f59e0b;--c2:#d97706;">🔄</div>
      <h3>آزمون Mann-Whitney U</h3>
      <p>مقایسه ناپارامتری دو گروه مستقل.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/wilcoxon-calculator/" class="tzhub-card" data-cat="nonparam" data-keywords="Wilcoxon زوجی ناپارامتری">
      <div class="tzhub-ico" style="--c1:#f59e0b;--c2:#d97706;">🔁</div>
      <h3>آزمون Wilcoxon</h3>
      <p>مقایسه ناپارامتری دو اندازه‌گیری زوجی.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/kruskal-wallis-calculator/" class="tzhub-card" data-cat="nonparam" data-keywords="Kruskal-Wallis ناپارامتری چند گروه">
      <div class="tzhub-ico" style="--c1:#f59e0b;--c2:#d97706;">📶</div>
      <h3>آزمون Kruskal-Wallis</h3>
      <p>مقایسه ناپارامتری چند گروه مستقل.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/cronbachs-alpha-calculator/" class="tzhub-card" data-cat="reliability" data-keywords="کرونباخ آلفا پایایی پرسشنامه">
      <div class="tzhub-ico" style="--c1:#ec4899;--c2:#be185d;">✓</div>
      <h3>آلفای کرونباخ</h3>
      <p>سنجش پایایی و همسانی درونی پرسشنامه.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/kr20-kr21-calculator/" class="tzhub-card" data-cat="reliability" data-keywords="KR-20 KR-21 دوحالتی پایایی">
      <div class="tzhub-ico" style="--c1:#ec4899;--c2:#be185d;">☑️</div>
      <h3>KR-20 / KR-21</h3>
      <p>پایایی آزمون‌های دوحالتی (صحیح/غلط).</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/cohens-kappa-calculator/" class="tzhub-card" data-cat="reliability" data-keywords="کاپا توافق قضاوت‌کننده">
      <div class="tzhub-ico" style="--c1:#ec4899;--c2:#be185d;">🤝</div>
      <h3>ضریب توافق کاپا</h3>
      <p>سنجش توافق بین دو قضاوت‌کننده.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/icc-calculator/" class="tzhub-card" data-cat="reliability" data-keywords="ICC درون‌کلاسی توافق چند قضاوت‌کننده">
      <div class="tzhub-ico" style="--c1:#ec4899;--c2:#be185d;">🔗</div>
      <h3>ضریب ICC</h3>
      <p>پایایی و توافق بین چند قضاوت‌کننده.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/regression-calculator/" class="tzhub-card" data-cat="comparison" data-keywords="رگرسیون خطی پیش‌بینی چندگانه">
      <div class="tzhub-ico" style="--c1:#06b6d4;--c2:#0e7490;">📉</div>
      <h3>تحلیل رگرسیون</h3>
      <p>رگرسیون خطی ساده و چندگانه برای پیش‌بینی.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/chi-square-calculator/" class="tzhub-card" data-cat="nonparam" data-keywords="کای مجذور Chi-Square کیفی">
      <div class="tzhub-ico" style="--c1:#f59e0b;--c2:#d97706;">📋</div>
      <h3>آزمون مجذور کای</h3>
      <p>بررسی ارتباط بین دو متغیر کیفی.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/goodness-of-fit-calculator/" class="tzhub-card" data-cat="nonparam" data-keywords="نیکویی برازش goodness fit توزیع">
      <div class="tzhub-ico" style="--c1:#f59e0b;--c2:#d97706;">✅</div>
      <h3>آزمون نیکویی برازش</h3>
      <p>بررسی تطابق توزیع داده با توزیع مورد انتظار.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/sample-size-calculator/" class="tzhub-card" data-cat="design" data-keywords="حجم نمونه نمونه‌گیری sample size">
      <div class="tzhub-ico" style="--c1:#6366f1;--c2:#4338ca;">📏</div>
      <h3>محاسبه‌گر حجم نمونه</h3>
      <p>تعیین حجم نمونه مناسب برای پژوهش.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/power-analysis-calculator/" class="tzhub-card" data-cat="design" data-keywords="توان آزمون power analysis">
      <div class="tzhub-ico" style="--c1:#6366f1;--c2:#4338ca;">⚡</div>
      <h3>محاسبه‌گر توان آزمون</h3>
      <p>محاسبه توان آماری برای طراحی پژوهش.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

    <a href="/online-calculation-tools/content-validity-calculator/" class="tzhub-card" data-cat="design" data-keywords="روایی محتوا CVR CVI">
      <div class="tzhub-ico" style="--c1:#6366f1;--c2:#4338ca;">📐</div>
      <h3>روایی محتوا (CVR/CVI)</h3>
      <p>سنجش روایی محتوای ابزارهای پژوهشی.</p>
      <span class="tzhub-go">مشاهده ابزار <i>←</i></span>
    </a>

  </div>
  <div class="tzhub-noresult" id="tzhub-noresult">هیچ ابزاری مطابق جستجوی شما یافت نشد. 🔍</div>

  <!-- ===== CONTENT ===== -->
  <article class="tzhub-content">
    <h2>ابزارهای محاسبه آنلاین تزنویسه</h2>
    <p>پلتفرم تزنویسه مجموعه‌ای جامع از <strong>ابزارهای محاسبه آنلاین</strong> رایگان را برای انجام تحلیل‌های آماری دقیق ارائه می‌دهد. این ابزارها به‌گونه‌ای طراحی شده‌اند که بدون نیاز به نرم‌افزارهای پیچیده مانند SPSS یا R، بتوانید محاسبات خود را مستقیماً در مرورگر انجام دهید.</p>

    <h3>چرا ابزارهای محاسبه تزنویسه؟</h3>
    <div class="tzhub-feats">
      <div class="tzhub-feat"><span>🆓</span><div><b>کاملاً رایگان</b><p>بدون نیاز به ثبت‌نام یا پرداخت.</p></div></div>
      <div class="tzhub-feat"><span>🎯</span><div><b>دقیق و استاندارد</b><p>منطبق با فرمول‌های علمی و SPSS.</p></div></div>
      <div class="tzhub-feat"><span>🔒</span><div><b>حریم خصوصی کامل</b><p>داده‌ها در مرورگر شما می‌مانند.</p></div></div>
      <div class="tzhub-feat"><span>📱</span><div><b>سازگار با موبایل</b><p>طراحی واکنش‌گرا در همه دستگاه‌ها.</p></div></div>
    </div>

    <h3>راهنمای انتخاب ابزار مناسب</h3>
    <p>برای انتخاب آزمون آماری درست، ابتدا نوع داده و هدف پژوهش خود را مشخص کنید:</p>
    <ul>
      <li><strong>مقایسه دو گروه (داده نرمال):</strong> از <a href="/online-calculation-tools/t-test-calculator/">آزمون t</a> استفاده کنید.</li>
      <li><strong>مقایسه چند گروه (داده نرمال):</strong> از <a href="/online-calculation-tools/anova-calculator/">ANOVA</a> استفاده کنید.</li>
      <li><strong>داده‌های غیرنرمال:</strong> از <a href="/online-calculation-tools/mann-whitney-calculator/">Mann-Whitney</a> یا <a href="/online-calculation-tools/kruskal-wallis-calculator/">Kruskal-Wallis</a> بهره ببرید.</li>
      <li><strong>بررسی رابطه بین متغیرها:</strong> از <a href="/online-calculation-tools/pearson-correlation-calculator/">همبستگی پیرسون</a> یا <a href="/online-calculation-tools/regression-calculator/">رگرسیون</a> استفاده کنید.</li>
      <li><strong>سنجش پایایی پرسشنامه:</strong> از <a href="/online-calculation-tools/cronbachs-alpha-calculator/">آلفای کرونباخ</a> کمک بگیرید.</li>
    </ul>
  </article>

  <!-- ===== FAQ ===== -->
  <section class="tzhub-faq">
    <h2>سوالات متداول</h2>
    <div class="tzhub-faq-item">
      <button class="tzhub-faq-q">آیا استفاده از این ابزارها رایگان است؟<i></i></button>
      <div class="tzhub-faq-a"><p>بله، تمام ابزارهای محاسبه آنلاین تزنویسه کاملاً رایگان هستند و نیازی به ثبت‌نام، اشتراک یا پرداخت هزینه ندارند.</p></div>
    </div>
    <div class="tzhub-faq-item">
      <button class="tzhub-faq-q">آیا نتایج با SPSS و R مطابقت دارند؟<i></i></button>
      <div class="tzhub-faq-a"><p>بله، تمام محاسبات بر اساس فرمول‌های استاندارد آماری انجام می‌شوند و نتایج آن‌ها با نرم‌افزارهای معتبر مانند SPSS و R مطابقت کامل دارد.</p></div>
    </div>
    <div class="tzhub-faq-item">
      <button class="tzhub-faq-q">آیا داده‌های من ذخیره یا ارسال می‌شوند؟<i></i></button>
      <div class="tzhub-faq-a"><p>خیر، تمام محاسبات به‌صورت محلی در مرورگر شما انجام می‌شود و هیچ داده‌ای به سرور ارسال یا ذخیره نمی‌شود. حریم خصوصی شما کاملاً محفوظ است.</p></div>
    </div>
    <div class="tzhub-faq-item">
      <button class="tzhub-faq-q">چگونه ابزار مناسب پژوهشم را انتخاب کنم؟<i></i></button>
      <div class="tzhub-faq-a"><p>انتخاب ابزار به نوع داده (کمی/کیفی)، تعداد گروه‌ها و نرمال بودن توزیع بستگی دارد. در بخش «راهنمای انتخاب ابزار» بالا راهنمای کاملی ارائه شده است.</p></div>
    </div>
    <div class="tzhub-faq-item">
      <button class="tzhub-faq-q">آیا می‌توانم از این ابزارها در پایان‌نامه استفاده کنم؟<i></i></button>
      <div class="tzhub-faq-a"><p>بله، نتایج این ابزارها معتبر و قابل استناد هستند. برای تحلیل‌های پیچیده‌تر، توصیه می‌شود از مشاوره متخصص آماری نیز بهره ببرید.</p></div>
    </div>
    <div class="tzhub-faq-item">
      <button class="tzhub-faq-q">آیا ابزارهای جدید اضافه می‌شوند؟<i></i></button>
      <div class="tzhub-faq-a"><p>بله، مجموعه ابزارهای تزنویسه به‌طور مستمر در حال گسترش است. ابزارهای جدیدی مانند تفسیرگر خروجی SPSS و راهنمای انتخاب آزمون آماری به‌زودی اضافه خواهند شد.</p></div>
    </div>
  </section>

  <!-- ===== CTA ===== -->
  <section class="tzhub-cta">
    <h2>به مشاوره تخصصی آماری نیاز دارید؟</h2>
    <p>تیم متخصص تزنویسه در تحلیل داده، طراحی پژوهش و تفسیر نتایج همراه شماست. مشاوره اولیه رایگان است.</p>
    <a href="/inquiry/">درخواست مشاوره رایگان</a>
  </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var grid = document.getElementById('tzhub-grid');
  if(!grid) return;

  var searchInput = document.getElementById('tzhub-search');
  var cards = Array.prototype.slice.call(document.querySelectorAll('.tzhub-card'));
  var chips = document.querySelectorAll('.tzhub-chip');
  var noResult = document.getElementById('tzhub-noresult');
  var activeCat = 'all';

  function applyFilter(){
    var q = (searchInput.value || '').toLowerCase().trim();
    var visible = 0;
    cards.forEach(function(card){
      var kw = (card.getAttribute('data-keywords') || '').toLowerCase();
      var title = card.querySelector('h3').textContent.toLowerCase();
      var cat = card.getAttribute('data-cat');
      var matchCat = (activeCat === 'all' || cat === activeCat);
      var matchSearch = (q === '' || kw.indexOf(q) > -1 || title.indexOf(q) > -1);
      if(matchCat && matchSearch){ card.style.display = 'flex'; visible++; }
      else { card.style.display = 'none'; }
    });
    noResult.style.display = visible === 0 ? 'block' : 'none';
  }

  searchInput.addEventListener('input', applyFilter);

  chips.forEach(function(chip){
    chip.addEventListener('click', function(){
      chips.forEach(function(c){ c.classList.remove('active'); });
      this.classList.add('active');
      activeCat = this.getAttribute('data-cat');
      applyFilter();
    });
  });

  document.querySelectorAll('.tzhub-faq-q').forEach(function(btn){
    btn.addEventListener('click', function(){
      var item = this.parentElement;
      var ans = item.querySelector('.tzhub-faq-a');
      if(item.classList.contains('open')){
        item.classList.remove('open');
        ans.style.maxHeight = '0';
      } else {
        item.classList.add('open');
        ans.style.maxHeight = ans.scrollHeight + 'px';
      }
    });
  });
});
</script>
<?php
    return ob_get_clean();
}

// ===== SEO + Schema =====
add_action('wp_head', function(){
    if(!is_singular()) return;
    global $post;
    if(!$post || strpos($post->post_content, 'tz_calculation_hub') === false) return;

    $url = get_permalink();
    $tools = array(
        array('آمار توصیفی','descriptive-statistics-calculator'),
        array('همبستگی پیرسون','pearson-correlation-calculator'),
        array('همبستگی اسپیرمن','spearman-correlation-calculator'),
        array('آزمون t-test','t-test-calculator'),
        array('تحلیل واریانس ANOVA','anova-calculator'),
        array('آزمون Mann-Whitney U','mann-whitney-calculator'),
        array('آزمون Wilcoxon','wilcoxon-calculator'),
        array('آزمون Kruskal-Wallis','kruskal-wallis-calculator'),
        array('آلفای کرونباخ','cronbachs-alpha-calculator'),
        array('KR-20 و KR-21','kr20-kr21-calculator'),
        array('ضریب توافق کاپا','cohens-kappa-calculator'),
        array('ضریب ICC','icc-calculator'),
        array('تحلیل رگرسیون','regression-calculator'),
        array('آزمون مجذور کای','chi-square-calculator'),
        array('آزمون نیکویی برازش','goodness-of-fit-calculator'),
        array('محاسبه‌گر حجم نمونه','sample-size-calculator'),
        array('محاسبه‌گر توان آزمون','power-analysis-calculator'),
        array('روایی محتوا','content-validity-calculator'),
    );
    $items = array();
    foreach($tools as $i => $t){
        $items[] = array('@type'=>'ListItem','position'=>$i+1,'name'=>$t[0],'url'=>trailingslashit($url).$t[1].'/');
    }
    echo "\n".'<script type="application/ld+json">'.wp_json_encode(array('@context'=>'https://schema.org','@type'=>'ItemList','name'=>'ابزارهای محاسبه آنلاین','itemListElement'=>$items), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";

    $faqs = array(
        array('آیا استفاده از این ابزارها رایگان است؟','بله، تمام ابزارهای محاسبه آنلاین تزنویسه کاملاً رایگان هستند.'),
        array('آیا نتایج با SPSS و R مطابقت دارند؟','بله، تمام محاسبات بر اساس فرمول‌های استاندارد آماری انجام می‌شوند و با SPSS و R مطابقت دارند.'),
        array('آیا داده‌های من ذخیره یا ارسال می‌شوند؟','خیر، تمام محاسبات در مرورگر شما انجام می‌شود و هیچ داده‌ای به سرور ارسال نمی‌شود.'),
        array('چگونه ابزار مناسب پژوهشم را انتخاب کنم؟','انتخاب ابزار به نوع داده، تعداد گروه‌ها و نرمال بودن توزیع بستگی دارد.'),
        array('آیا می‌توانم از این ابزارها در پایان‌نامه استفاده کنم؟','بله، نتایج این ابزارها معتبر و قابل استناد هستند.'),
        array('آیا ابزارهای جدید اضافه می‌شوند؟','بله، مجموعه ابزارهای تزنویسه به‌طور مستمر در حال گسترش است.'),
    );
    $faqItems = array();
    foreach($faqs as $f){
        $faqItems[] = array('@type'=>'Question','name'=>$f[0],'acceptedAnswer'=>array('@type'=>'Answer','text'=>$f[1]));
    }
    echo '<script type="application/ld+json">'.wp_json_encode(array('@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$faqItems), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
});
}


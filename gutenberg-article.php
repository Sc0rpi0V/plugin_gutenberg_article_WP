<?php
/**
 * Plugin Name:      Gutenberg Article
 * Description:     Block Gutenberg permettant l'affichage d'articles de différentes manières
 * Version:         0.1.0
 * Author:          
 * License:         GPL-2.0-or-later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     gutenberg-article
 *
 * @package         create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */


class GutenbergArticle {

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		//register activation function
		register_block_type_from_metadata(__DIR__,['render_callback' => [__CLASS__,'block_dynamic_render']]);
		add_filter('block_categories_all', [__CLASS__, 'gutenberg_category'], 10, 2);
	}

	/**
	 * init
	 *
	 * @return void
	 */
	public static function init() {
		new self;
	}

	/**
	 * Add category Inseec if not exist
	 * @param $categories
	 * @return array
	 */
	public static function gutenberg_category($categories){
		if(!in_array(['slug' => 'gutenberg', 'title' => __('Gutenberg Blocks', 'mario-blocks')],$categories)) {
			$news = array(
				'slug' => 'gutenberg',
				'title' => __('Gutenberg Blocks', 'mario-blocks'),
			);
			if (!in_array($news, $categories))
				return array_merge(
					array(
						$news,
					),
					$categories
				);
		}
		return $categories;
	}

	/**
	 * CALLBACK
	 *
	 * Render callback for the dynamic block.
	 *
	 * Instead of rendering from the block's save(), this callback will render the front-end
	 *
	 * @since    1.0.0
	 * @param $att Attributes from the JS block
	 * @return string Rendered HTML
	 */
	public static function block_dynamic_render( $att ) {
		$title = "";
		extract($att);
		$html = '<div class="wp-block-create-block-gutenberg-article">';
		$html .= '<div class="style-'.$style.'">';
		$html .= '<div class="wrapper-content">';
		$html.= '<div class="list-article-heading">';
		/*if(isset($title) && $title ){*/
			if ($style == '3')
				$html .= '<div class="list-article-title title title-category"><h3><span>'.$category.'</span>'.$title.'</h3></div>';
			else
				$html .= '<div class="list-article-title title title-category"><h2><span>'.$category.'</span>'.$title.'</h2></div>';
		/*} */
		/*  No need for subtitle now
		if(isset($subtitle) && $subtitle){
			$html .= '<div class="list-article-subtitle subtitle"><p>'.$subtitle.'</p></div>';
		}*/
		$html.= '</div>';
		
		if ($style == '4')
			$html.='<div class="list-article init-slider">';
		else
			$html.='<div class="list-article">';
		if (isset($btnRandom) && $btnRandom)
			shuffle($listArticle);
		foreach ($listArticle as $key => $article){
			$post_id = has_filter('wpml_object_id') ? apply_filters( 'wpml_object_id', $article['id'], get_post_type($article['id']) ) : $article['id'];
			if ($post_id && $post = get_post($post_id)) {
				$cattestimonies = get_card_class($post->ID);
				switch ($style) {
					case '1':  /* Style Temoignage */
						$html .= '
							<div class="list-article-item">
								<div class="card testimony '. implode(' ' , $cattestimonies ). ' ">
									<a class="article-link" href="'.get_permalink($post).'">
										<img width="530" height="340" class="article-image" src="'.get_the_post_thumbnail_url($post, "custom_medium").'" />
									</a>
									<div class="article-info">
										<p class="article-title is-limited">'.get_the_title($post).'</p>
										<p class="article-summary is-limited">'.get_the_excerpt($post).'</p>
										<a href="'.get_permalink($post).'" class="link article-btn">'.__('Voir ce témoignage','gutenberg').'</a>
									</div>
								</div>
							</div>';
					break;
					case '2':  /* Style simple */
						if (get_post_type($post) == "post") {
							$categories_obj = get_the_category($post->ID);
						} else {
							$categories_obj = get_the_terms($post->ID, get_post_type($post) .'-categories');
						}
						$categories = [];

						foreach( $categories_obj as $cat ) {
							$categories[] = $cat->name;
						}

						$html .= '
							<div class="list-article-item">
								<a class="article-link" href="'.get_permalink($post).'">
									<figure><img width="100%" height="100%" class="article-image" src="'.get_the_post_thumbnail_url($post, "custom_medium").'" /></figure>

									<div class="article-info">
										<p class="article-category">'.implode(", ", $categories).'</p>
										<p class="article-title is-limited">'.get_the_title($post).'</p>
									</div>
								</a>
							</div>';
					break;
					case '3':  /* Style complémentaire */
						if (get_post_type($post) == "post") {
							$categories_obj = get_the_category($post->ID);
						} else {
							$categories_obj = get_the_terms($post->ID, get_post_type($post) .'-categories');
						}
						$categories = [];

						foreach( $categories_obj as $cat ) {
							$categories[] = $cat->name;
						}

						$html .= '
							<div class="list-article-item">
								<a class="article-link" href="'.get_permalink($post).'">
									<figure>
										<img width="100%" height="100%" class="article-image" src="'.get_the_post_thumbnail_url($post, "custom_medium").'" />
									</figure>

									<div class="article-info">
										<p class="article-category">'.implode(", ", $categories).'</p>
										<p class="article-title is-limited">'.get_the_title($post).'</p>
									</div>
								</a>
							</div>';
					break;
					case '4':  /* Style Carousel */
						$html .= '
						<div class="list-article-item">
							<div class="card testimony '. implode(' ' , $cattestimonies ). ' ">
								<a class="article-link" href="'.get_permalink($post).'">
									<img width="530" height="340" class="article-image" src="'.get_the_post_thumbnail_url($post, "custom_medium").'" />
								</a>
								<div class="article-info">
									<p class="article-title is-limited--small">'.get_the_title($post).'</p>
									<p class="article-summary is-limited--small">'.get_the_excerpt($post).'</p>
									<a href="'.get_permalink($post).'" class="link article-btn">'.__('Voir ce témoignage','gutenberg').'</a>
								</div>
							</div>
						</div>';
					break;
					case '5':  /* Style Metiers */
						$html .= '
							<div class="list-article-item">
									<figure><img width="530" height="340" class="article-image" src="'.get_the_post_thumbnail_url($post, "custom_medium").'" /></figure>
									<div class="article-info">
										<p class="article-title is-limited">'.get_the_title($post).'</p>
										<div class="sub-info"><a href="'. get_permalink($post) . '" class="open-panel-bottom button" data-id="'. $post->ID .'"></a></div>
									</div>
							</div>';
					break;
					default: /* Style 4 colonnes avec dates */
						$html .= '
							<div class="list-article-item">
								<a class="article-link" href="'.get_permalink($post).'">
									<img width="500" height="500" class="article-image" src="'.get_the_post_thumbnail_url($post, "custom_medium").'" />
								</a>
								<div class="article-info">
									<p class="article-date mentions">'.get_the_date('F Y', $post).'</p>
									<p class="article-title title title-small is-limited">'.get_the_title($post).'</p>
									<a href="'.get_permalink($post).'" class="link article-btn">'.__('Lire la suite','gutenberg').'</a>
								</div>
							</div>';
				}
			}
		}
		if ($style == 5) {
			$html .= '
				<div class="list-article-item last">
					<div class="article-info wrapper-button">';
						if ( isset($btnAll) && $btnAll == "1" )
						$html .= '<a class="btn btn-all-articles" href="'. get_post_type_archive_link('post') .'"><span><span>' . $btnTxtAllArticles .'</span></span></a>';
						if ( isset($btnAll) && $btnAll == "3" )
						$html .= '<a class="btn btn-all-articles" href="'. get_post_type_archive_link('metiers') .'"><span><span>' . $btnTxtAllMetiers .'</span></span></a>';
						if ( isset($btnAll) && $btnAll == "0" )
						$html .= '<a class="btn btn-all-articles" href="'. $btnUrlAllCustom .'"><span><span>' . $btnTxtAllCustom .'</span></span></a>';
					$html .= '</div>
				</div>';
		}
		$html .= '</div>';
		if ($style !=5) {
			if ( isset($btnAll) && $btnAll == "1" )
				$html .= '<a class="btn btn-all-articles" href="'. get_post_type_archive_link('post') .'"><span><span>' . $btnTxtAllArticles .'</span></span></a>';
			if ( isset($btnAll) && $btnAll == "3" )
				$html .= '<a class="btn btn-all-articles" href="'. get_post_type_archive_link('metiers') .'"><span><span>' . $btnTxtAllMetiers .'</span></span></a>';
			if ( isset($btnAll) && $btnAll == "0" )
				$html .= '<a class="btn btn-all-articles" href="'. $btnUrlAllCustom .'"><span><span>' . $btnTxtAllCustom .'</span></span></a>';
		}
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		return $html;

	}
}

add_action( 'init', array ( 'GutenbergArticle', 'init' ) );

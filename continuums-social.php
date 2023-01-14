<?php
/*
 * Plugin Name: Continuum(s) Social
 * Description: Enrichissement pour le partage social et le référencement
 * Version: 2014.04.24
 * Text Domain: continuums-social
 * @author Luc Poupard
 * @link http://www.kloh.ch
*/

/* ----------------------------- */
/* Sommaire */
/* ----------------------------- */
/*
  == Traduction
  == Dublin Core
    -- Balise profile sur <html>
    -- Balises méta
  == Open Graph
    -- Balise prefix sur <html>
    -- Balises méta
  == Twitter Cards
    -- Balises méta
  == Liens de partage
*/


/* == @section Traduction ==================== */
/**
 * @author Luc Poupard
 * @note I18n : déclare le domaine et l’emplacement des fichiers de traduction
 * @link https://codex.wordpress.org/I18n_for_WordPress_Developers
*/
function continnums_social_init() {
  $plugin_dir = basename( dirname( __FILE__ ) );
  load_plugin_textdomain( 'continuums-social', false, $plugin_dir );
}

add_action( 'plugins_loaded', 'continnums_social_init' );


/* == @section Dublin Core ==================== */
/**
 * @see http://openweb.eu.org/articles/dublin_core
*/

/* -- @subsection Balise profile sur <html> -------------------- */

/* On ajoute l'attribut profile sur la balise <html> 
 * @see http://stackoverflow.com/questions/13364983/wordpress-html-tag-hook-filter
 */
function continuums_dc_html( $profile ) {
  $profile .= ' profile="http://dublincore.org/documents/2008/08/04/dc-html/"';
  return $profile;
}

add_filter( 'language_attributes', 'continuums_dc_html' );

/* -- @subsection Balises méta -------------------- */

function continuums_dc() {
  /* -- @subsection schema.dc -------------------- */
  $continuums_dublin = '<link rel="schema.dc" href="http://purl.org/dc/elements/1.1/">';

  /* -- @subsection dc.title -------------------- */
  if ( function_exists( 'ffeeeedd__injection__titre' ) ) {
    $continuums_dublin .= '<meta name="dc.title" content="' . esc_attr( wp_title( '-', false, 'right' ) ) . '" />';
  } else {
    $continuums_dublin .= '<meta name="dc.title" content="' . esc_attr( get_the_tile() ) . '" />';
  }

  /* -- @subsection dc.description -------------------- */
  /**
   * @note Inspiré par le thème ffeeeedd, lui même inspiré du thème Noviseo2012
   * @author Noviseo2012 : Sylvain Fouillaud
   * @link https://twitter.com/noviseo
   * @see http://noviseo.fr/2012/11/theme-wordpress-referencement/
   * @author ffeeeedd : Gaël Poupard
   * @link https://twitter.com/ffoodd_fr
   * @see http://www.ffeeeedd.fr/
   */
  // On teste d’abord si la fonction est surchargée ou si un plugin dédié existe
  if (
    ! function_exists( 'ffeeeedd__metabox' ) &&
    ! class_exists( 'WPSEO_Frontend' ) &&
    ! class_exists( 'All_in_One_SEO_Pack' )
  ) {
    if ( ! function_exists( 'continuums_dc_description' ) ) {
      global $wp_query;
      // Si le champ est rempli, on affiche sa valeur
      if ( isset( $wp_query->post->ID ) && get_post_meta( $wp_query->post->ID, '_ffeeeedd__metabox__description', true ) ) {
        $continuums_dublin .= '<meta name="dc.description" content="' . esc_attr( get_post_meta( $wp_query->post->ID, '_ffeeeedd__metabox__description', true ) ) . '" />';
      }
      // Sinon, dans le cas d’un article on affiche l’extrait
      elseif ( is_single() && has_excerpt() ) {
        $continuums_dublin .= '<meta name="dc.description" content="' . strip_tags( get_the_excerpt() ) . '" />';
      }
      // Sinon, on affiche la description générale du site
      else {
        $continuums_dublin .= '<meta name="dc.description" content="' . esc_attr( get_bloginfo( 'description' ) ) . '" />';
      }
    }
  }

  /* -- @subsection dc.language -------------------- */
  if ( is_single() ) {
    $continuums_dublin .= '<meta name="dc.date" scheme="DCTERMS.W3CDTF" content="' . get_the_time( 'Y-m-d' ) . '">';
  }

  /* -- @subsection dc.language -------------------- */
  if ( get_locale() ) {
    $continuums_dublin .= '<meta name="dc.language" content="' . get_locale() . '" />';
  }

  // On renvoie les métas
  echo $continuums_dublin;
}


/* == @section Open Graph ==================== */
/**
 * @note Les balises Open Graph permettent d'enrichir les contenus partagés sur les réseaux sociaux comme Facebook, Google+ ou encore LinkedIn
 * @see Best practices d'intégration Open Graph pour Facebook
 * @link https://developers.facebook.com/docs/opengraph/howtos/maximizing-distribution-media-content
*/

/* -- @subsection Balise prefix sur <html> -------------------- */

/* On ajoute l'attribut prefix sur la balise <html> 
 * @see http://stackoverflow.com/questions/13364983/wordpress-html-tag-hook-filter
 */
function continuums_opengraph_html( $prefix ) {
  $prefix .= ' prefix="og: http://ogp.me/ns#"';
  return $prefix;
}

add_filter( 'language_attributes', 'continuums_opengraph_html' );

/* -- @subsection Balises méta -------------------- */
function continuums_opengraph() {
  /* -- @subsection og:type -------------------- */
  if ( is_single() ) {
    $continuums_opengraph = '<meta property="og:type" content="article" />';
  } else {
    $continuums_opengraph = '<meta property="og:type" content="website" />';
  }

  /* -- @subsection og:title -------------------- */
  if ( function_exists( 'ffeeeedd__injection__titre' ) ) {
    $continuums_opengraph .= '<meta property="og:title" content="' . esc_attr( wp_title( '-', false, 'right' ) ) . '" />';
  } else {
    $continuums_opengraph .= '<meta property="og:title" content="' . esc_attr( get_the_tile() ) . '" />';
  }

  /* -- @subsection og:site_name -------------------- */
  $continuums_opengraph .= '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />';

  /* -- @subsection og:url -------------------- */
  $continuums_opengraph .= '<meta property="og:url" content="' . esc_url( get_permalink() ) . '" />';

  /* -- @subsection og:description -------------------- */
  /**
   * @note Inspiré par le thème ffeeeedd, lui même inspiré du thème Noviseo2012
   * @author Noviseo2012 : Sylvain Fouillaud
   * @link https://twitter.com/noviseo
   * @see http://noviseo.fr/2012/11/theme-wordpress-referencement/
   * @author ffeeeedd : Gaël Poupard
   * @link https://twitter.com/ffoodd_fr
   * @see http://www.ffeeeedd.fr/
   */
  // On teste d’abord si la fonction est surchargée ou si un plugin dédié existe
  if (
    ! function_exists( 'ffeeeedd__metabox' ) &&
    ! class_exists( 'WPSEO_Frontend' ) &&
    ! class_exists( 'All_in_One_SEO_Pack' )
  ) {
    if ( ! function_exists( 'continuums_opengraph_description' ) ) {
      global $wp_query;
      // Si le champ est rempli, on affiche sa valeur
      if ( isset( $wp_query->post->ID ) && get_post_meta( $wp_query->post->ID, '_ffeeeedd__metabox__description', true ) ) {
        $continuums_opengraph .= '<meta property="og:description" content="' . esc_attr( get_post_meta( $wp_query->post->ID, '_ffeeeedd__metabox__description', true ) ) . '" />';
      }
      // Sinon, dans le cas d’un article on affiche l’extrait
      elseif ( is_single() && has_excerpt() ) {
        $continuums_opengraph .= '<meta property="og:description" content="' . strip_tags( get_the_excerpt() ) . '" />';
      }
      // Sinon, on affiche la description générale du site
      else {
        $continuums_opengraph .= '<meta property="og:description" content="' . esc_attr( get_bloginfo( 'description' ) ) . '" />';
      }
    }
  }

  /* -- @subsection og:image -------------------- */
  /**
   * @note Inspiré par le thème ffeeeedd, lui même inspiré du thème Noviseo2012
   * @author Noviseo2012 : Sylvain Fouillaud
   * @link https://twitter.com/noviseo
   * @see http://noviseo.fr/2012/11/theme-wordpress-referencement/
   * @author ffeeeedd : Gaël Poupard
   * @link https://twitter.com/ffoodd_fr
   * @see http://www.ffeeeedd.fr/
   */
  if ( has_post_thumbnail() ) {
    $og_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
    $continuums_opengraph .= '<meta property="og:image" content="' . esc_url( $og_image[0] ) . '"/>';
  } else {
    $og_image_default = wp_get_attachment_image_src( 282, 'full' );
    $continuums_opengraph .= '<meta property="og:image" content="' . esc_url( $og_image_default[0] ) . '"/>';
  }

  /* -- @subsection og:locale -------------------- */
  if ( get_locale() ) {
    $continuums_opengraph .= '<meta property="og:locale" content="' . get_locale() . '" />';
  }

  // On renvoie les métas
  echo $continuums_opengraph;
}


/* == @section Twitter Cards ==================== */
/**
 * @note Les balises Twitter Cards permettent d'enrichir les contenus partagés sur Twitter
 * @see Documentation Twitter Cards
 * @link https://dev.twitter.com/docs/cards
*/

/* -- @subsection Balises méta -------------------- */
function continuums_twittercards() {
  /* -- @subsection twitter:card -------------------- */
  $continuums_twittercards = '<meta name="twitter:card" content="summary_large_image">';

  /* -- @subsection twitter:site -------------------- */
  $continuums_twittercards .= '<meta name="twitter:site" content="@MA3YT_Asso" />';

  /* -- @subsection twitter:creator -------------------- */
  if ( is_single() && get_the_author_meta( 'twitter', 1 ) ) {
    $continuums_twittercards .= '<meta name="twitter:creator" content="' . esc_attr( get_the_author_meta( 'twitter', 1 ) ) . '" />';
  }

  /* -- @subsection twitter:title -------------------- */
  if ( function_exists( 'ffeeeedd__injection__titre' ) ) {
    $continuums_twittercards .= '<meta name="twitter:title" content="' . esc_attr( wp_title( '-', false, 'right' ) ) . '" />';
  } else {
    $continuums_opengraph .= '<meta name="twitter:title" content="' . esc_attr( get_the_tile() ) . '" />';
  }

  /* -- @subsection twitter:url -------------------- */
  $continuums_twittercards .= '<meta name="twitter:url" content="' . esc_url( get_permalink() ) . '" />';

  /* -- @subsection twitter:description -------------------- */
  /**
   * @note Inspiré par le thème ffeeeedd, lui même inspiré du thème Noviseo2012
   * @author Noviseo2012 : Sylvain Fouillaud
   * @link https://twitter.com/noviseo
   * @see http://noviseo.fr/2012/11/theme-wordpress-referencement/
   * @author ffeeeedd : Gaël Poupard
   * @link https://twitter.com/ffoodd_fr
   * @see http://www.ffeeeedd.fr/
   */
  // On teste d’abord si la fonction est surchargée ou si un plugin dédié existe
  if (
    ! function_exists( 'ffeeeedd__metabox' ) &&
    ! class_exists( 'WPSEO_Frontend' ) &&
    ! class_exists( 'All_in_One_SEO_Pack' )
  ) {
    if ( ! function_exists( 'continuums_twittercards_description' ) ) {
      global $wp_query;
      // Si le champ est rempli, on affiche sa valeur
      if ( isset( $wp_query->post->ID ) && get_post_meta( $wp_query->post->ID, '_ffeeeedd__metabox__description', true ) ) {
        $continuums_twittercards .= '<meta name="twitter:description" content="' . esc_attr( get_post_meta( $wp_query->post->ID, '_ffeeeedd__metabox__description', true ) ) . '" />';
      }
      // Sinon, dans le cas d’un article on affiche l’extrait
      elseif ( is_single() && has_excerpt() ) {
        $continuums_twittercards .= '<meta name="twitter:description" content="' . strip_tags( get_the_excerpt() ) . '" />';
      }
      // Sinon, on affiche la description générale du site
      else {
        $continuums_twittercards .= '<meta name="twitter:description" content="' . esc_attr( get_bloginfo( 'description' ) ) . '" />';
      }
    }
  }

  /* -- @subsection twitter:image:src -------------------- */
  /**
   * @note Inspiré par le thème ffeeeedd, lui même inspiré du thème Noviseo2012
   * @author Noviseo2012 : Sylvain Fouillaud
   * @link https://twitter.com/noviseo
   * @see http://noviseo.fr/2012/11/theme-wordpress-referencement/
   * @author ffeeeedd : Gaël Poupard
   * @link https://twitter.com/ffoodd_fr
   * @see http://www.ffeeeedd.fr/
   */
  if ( has_post_thumbnail() ) {
    $twitter_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
    $continuums_twittercards .= '<meta name="twitter:image:src" content="' . esc_url( $twitter_image[0] ) . '"/>';
  } else {
    $twitter_image_default = wp_get_attachment_image_src( 282, 'full' );
    $continuums_twittercards .= '<meta name="twitter:image:src" content="' . esc_url( $twitter_image_default[0] ) . '"/>';
  }

  // On renvoie les métas
  echo $continuums_twittercards;
}


/* == @section Liens de partage ==================== */
/**
 * 
*/
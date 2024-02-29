<?php
/*
Plugin Name: Order notes
Description: Add order notes column to orders list
Version: 1.0.0
Author: Vladimir
Author URI: slapiosnosys.lt
*/

// WooCommerce - Add order notes column to orders list
  // 'Actions' to remain as last column
  // show only the last private note
 
  // Add column "Order Notes" on the orders page
  add_filter( 'manage_edit-shop_order_columns', 'add_order_notes_column' );
  function add_order_notes_column( $columns ) {
    $new_columns = ( is_array( $columns ) ) ? $columns : array();
    unset( $new_columns['wc_actions'] );
    $new_columns['order_notes'] = 'Order Notes';
    $new_columns['wc_actions'] = 'Actions';
    return $new_columns;
  }
 
  add_action( 'admin_print_styles', 'add_order_notes_column_style' );
  function add_order_notes_column_style() {
    $css = '.post-type-shop_order table.widefat.fixed { table-layout: auto; width: 100%; }';
    $css .= 'table.wp-list-table .column-order_notes { min-width: 280px; text-align: left; }';
    $css .= '.column-order_notes ul { margin: 0 0 0 18px; list-style-type: disc; }';
    $css .= '.order_customer_note { color: #ee0000; }'; // red
    $css .= '.order_private_note { color: #0000ee; }'; // blue
    wp_add_inline_style( 'woocommerce_admin_styles', $css );
  }
 
  // Add order notes to the "Order Notes" column
  add_action( 'manage_shop_order_posts_custom_column', 'add_order_notes_content' );
  function add_order_notes_content( $column ) {
    if( $column != 'order_notes' ) return;      
    global $post, $the_order;
    if( empty( $the_order ) || $the_order->get_id() != $post->ID ) {
      $the_order = wc_get_order( $post->ID );
    }    
    $args = array();
    $args['order_id'] = $the_order->get_id();
    $args['order_by'] = 'date_created';
    $args['order'] = 'DESC';
    $notes = wc_get_order_notes( $args );
    if( $notes ) {
      print '<ul>';
      foreach( $notes as $note ) {
        if( ! $note->customer_note ) {
          print '<li class="order_private_note">';
          $date = date( 'd/m/y H:i', strtotime( $note->date_created ) );
          print $date.' by '.$note->added_by.'<br>'.$note->content.'</li>';
          break;
        }
      }
      print '</ul>';
    }
  } 
// end function/**
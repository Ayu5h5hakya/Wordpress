<?php

/**
 * Adds a meta box to the post editing screen
 */
 $GLOBALS['err_occur'] = 'false';
 function dont_publish( $post_ID )
{
     if(get_post_type($post_ID) == 'job'){
         if(empty($_POST[ 'description' ] ))
         {
           exit;
         }
     }
}
//the dont_publish function will be called after the publish button is clicked
add_action( 'publish_post', 'dont_publish' );
function hrm_custom_meta() {
    add_meta_box(
      'hrm_meta',
      __( 'Task Details', 'hrm-jobs' ),
      'hrm_meta_callback',
      'job'
    );
}
add_action( 'add_meta_boxes', 'hrm_custom_meta' );

add_filter( 'post_updated_messages', 'post_published' );

function post_published( $messages )
{
    if( $GLOBALS['err_occur']=='false') unset($messages[post][6]);
    return $messages;
}
/**
 * Outputs the content of the meta box
 */
function hrm_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'hrm_nonce' );
    $hrm_stored_meta = get_post_meta( $post->ID );
    ?>
    <div>
      <div class="meta-row">
          <div class="meta-th">
            <label for="application_deadline" class="wpdt-row-title"><?php _e( 'Complete within', 'hrm-textdomain' )?></label>
          </div>
          <div class="meta-td">
            <input type="text" size="10" class="wpdt-row-content datepicker" name="deadline" id="deadline" value="<?php if ( isset ( $hrm_stored_meta['deadline'] ) ) echo esc_attr( $hrm_stored_meta['deadline'][0] ); ?>" />
          </div>
      </div>
      <div class="meta-row">
          <div class="meta-th">
            <span>Task</span>
          </div>
          <div  class="meta-editor">
            <?php

              $content = get_post_meta( $post->ID, 'description', true );
              $editor_id = 'description';
              $settings = array(
                'textarea_rows' => 5,
              );

              wp_editor( $content, $editor_id, $settings );

            ?>
          </div>
        </div>
    </div>

    <?php
}

function my_admin_notice(){
    //print the message
    global $post;
    $notice = get_option('otp_notice');
    if (empty($notice)) return '';
    foreach($notice as $pid => $m){
        if ($post->ID == $pid ){
            echo '<div id="message" class="error"><p>'.$m.'</p></div>';
            //make sure to remove notice after its displayed so its only displayed when needed.
            unset($notice[$pid]);
            update_option('otp_notice',$notice);
            break;
        }
    }
}
/**
 * Saves the custom meta input
 */
function hrm_meta_save( $post_id )
{

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'hrm_nonce' ] ) && wp_verify_nonce( $_POST[ 'hrm_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce )
    {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    // if( isset( $_POST[ 'job-id' ] ) ) {
    //     update_post_meta( $post_id, 'job-id', sanitize_text_field( $_POST[ 'job-id' ] ) );
    // }

    //
    // if( isset( $_POST[ 'minimum-requirements' ] ) ) {
    //     update_post_meta( $post_id, 'minimum-requirements', sanitize_text_field( $_POST[ 'minimum-requirements' ] ) );
    // }
    //
    // if( isset( $_POST[ 'date_listed' ] ) ) {
    //     update_post_meta( $post_id, 'date_listed', sanitize_text_field( $_POST[ 'date_listed' ] ) );
    // }

    if( isset( $_POST[ 'description' ] ) ) {
        update_post_meta( $post_id, 'description', sanitize_text_field( $_POST[ 'description' ] ) );
    }
    if( isset( $_POST[ 'deadline' ] ) )
    {
        update_post_meta( $post_id, 'deadline', sanitize_text_field( $_POST[ 'deadline' ] ) );
    }
    //
    // if( isset( $_POST[ 'preferred-requirements' ] ) ) {
    //     update_post_meta( $post_id, 'preferred-requirements', sanitize_text_field( $_POST[ 'preferred-requirements' ] ) );
    // }
    //
    // if( isset( $_POST[ 'relocation-assistance' ] ) ) {
    //     update_post_meta( $post_id, 'relocation-assistance', sanitize_text_field( $_POST[ 'relocation-assistance' ] ) );
    // }
}
add_action( 'save_post', 'hrm_meta_save' );
/**
 * Change Placeholder text in Default title field.
 */
function hrm_change_default_title( $placeholder_title ){

    $screen = get_current_screen();

    if ( $screen->post_type == 'job' ){
        return $placeholder_title = "Enter Task Title Here";
    }
}

add_filter( 'enter_title_here', 'hrm_change_default_title' );

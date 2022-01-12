<?php
/**
 * Plugin Name:         Rimarx Top Commentators Widget
 * Description:         Add WordPress user top comments widget
 * Author:              Rimarx
 * Version:             1.0
 * Requires at least:   5
 * Requires PHP:        5.4
 */

function register_widget_rmx() {
    register_widget( 'mynew_widget' );
}
add_action( 'widgets_init', 'register_widget_rmx' );

class mynew_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
        // widget ID
        'mynew_widget',
        // widget name
        __('Top Comments Widget', 'mynew_widget'),
        // widget description
        array( 'description' => __( 'Display Widget', 'mynew_widget' ), )
        );
    }

    protected function get_users_list( $user_role ){

        if ( $user_role == 'All' ) {
            $usersObj = get_users();
        }else{
            $usersObj = get_users( array( 'role' => $user_role, ) );
        }
        
        $usersArr = array();

        foreach ( $usersObj as $user ) {

            $usersArr[$user->ID]['role'] = $user->roles;

            $usersArr[$user->ID]['name'] = $user->display_name;

            $commentsCount = get_comments( array(
                'user_id' => $user->ID,
                'count'   => true,
            ) );

            $usersArr[$user->ID]['coments_count'] = $commentsCount;
        }

        usort( $usersArr, function ( $a, $b ) {
            return $a['coments_count'] === $b['coments_count'] ? 0 : ( $a['coments_count'] > $b['coments_count'] ? -1 : 1 );
        });

        return $usersArr;
    }

    public function widget( $args, $instance ) {

        if( $instance['color_theme'] == 'Dark' ){
            $args['after_widget'] = '<style>.widget_mynew_widget {
                background-color: #606060; color: white; padding: 10px;
            }</style>';
        }

//var_dump($args);

        echo $args['before_widget'];
        echo $args['before_title'] .  __( 'Top Commentators', 'mynew_widget' ) . $args['after_title'];

        $usersArr = $this->get_users_list( $instance['user_roles'] );

        $usersArrSize = count($usersArr);
        $max_count = $instance['users_amount'];

        if ( $usersArrSize < $instance['users_amount'] ) {
            $max_count = $usersArrSize;
        }

        for ( $i = 0; $i < $max_count; $i++ ) { 

            if ( $instance['empty_comment_show'] == 'No' && $usersArr[$i]['coments_count'] == 0 )
                break;

            $comments_amount = ( $instance['show_amount'] == 'Yes' ) ? '(' . $usersArr[$i]['coments_count'] . ')' : '' ;
            echo  '<p>' . $usersArr[$i]['name'] . $comments_amount . '</p>';
        }

        echo $args['after_widget'];
    }

    public function update( $new_instance, $old_instance ) {
        $instance                       = $old_instance;
        $instance['show_amount']        = $new_instance['show_amount'];
        $instance['users_amount']       = $new_instance['users_amount'];
        $instance['empty_comment_show'] = $new_instance['empty_comment_show'];
        $instance['color_theme']        = $new_instance['color_theme'];
        $instance['user_roles']         = $new_instance['user_roles'];
        return $instance;
    }

    public function form( $instance ) {

        require_once ABSPATH . 'wp-admin/includes/user.php';
        $user_roles_arr     = get_editable_roles();

        $show_amount        = isset( $instance['show_amount'] ) ? $instance['show_amount'] : 'No';
        $empty_comment_show = isset( $instance['empty_comment_show'] ) ? $instance['empty_comment_show'] : 'No';
        $users_amount       = isset( $instance['users_amount'] ) ? $instance['users_amount'] : 5;
        $color_theme        = isset( $instance['color_theme'] ) ? $instance['color_theme'] : 'Light';
        $user_roles         = isset( $instance['user_roles'] ) ? $instance['user_roles'] : 'All';
        ?>
     <!-- show_amount field START -->
     <p>
      <label for="<?php echo $this->get_field_id('show_amount'); ?>">Show Amount:

        <select class='widefat'
            id="<?php echo $this->get_field_id('show_amount'); ?>"
            name="<?php echo $this->get_field_name('show_amount'); ?>">
          <option value="Yes" <?php echo ($show_amount == 'Yes') ? 'selected' : ''; ?>>
            Yes
          </option>
          <option value="No" <?php echo ($show_amount == 'No') ? 'selected' : ''; ?>>
            No
          </option>
        </select>
      </label>
     </p>
     <!-- show_amount field END -->
     <!-- users_amount field START -->
    <p>
        <label for="<?php echo $this->get_field_id( 'users_amount' ); ?>">Users Amount To Show</label>

        <input class="checkbox" type="number"
        id="<?php echo $this->get_field_id( 'users_amount' ); ?>"
        name="<?php echo $this->get_field_name( 'users_amount' ); ?>" 
        value="<?php echo $users_amount; ?>"
        min="1"
        >
    </p>
     <!-- users_amount field END -->
     <!-- empty_comment_show field START -->
     <p>
      <label for="<?php echo $this->get_field_id('empty_comment_show'); ?>">Show User With No Comments:

        <select class='widefat'
            id="<?php echo $this->get_field_id('empty_comment_show'); ?>"
            name="<?php echo $this->get_field_name('empty_comment_show'); ?>">
          <option value="Yes" <?php echo ($empty_comment_show == 'Yes') ? 'selected' : ''; ?>>
            Yes
          </option>
          <option value="No" <?php echo ($empty_comment_show == 'No') ? 'selected' : ''; ?>>
            No
          </option>
        </select>
      </label>
     </p>
     <!-- empty_comment_show field END -->
     <!-- color_theme field START -->
     <p>
      <label for="<?php echo $this->get_field_id('color_theme'); ?>">Widget Color Theme:

        <select class='widefat'
            id="<?php echo $this->get_field_id('color_theme'); ?>"
            name="<?php echo $this->get_field_name('color_theme'); ?>">
          <option value="Dark" <?php echo ($color_theme == 'Dark') ? 'selected' : ''; ?>>
            Dark
          </option>
          <option value="Light" <?php echo ($color_theme == 'Light') ? 'selected' : ''; ?>>
            Light
          </option>
        </select>
      </label>
     </p>
     <!-- color_theme field END -->
     <!-- user_roles field START -->
     <p>
      <label for="<?php echo $this->get_field_id('user_roles'); ?>">Show Users With Roles:

        <select class='widefat'
            id="<?php echo $this->get_field_id('user_roles'); ?>"
            name="<?php echo $this->get_field_name('user_roles'); ?>">
            <option value="All" <?php echo ( $user_roles == 'All' ) ? 'selected' : ''; ?>>
            All
            </option>
            <?php foreach ( $user_roles_arr as $role ) { ?>
                <option value="<?= $role['name'] ?>" <?php echo ( $user_roles == $role['name'] ) ? 'selected' : ''; ?>>
                <?= $role['name']; ?>
                </option>
            <?php } ?>
        </select>
      </label>
     </p>
     <!-- user_roles field END -->




        <?php
    }



}
<?php

/**
 * Adds Foo_Widget widget.
 */
class LogoList_Widget extends WP_Widget {

        var $plugin_pref='EventAggregator_';

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'foo_widget', // Base ID
			'EventAggregator LogoList', // Name
			array( 'description' => __( 'A Foo Widget', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
                
		extract( $args );
                
                $logolist= get_option($this->plugin_pref.'LogoList');
                
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
                
                if (count($logolist) >0) {
                    echo '<table>';
                    foreach($logolist as $logo) {
                        echo '<tr><td><a href="'.$logo['link'].'"><img src="'.$logo['logo'].'" alt="'.$logo['name'].'"/><p>'.$logo['name'].'</p></a></td></tr>';
                    
                    }
                    echo '</table>';
                }
                
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

} // class Foo_Widget

?>
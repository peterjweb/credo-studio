<?php

if ( fusion_is_element_enabled( 'fusion_alert' ) ) {

	if ( ! class_exists( 'FusionSC_Alert' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Alert extends Fusion_Element {

			/**
			 * The alert class.
			 *
			 * @access private
			 * @since 1.0
			 * @var string
			 */
			private $alert_class;

			/**
			 * The icon class.
			 *
			 * @access private
			 * @since 1.0
			 * @var string
			 */
			private $icon_class;

			/**
			 * An array of the shortcode arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $args;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_alert-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_alert-shortcode-icon', array( $this, 'icon_attr' ) );
				add_filter( 'fusion_attr_alert-shortcode-button', array( $this, 'button_attr' ) );

				add_shortcode( 'fusion_alert', array( $this, 'render' ) );

			}

			/**
			 * Render the shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render( $args, $content = '' ) {

				global $fusion_settings;
				if ( ! $fusion_settings ) {
					$fusion_settings = Fusion_Settings::get_instance();
				}

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile'      => fusion_builder_default_visibility( 'string' ),
						'class'               => '',
						'id'                  => '',
						'accent_color'        => '',
						'background_color'    => '',
						'border_size'         => '',
						'box_shadow'          => 'no',
						'icon'                => '',
						'type'                => 'general',
						'animation_type'      => '',
						'animation_direction' => 'left',
						'animation_speed'     => '',
						'animation_offset'    => $fusion_settings->get( 'animation_offset' ),
					), $args
				);
				$defaults['border_size'] = FusionBuilder::validate_shortcode_attr_value( $defaults['border_size'], 'px' );

				extract( $defaults );

				$this->args = $defaults;

				switch ( $this->args['type'] ) {

					case 'general':
						$this->alert_class = 'info';
						if ( ! $icon || 'none' !== $icon ) {
							$this->args['icon'] = $icon = 'fa-info-circle';
						}
						break;
					case 'error':
						$this->alert_class = 'danger';
						if ( ! $icon || 'none' !== $icon ) {
							$this->args['icon'] = $icon = 'fa-exclamation-triangle';
						}
						break;
					case 'success':
						$this->alert_class = 'success';
						if ( ! $icon || 'none' !== $icon ) {
							$this->args['icon'] = $icon = 'fa-check-circle';
						}
						break;
					case 'notice':
						$this->alert_class = 'warning';
						if ( ! $icon || 'none' !== $icon ) {
							$this->args['icon'] = $icon = 'fa-lg fa-cog';
						}
						break;
					case 'blank':
						$this->alert_class = 'blank';
						break;
					case 'custom':
						$this->alert_class = 'custom';
						break;
				}

				$html = '<div ' . FusionBuilder::attributes( 'alert-shortcode' ) . '>';
				$html .= '  <button ' . FusionBuilder::attributes( 'alert-shortcode-button' ) . '>&times;</button>';
				if ( $icon && 'none' !== $icon ) {
					$html .= '<span ' . FusionBuilder::attributes( 'alert-icon' ) . '>';
					$html .= '<i ' . FusionBuilder::attributes( 'alert-shortcode-icon' ) . '></i>';
					$html .= '</span>';
				}
				// Make sure the title text is not wrapped with an unattributed p tag.
				$content = preg_replace( '!^<p>(.*?)</p>$!i', '$1', trim( $content ) );

				$html .= do_shortcode( $content );
				$html .= '</div>';

				return $html;

			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function attr() {

				$attr = array();

				$attr['class'] = 'fusion-alert alert ' . $this->args['type'] . ' alert-dismissable alert-' . $this->alert_class;

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], $attr );

				if ( 'yes' === $this->args['box_shadow'] ) {
					$attr['class'] .= ' alert-shadow';
				}

				if ( 'custom' === $this->alert_class ) {
					$attr['style']  = 'background-color:' . $this->args['background_color'] . ';';
					$attr['style'] .= 'color:' . $this->args['accent_color'] . ';';
					$attr['style'] .= 'border-color:' . $this->args['accent_color'] . ';';
					$attr['style'] .= 'border-width:' . $this->args['border_size'] . ';';
				}

				if ( $this->args['animation_type'] ) {
					$animations = FusionBuilder::animations( array(
						'type'      => $this->args['animation_type'],
						'direction' => $this->args['animation_direction'],
						'speed'     => $this->args['animation_speed'],
						'offset'    => $this->args['animation_offset'],
					) );

					$attr = array_merge( $attr, $animations );

					$attr['class'] .= ' ' . $attr['animation_class'];
					unset( $attr['animation_class'] );
				}

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				return $attr;

			}

			/**
			 * Builds theicon  attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function icon_attr() {
				return array(
					'class' => 'fa fa-lg ' . FusionBuilder::font_awesome_name_handler( $this->args['icon'] ),
				);
			}

			/**
			 * Builds the button attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function button_attr() {

				$attr = array();

				if ( 'custom' === $this->alert_class ) {
					$attr['style'] = 'color:' . $this->args['accent_color'] . ';border-color:' . $this->args['accent_color'] . ';';
				}

				$attr['type']         = 'button';
				$attr['class']        = 'close toggle-alert';
				$attr['data-dismiss'] = 'alert';
				$attr['aria-hidden']  = 'true';

				return $attr;

			}


			/**
			 * Sets the necessary scripts.
			 *
			 * @access public
			 * @since 1.1
			 * @return void
			 */
			public function add_scripts() {
				Fusion_Dynamic_JS::enqueue_script( 'fusion-animations' );
				Fusion_Dynamic_JS::enqueue_script( 'fusion-alert' );
			}
		}
	}

	new FusionSC_Alert();
}


/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_alert() {
	fusion_builder_map( array(
		'name'            => esc_attr__( 'Alert', 'fusion-builder' ),
		'shortcode'       => 'fusion_alert',
		'icon'            => 'fa fa-lg fa-exclamation-triangle',
		'preview'         => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-alert-preview.php',
		'preview_id'      => 'fusion-builder-block-module-alert-preview-template',
		'allow_generator' => true,
		'params'          => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Alert Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of alert message. Choose custom for advanced color options below.', 'fusion-builder' ),
				'param_name'  => 'type',
				'default'     => 'error',
				'value'       => array(
					'general' => esc_attr__( 'General', 'fusion-builder' ),
					'error'   => esc_attr__( 'Error', 'fusion-builder' ),
					'success' => esc_attr__( 'Success', 'fusion-builder' ),
					'notice'  => esc_attr__( 'Notice', 'fusion-builder' ),
					'custom'  => esc_attr__( 'Custom', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Accent Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Custom setting only. Set the border, text and icon color for custom alert boxes.', 'fusion-builder' ),
				'param_name'  => 'accent_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Custom setting only. Set the background color for custom alert boxes.', 'fusion-builder' ),
				'param_name'  => 'background_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Border Width', 'fusion-builder' ),
				'param_name'  => 'border_size',
				'value'       => '1px',
				'description' => esc_attr__( 'Custom setting only. Set the border width for custom alert boxes. In pixels.', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'custom',
						'operator' => '==',
					),
				),

			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Select Custom Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Box Shadow', 'fusion-builder' ),
				'description' => esc_attr__( 'Display a box shadow below the alert box.', 'fusion-builder' ),
				'param_name'  => 'box_shadow',
				'default'     => 'no',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Alert Content', 'fusion-builder' ),
				'description' => esc_attr__( "Insert the alert's content.", 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => esc_html__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Animation Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of animation to use on the element.', 'fusion-builder' ),
				'param_name'  => 'animation_type',
				'value'       => fusion_builder_available_animations(),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Direction of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the incoming direction for the animation.', 'fusion-builder' ),
				'param_name'  => 'animation_direction',
				'value'       => array(
					'down'   => esc_attr__( 'Top', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
					'up'     => esc_attr__( 'Bottom', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'static' => esc_attr__( 'Static', 'fusion-builder' ),
				),
				'default'     => 'left',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Speed of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-builder' ),
				'param_name'  => 'animation_speed',
				'min'         => '0.1',
				'max'         => '1',
				'step'        => '0.1',
				'value'       => '0.3',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Offset of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls when the animation should start.', 'fusion-builder' ),
				'param_name'  => 'animation_offset',
				'value'       => array(
					''                => esc_attr__( 'Default', 'fusion-builder' ),
					'top-into-view'   => esc_attr__( 'Top of element hits bottom of viewport', 'fusion-builder' ),
					'top-mid-of-view' => esc_attr__( 'Top of element hits middle of viewport', 'fusion-builder' ),
					'bottom-in-view'  => esc_attr__( 'Bottom of element enters viewport', 'fusion-builder' ),
				),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
				'param_name'  => 'class',
				'value'       => '',
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_alert' );

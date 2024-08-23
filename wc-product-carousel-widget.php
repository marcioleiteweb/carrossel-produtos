<?php
/*
Plugin Name: WooCommerce Carrossel e Estilo de Exibição de Produtos
Description: Com esse Plugin é possível gerar um Carrossel e Estilo de Exibição de Produtos.
Version: 1.4
Author: Marcio Leite Web
*/

if (!defined('ABSPATH')) {
    exit; // Impede o acesso direto
}

// Registrar o widget
class WC_Product_Carousel_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wc_product_carousel_widget',
            __('WooCommerce Product Carousel', 'text_domain'),
            array('description' => __('Displays a customizable WooCommerce product carousel by category with style options.', 'text_domain'))
        );
    }

    // Formulário de configuração no painel de widgets
    public function form($instance) {
        $selected_category = !empty($instance['category']) ? $instance['category'] : '';
        $num_products = !empty($instance['num_products']) ? $instance['num_products'] : 10;
        $autoplay = !empty($instance['autoplay']) ? $instance['autoplay'] : '';
        $carousel_speed = !empty($instance['carousel_speed']) ? $instance['carousel_speed'] : 3000;
        
        $title_color = !empty($instance['title_color']) ? $instance['title_color'] : '#000000';
        $price_color = !empty($instance['price_color']) ? $instance['price_color'] : '#000000';
        $button_background = !empty($instance['button_background']) ? $instance['button_background'] : '#000000';
        $button_text_color = !empty($instance['button_text_color']) ? $instance['button_text_color'] : '#FFFFFF';
        $button_border_radius = !empty($instance['button_border_radius']) ? $instance['button_border_radius'] : '4px';
        
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php _e('Select Product Category:', 'text_domain'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>">
                <?php
                $categories = get_terms('product_cat');
                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr($category->term_id) . '" ' . selected($selected_category, $category->term_id, false) . '>' . esc_html($category->name) . '</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('num_products')); ?>"><?php _e('Number of Products to Show:', 'text_domain'); ?></label>
            <input type="number" id="<?php echo esc_attr($this->get_field_id('num_products')); ?>" name="<?php echo esc_attr($this->get_field_name('num_products')); ?>" value="<?php echo esc_attr($num_products); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('autoplay')); ?>"><?php _e('Autoplay:', 'text_domain'); ?></label>
            <input type="checkbox" id="<?php echo esc_attr($this->get_field_id('autoplay')); ?>" name="<?php echo esc_attr($this->get_field_name('autoplay')); ?>" value="1" <?php checked($autoplay, '1'); ?> />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('carousel_speed')); ?>"><?php _e('Carousel Speed (ms):', 'text_domain'); ?></label>
            <input type="number" id="<?php echo esc_attr($this->get_field_id('carousel_speed')); ?>" name="<?php echo esc_attr($this->get_field_name('carousel_speed')); ?>" value="<?php echo esc_attr($carousel_speed); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title_color')); ?>"><?php _e('Title Color:', 'text_domain'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id('title_color')); ?>" name="<?php echo esc_attr($this->get_field_name('title_color')); ?>" value="<?php echo esc_attr($title_color); ?>" class="my-color-picker" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('price_color')); ?>"><?php _e('Price Color:', 'text_domain'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id('price_color')); ?>" name="<?php echo esc_attr($this->get_field_name('price_color')); ?>" value="<?php echo esc_attr($price_color); ?>" class="my-color-picker" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_background')); ?>"><?php _e('Button Background Color:', 'text_domain'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id('button_background')); ?>" name="<?php echo esc_attr($this->get_field_name('button_background')); ?>" value="<?php echo esc_attr($button_background); ?>" class="my-color-picker" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text_color')); ?>"><?php _e('Button Text Color:', 'text_domain'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id('button_text_color')); ?>" name="<?php echo esc_attr($this->get_field_name('button_text_color')); ?>" value="<?php echo esc_attr($button_text_color); ?>" class="my-color-picker" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_border_radius')); ?>"><?php _e('Button Border Radius:', 'text_domain'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id('button_border_radius')); ?>" name="<?php echo esc_attr($this->get_field_name('button_border_radius')); ?>" value="<?php echo esc_attr($button_border_radius); ?>" />
        </p>
        <?php
    }

    // Salvar as opções do widget
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['category'] = (!empty($new_instance['category'])) ? strip_tags($new_instance['category']) : '';
        $instance['num_products'] = (!empty($new_instance['num_products'])) ? intval($new_instance['num_products']) : 10;
        $instance['autoplay'] = (!empty($new_instance['autoplay'])) ? strip_tags($new_instance['autoplay']) : '';
        $instance['carousel_speed'] = (!empty($new_instance['carousel_speed'])) ? intval($new_instance['carousel_speed']) : 3000;
        $instance['title_color'] = (!empty($new_instance['title_color'])) ? strip_tags($new_instance['title_color']) : '#000000';
        $instance['price_color'] = (!empty($new_instance['price_color'])) ? strip_tags($new_instance['price_color']) : '#000000';
        $instance['button_background'] = (!empty($new_instance['button_background'])) ? strip_tags($new_instance['button_background']) : '#000000';
        $instance['button_text_color'] = (!empty($new_instance['button_text_color'])) ? strip_tags($new_instance['button_text_color']) : '#FFFFFF';
        $instance['button_border_radius'] = (!empty($new_instance['button_border_radius'])) ? strip_tags($new_instance['button_border_radius']) : '4px';
        return $instance;
    }

    // Exibir o widget no frontend
    public function widget($args, $instance) {
        if (!class_exists('WooCommerce')) {
            echo __('WooCommerce is not activated!', 'text_domain');
            return;
        }

        $category_id = !empty($instance['category']) ? $instance['category'] : '';
        $num_products = !empty($instance['num_products']) ? $instance['num_products'] : 10;
        $autoplay = !empty($instance['autoplay']) ? 'true' : 'false';
        $carousel_speed = !empty($instance['carousel_speed']) ? $instance['carousel_speed'] : 3000;

        $title_color = !empty($instance['title_color']) ? $instance['title_color'] : '#000000';
        $price_color = !empty($instance['price_color']) ? $instance['price_color'] : '#000000';
        $button_background = !empty($instance['button_background']) ? $instance['button_background'] : '#000000';
        $button_text_color = !empty($instance['button_text_color']) ? $instance['button_text_color'] : '#FFFFFF';
        $button_border_radius = !empty($instance['button_border_radius']) ? $instance['button_border_radius'] : '4px';

        // Exibir o carrossel
        echo $args['before_widget'];
        if (!empty($category_id)) {
            $this->display_product_carousel($category_id, $num_products, $autoplay, $carousel_speed, $title_color, $price_color, $button_background, $button_text_color, $button_border_radius);
        } else {
            echo __('Please select a product category.', 'text_domain');
        }
        echo $args['after_widget'];
    }

    // Função para exibir o carrossel de produtos
    private function display_product_carousel($category_id, $num_products, $autoplay, $carousel_speed, $title_color, $price_color, $button_background, $button_text_color, $button_border_radius) {
        $query_args = array(
            'post_type' => 'product',
            'posts_per_page' => $num_products,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $category_id,
                ),
            ),
        );

        $products = new WP_Query($query_args);

        if ($products->have_posts()) : ?>
            <div class="product-carousel-widget">
                <div class="owl-carousel">
                    <?php while ($products->have_posts()) : $products->the_post(); ?>
                        <div class="product-item" style="text-align:center;">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('woocommerce_thumbnail'); ?>
                                <?php endif; ?>
                                <h5 class="woocommerce-loop-product__title"><?php the_title(); ?></h5>
                            </a>
                            <p class="price"><?php woocommerce_template_loop_price(); ?></p>
                            <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" style="padding:14px; width:100%;" class="button add_to_cart_button">
								<?php _e('Comprar', 'text_domain'); ?>
							</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <style>
                .product-carousel-widget .woocommerce-loop-product__title {
                    color: <?php echo esc_attr($title_color); ?>;
                }
                .product-carousel-widget .price {
                    color: <?php echo esc_attr($price_color); ?>;
                }
                .product-carousel-widget .button.add_to_cart_button {
                    background-color: <?php echo esc_attr($button_background); ?>;
                    color: <?php echo esc_attr($button_text_color); ?>;
                    border-radius: <?php echo esc_attr($button_border_radius); ?>;
                }
            </style>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('.owl-carousel').owlCarousel({
                        loop: true,
                        margin: 10,
                        nav: true,
                        autoplay: <?php echo $autoplay; ?>,
                        autoplayTimeout: <?php echo $carousel_speed; ?>,
                        responsive: {
                            0: { items: 1 },
                            600: { items: 2 },
                            1000: { items: 4 }
                        }
                    });
                });
            </script>
            <?php
            wp_reset_postdata();
        else :
            echo __('No products found in this category.', 'text_domain');
        endif;
    }
}

// Registrar o widget
function register_wc_product_carousel_widget() {
    register_widget('WC_Product_Carousel_Widget');
}
add_action('widgets_init', 'register_wc_product_carousel_widget');

// Registrar script Owl Carousel
function enqueue_owl_carousel_scripts() {
    wp_enqueue_script('owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array('jquery'), '2.3.4', true);
    wp_enqueue_style('owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', array(), '2.3.4');
    wp_enqueue_style('owl-carousel-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css', array(), '2.3.4');
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_add_inline_script('wp-color-picker', 'jQuery(document).ready(function($) { $(".my-color-picker").wpColorPicker(); });');
}
add_action('wp_enqueue_scripts', 'enqueue_owl_carousel_scripts');

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Реєструємо всі ACF групи + блок
 */
function msmf_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    /*
     * ------------------------------------------------------------------
     *  ГРУПА ДЛЯ КОНСТРУКТОРА ФОРМ (post type: ms_form)
     * ------------------------------------------------------------------
     */
    acf_add_local_field_group( [
        'key'    => 'group_ms_form_builder',
        'title'  => 'MS Form Builder',
        'fields' => [

            [
                'key'   => 'field_ms_form_recipient_email',
                'label' => 'Recipient email',
                'name'  => 'ms_form_recipient_email',
                'type'  => 'email',
                'instructions' => 'Якщо порожньо — буде використано e-mail адміністратора сайту.',
            ],
            [
                'key'   => 'field_ms_form_redirect_url',
                'label' => 'Redirect URL after submit',
                'name'  => 'ms_form_redirect_url',
                'type'  => 'url',
                'instructions' => 'Якщо порожньо — редірект на ту ж сторінку з ?ms_form_submitted=1.',
            ],

            [
                'key'        => 'field_ms_steps',
                'label'      => 'Steps',
                'name'       => 'ms_steps',
                'type'       => 'repeater',
                'layout'     => 'row',
                'button_label' => 'Add step',
                'sub_fields'  => [
                    [
                        'key'   => 'field_ms_step_label',
                        'label' => 'Step title',
                        'name'  => 'ms_step_label',
                        'type'  => 'text',
                    ],
                    [
                        'key'   => 'field_ms_step_description',
                        'label' => 'Step description',
                        'name'  => 'ms_step_description',
                        'type'  => 'textarea',
                        'rows'  => 3,
                    ],
                    [
                        'key'   => 'field_ms_step_fields',
                        'label' => 'Fields',
                        'name'  => 'ms_step_fields',
                        'type'  => 'repeater',
                        'layout'=> 'row',
                        'button_label' => 'Add field',
                        'sub_fields' => [
                            [
                                'key'   => 'field_ms_field_label',
                                'label' => 'Field label',
                                'name'  => 'ms_field_label',
                                'type'  => 'text',
                            ],
                            [
                                'key'   => 'field_ms_field_name',
                                'label' => 'Field name (slug)',
                                'name'  => 'ms_field_name',
                                'type'  => 'text',
                                'instructions' => 'Без пробілів, наприклад subject, level, comment.',
                            ],
                            [
                                'key'   => 'field_ms_field_type',
                                'label' => 'Field type',
                                'name'  => 'ms_field_type',
                                'type'  => 'select',
                                'choices' => [
                                    'text'     => 'Text',
                                    'textarea' => 'Textarea',
                                    'radio'    => 'Radio buttons',
                                    'checkbox' => 'Checkboxes',
                                    'select'   => 'Select',
                                ],
                                'ui' => 1,
                            ],
                            [
                                'key'   => 'field_ms_field_placeholder',
                                'label' => 'Placeholder',
                                'name'  => 'ms_field_placeholder',
                                'type'  => 'text',
                                'conditional_logic' => [
                                    [
                                        [
                                            'field'    => 'field_ms_field_type',
                                            'operator' => '==',
                                            'value'    => 'text',
                                        ],
                                    ],
                                    [
                                        [
                                            'field'    => 'field_ms_field_type',
                                            'operator' => '==',
                                            'value'    => 'textarea',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'key'   => 'field_ms_field_required',
                                'label' => 'Required',
                                'name'  => 'ms_field_required',
                                'type'  => 'true_false',
                                'ui'    => 1,
                            ],
                            [
                                'key'   => 'field_ms_field_appearance',
                                'label' => 'Appearance (for options)',
                                'name'  => 'ms_field_appearance',
                                'type'  => 'select',
                                'choices' => [
                                    'icon' => 'Icon cards (як на першому скріні)',
                                    'text' => 'Text pills (як на другому скріні)',
                                ],
                                'ui'  => 1,
                                'conditional_logic' => [
                                    [
                                        [
                                            'field'    => 'field_ms_field_type',
                                            'operator' => '==',
                                            'value'    => 'radio',
                                        ],
                                    ],
                                    [
                                        [
                                            'field'    => 'field_ms_field_type',
                                            'operator' => '==',
                                            'value'    => 'checkbox',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'key'   => 'field_ms_field_options',
                                'label' => 'Options',
                                'name'  => 'ms_field_options',
                                'type'  => 'repeater',
                                'layout'=> 'table',
                                'button_label' => 'Add option',
                                'conditional_logic' => [
                                    [
                                        [
                                            'field'    => 'field_ms_field_type',
                                            'operator' => '==',
                                            'value'    => 'radio',
                                        ],
                                    ],
                                    [
                                        [
                                            'field'    => 'field_ms_field_type',
                                            'operator' => '==',
                                            'value'    => 'checkbox',
                                        ],
                                    ],
                                    [
                                        [
                                            'field'    => 'field_ms_field_type',
                                            'operator' => '==',
                                            'value'    => 'select',
                                        ],
                                    ],
                                ],
                                'sub_fields' => [
                                    [
                                        'key'   => 'field_ms_option_label',
                                        'label' => 'Label',
                                        'name'  => 'ms_option_label',
                                        'type'  => 'text',
                                    ],
                                    [
                                        'key'   => 'field_ms_option_value',
                                        'label' => 'Value',
                                        'name'  => 'ms_option_value',
                                        'type'  => 'text',
                                        'instructions' => 'Якщо порожньо — візьметься з Label.',
                                    ],
                                    [
                                        'key'   => 'field_ms_option_icon',
                                        'label' => 'Icon (для варіанта "icon")',
                                        'name'  => 'ms_option_icon',
                                        'type'  => 'image',
                                        'return_format' => 'array',
                                        'preview_size'  => 'thumbnail',
                                        'library'       => 'all',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'ms_form',
                ],
            ],
        ],
    ] );

    /*
     * ------------------------------------------------------------------
     *  ACF BLOCK + ГРУПА ПОЛІВ ДЛЯ НЬОГО
     * ------------------------------------------------------------------
     */

    if ( function_exists( 'acf_register_block_type' ) ) {

        // 1) Реєстрація блоку
        acf_register_block_type( [
            'name'            => 'ms-multistep-form',               // slug блоку
            'title'           => __( 'MS Multistep Form', 'msmf' ),
            'description'     => __( 'Вставити одну з мультистеп форм', 'msmf' ),
            'category'        => 'widgets',
            'icon'            => 'feedback',
            'supports'        => [
                'align' => false,
            ],
            'render_callback' => 'msmf_render_form_block',
            'mode'            => 'edit',
        ] );

        // 2) Field group для блоку (вибір форми)
        acf_add_local_field_group( [
            'key'    => 'group_ms_form_block',
            'title'  => 'MS Multistep Form Block',
            'fields' => [
                [
                    'key'          => 'field_ms_form_block_form',
                    'label'        => 'Form',
                    'name'         => 'ms_form_block_form',
                    'type'         => 'post_object',
                    'post_type'    => [ 'ms_form' ],
                    'return_format'=> 'id',
                    'ui'           => 1,
                ],
            ],
            'location' => [
                [
                    [
                        'param'    => 'block',
                        'operator' => '==',
                        'value'    => 'acf/ms-multistep-form', // !!! 'acf/' + name вище
                    ],
                ],
            ],
        ] );
    }
}
add_action( 'acf/init', 'msmf_register_acf_fields' );

/**
 * Рендер ACF-блоку
 */
function msmf_render_form_block( $block, $content = '', $is_preview = false, $post_id = 0 ) {
    $form_id = get_field( 'ms_form_block_form' );

    if ( ! $form_id ) {
        echo '<p style="color:#9ca3af;">Select form in block settings.</p>';
        return;
    }

    if ( function_exists( 'msmf_init_plugin' ) ) {
        $plugin = msmf_init_plugin();
        if ( $plugin instanceof MSMF_Plugin ) {
            $plugin->render_form( $form_id );
            return;
        }
    }

    echo do_shortcode( '[ms_form id="' . (int) $form_id . '"]' );
}

<?php
if ( function_exists( 'acf_add_local_field_group' ) ) {

  acf_add_local_field_group([
    'key' => 'group_skalum_page_settings',
    'title' => 'Page Settings',
    'fields' => [
      [
        'key' => 'field_hide_title',
        'label' => 'Hide page title',
        'name' => 'hide_title',
        'type' => 'true_false',
        'ui'   => 1,
        'default_value' => 0,
      ],
    ],
    'location' => [
      [
        [
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'page',
        ],
      ],
    ],
    'position' => 'side',
    'style'    => 'default',
    'label_placement' => 'top',
  ]);

}

<?php
if (!defined('ABSPATH')) {
    exit;
}

class MSMF_Plugin
{

    public function __construct()
    {
        add_action('init', [$this, 'register_post_types']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_shortcode('ms_form', [$this, 'shortcode']);

        add_action('admin_post_nopriv_ms_form_submit', [$this, 'handle_submit']);
        add_action('admin_post_ms_form_submit', [$this, 'handle_submit']);

        add_filter('manage_ms_entry_posts_columns', [$this, 'entry_columns']);
        add_action('manage_ms_entry_posts_custom_column', [$this, 'render_entry_column'], 10, 2);
    }

    /**
     * CPT: ms_form (конструктор) + ms_entry (заявки)
     */
    public function register_post_types()
    {

        register_post_type('ms_form', [
            'label' => 'Multi-step forms',
            'public' => false,
            'show_ui' => true,
            'supports' => ['title'],
            'menu_icon' => 'dashicons-feedback',
            'show_in_rest' => false,
        ]);

        register_post_type('ms_entry', [
            'label' => 'Form entries',
            'public' => false,
            'show_ui' => true,
            'supports' => ['title'],
            'menu_icon' => 'dashicons-list-view',
            'show_in_rest' => false,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'capabilities' => [
                'create_posts' => 'do_not_allow', // ховає "Add New"
            ],
        ]);
    }

    /**
     * CSS + JS
     */
    public function enqueue_assets()
    {
        wp_enqueue_style(
            'ms-multistep-form',
            MSMF_URL . 'assets/css/style.css',
            [],
            '0.2.0'
        );

        wp_enqueue_script(
            'ms-multistep-form',
            MSMF_URL . 'assets/js/form.js',
            ['jquery'],
            '0.2.0',
            true
        );
    }

    /**
     * [ms_form id="123"]
     */
    public function shortcode($atts)
    {
        $atts = shortcode_atts([
            'id' => 0,
        ], $atts);

        $form_id = absint($atts['id']);
        if (!$form_id) {
            return '<p>No form id provided.</p>';
        }

        ob_start();
        $this->render_form($form_id);
        return ob_get_clean();
    }

    /**
     * Рендер всієї форми (прогрес-бар + кроки)
     */
    public function render_form($form_id)
    {
        $steps = get_field('ms_steps', $form_id);
        if (empty($steps)) {
            echo '<p>Form is not configured.</p>';
            return;
        }

        $total_steps = count($steps);
        ?>

        <form class="ms-form" data-ms-form="<?php echo esc_attr($form_id); ?>"
            action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="action" value="ms_form_submit">
            <input type="hidden" name="ms_form_id" value="<?php echo (int) $form_id; ?>">
            <?php wp_nonce_field('ms_form_submit_' . $form_id, 'ms_form_nonce'); ?>

            <!-- PROGRESS BAR -->
            <div class="ms-progress" data-ms-progress>
                <div class="ms-progress__bar">
                    <div class="ms-progress__bar-fill"></div>
                </div>
                <div class="ms-progress__steps">
                    <?php foreach ($steps as $index => $step):
                        $label = $step['ms_step_label'] ?? sprintf('Step %d', $index + 1);
                        ?>
                        <div class="ms-progress__step" data-step="<?php echo esc_attr($index); ?>">
                            <span class="ms-progress__step-index"><?php echo (int) ($index + 1); ?></span>
                            <span class="ms-progress__step-label"><?php echo esc_html($label); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- STEPS -->
            <div class="ms-form__steps">
                <?php foreach ($steps as $step_index => $step): ?>
                    <?php
                    $step_title = $step['ms_step_label'] ?? '';
                    $step_desc = $step['ms_step_description'] ?? '';
                    $fields = $step['ms_step_fields'] ?? [];
                    ?>
                    <div class="ms-step" data-step="<?php echo esc_attr($step_index); ?>">
                        <?php if ($step_title): ?>
                            <h3 class="ms-step__title"><?php echo esc_html($step_title); ?></h3>
                        <?php endif; ?>

                        <?php if ($step_desc): ?>
                            <div class="ms-step__description">
                                <?php echo wp_kses_post(nl2br($step_desc)); ?>
                            </div>
                        <?php endif; ?>

                        <div class="ms-step__fields">
                            <?php foreach ($fields as $field): ?>
                                <?php echo $this->render_field($field); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="ms-form__actions">
                <button type="button" class="ms-btn ms-btn--secondary ms-btn-prev">Назад</button>
                <button type="button" class="ms-btn ms-btn--primary ms-btn-next">Далі</button>
                <button type="submit" class="ms-btn ms-btn--primary ms-btn-submit">Надіслати</button>
            </div>
        </form>

        <?php
    }

    /**
     * Рендер одного поля (options / іконки / pills)
     */
    private function render_field($field)
    {
        $type = $field['ms_field_type'] ?? 'text';
        $label = $field['ms_field_label'] ?? '';
        $name = $field['ms_field_name'] ?? '';
        $required = !empty($field['ms_field_required']);
        $variant = $field['ms_field_appearance'] ?? 'text';
        $options = $field['ms_field_options'] ?? [];
        $placeholder = $field['ms_field_placeholder'] ?? '';

        if (!$name) {
            $name = 'field_' . wp_generate_uuid4();
        }

        ob_start();
        ?>
        <div class="ms-field ms-field--<?php echo esc_attr($type); ?>">
            <?php if ($label): ?>
                <div class="ms-field__label">
                    <label><?php echo esc_html($label); ?><?php if ($required)
                           echo ' *'; ?></label>
                </div>
            <?php endif; ?>

            <div class="ms-field__control">
                <?php
                switch ($type) {
                    case 'textarea':
                        ?>
                        <textarea name="<?php echo esc_attr($name); ?>" class="ms-input ms-input--textarea" <?php if ($required)
                               echo 'required'; ?> placeholder="<?php echo esc_attr($placeholder); ?>"></textarea>
                        <?php
                        break;

                    case 'select':
                        ?>
                        <div class="ms-select-wrapper">
                            <select name="<?php echo esc_attr($name); ?>" class="ms-input ms-input--select" <?php if ($required)
                                   echo 'required'; ?>>
                                <option value="">— Оберіть —</option>
                                <?php foreach ($options as $opt):
                                    $ov = $opt['ms_option_value'] ?? '';
                                    $ol = $opt['ms_option_label'] ?? $ov;
                                    ?>
                                    <option value="<?php echo esc_attr($ov); ?>">
                                        <?php echo esc_html($ol); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
                        break;

                    case 'radio':
                    case 'checkbox':
                        $input_type = $type;
                        ?>
                        <div class="ms-options ms-options--<?php echo esc_attr($variant); ?>">
                            <?php foreach ($options as $i => $opt):
                                $ov = $opt['ms_option_value'] ?? '';
                                $ol = $opt['ms_option_label'] ?? $ov;
                                $icon = $opt['ms_option_icon'] ?? null;
                                $id = $name . '_' . $i;
                                ?>
                                <div class="ms-option">
                                    <input type="<?php echo esc_attr($input_type); ?>" id="<?php echo esc_attr($id); ?>"
                                        name="<?php echo esc_attr($name); ?><?php echo $input_type === 'checkbox' ? '[]' : ''; ?>"
                                        value="<?php echo esc_attr($ov); ?>" class="ms-option__input" <?php if ($required && $i === 0 && $input_type === 'radio')
                                               echo 'required'; ?> />
                                    <label for="<?php echo esc_attr($id); ?>" class="ms-option__label">
                                        <?php if ($variant === 'icon' && !empty($icon)): ?>
                                            <?php
                                            $src = is_array($icon) ? ($icon['url'] ?? '') : wp_get_attachment_image_url($icon, 'thumbnail');
                                            if ($src):
                                                ?>
                                                <span class="ms-option__icon">
                                                    <img src="<?php echo esc_url($src); ?>" alt="">
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <span class="ms-option__text"><?php echo esc_html($ol); ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php
                        break;

                    case 'text':
                    default:
                        ?>
                        <input type="text" name="<?php echo esc_attr($name); ?>" class="ms-input ms-input--text"
                            placeholder="<?php echo esc_attr($placeholder); ?>" <?php if ($required)
                                   echo 'required'; ?> />
                        <?php
                        break;
                }
                ?>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * Обробка сабміту: створюємо ms_entry + шлемо лист
     */
    public function handle_submit()
    {

        $form_id = isset($_POST['ms_form_id']) ? absint($_POST['ms_form_id']) : 0;
        if (!$form_id) {
            wp_die('Invalid form.');
        }

        if (
            !isset($_POST['ms_form_nonce']) ||
            !wp_verify_nonce($_POST['ms_form_nonce'], 'ms_form_submit_' . $form_id)
        ) {
            wp_die('Security check failed.');
        }

        $steps = get_field('ms_steps', $form_id);
        if (empty($steps)) {
            wp_die('Form is not configured.');
        }

        $data = [];

        foreach ($steps as $step) {
            $fields = $step['ms_step_fields'] ?? [];
            if (empty($fields)) {
                continue;
            }

            foreach ($fields as $field) {
                $name = $field['ms_field_name'] ?? '';
                $label = $field['ms_field_label'] ?? $name;

                if (!$name || !isset($_POST[$name])) {
                    continue;
                }

                $raw = $_POST[$name];

                if (is_array($raw)) {
                    $value = array_map('sanitize_text_field', $raw);
                } else {
                    $value = sanitize_text_field($raw);
                }

                $data[$name] = [
                    'label' => $label,
                    'value' => $value,
                ];
            }
        }

        // Створюємо entry
        $form_title = get_the_title($form_id);
        $entry_title = sprintf(
            'Entry for form "%s" – %s',
            $form_title ?: ('Form #' . $form_id),
            current_time('mysql')
        );

        $entry_id = wp_insert_post([
            'post_type' => 'ms_entry',
            'post_status' => 'publish',
            'post_title' => $entry_title,
        ]);

        if (!is_wp_error($entry_id) && $entry_id) {
            update_post_meta($entry_id, 'ms_entry_form_id', $form_id);
            update_post_meta($entry_id, 'ms_entry_data', $data);
        }

        // Готуємо лист
        $recipient = get_field('ms_form_recipient_email', $form_id);
        if (!$recipient) {
            $recipient = get_option('admin_email');
        }

        $subject = sprintf(
            'New entry from form: %s',
            $form_title ?: ('Form #' . $form_id)
        );

        $lines = [];
        foreach ($data as $row) {
            $label = $row['label'] ?: '';
            $value = $row['value'];

            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $lines[] = sprintf('%s: %s', $label, $value);
        }

        $message = "New form entry from: " . ($form_title ?: ('Form #' . $form_id)) . "\n\n";
        $message .= implode("\n", $lines);
        if ($entry_id) {
            $message .= "\n\nEntry ID: " . $entry_id;
        }

        if ($recipient) {
            wp_mail($recipient, $subject, $message);
        }

        // Редірект
        $redirect = get_field('ms_form_redirect_url', $form_id);
        if (!$redirect) {
            $redirect = wp_get_referer();
        }
        if (!$redirect) {
            $redirect = home_url('/');
        }

        $redirect = add_query_arg('ms_form_submitted', '1', $redirect);
        wp_safe_redirect($redirect);
        exit;
    }

    /**
     * Колонки для списку ms_entry
     */
    public function entry_columns($columns)
    {
        // Базові колонки WordPress
        $new = [];
        $new['cb'] = $columns['cb'];
        $new['title'] = __('Entry', 'msmf');
        $new['ms_form'] = __('Form', 'msmf');
        $new['ms_data'] = __('Summary', 'msmf');
        $new['date'] = $columns['date'];

        return $new;
    }

    /**
     * Вміст кастомних колонок
     */
    public function render_entry_column($column, $post_id)
    {
        if ($column === 'ms_form') {
            $form_id = (int) get_post_meta($post_id, 'ms_entry_form_id', true);
            if ($form_id) {
                $title = get_the_title($form_id);
                $edit_link = get_edit_post_link($form_id);
                if ($edit_link) {
                    echo '<a href="' . esc_url($edit_link) . '">' . esc_html($title) . '</a>';
                } else {
                    echo esc_html($title);
                }
            } else {
                echo '—';
            }
        }

        if ($column === 'ms_data') {
            $data = get_post_meta($post_id, 'ms_entry_data', true);
            if (!is_array($data) || empty($data)) {
                echo '—';
                return;
            }

            // Спробуємо витягти імʼя / email окремо, якщо є
            $name = null;
            $email = null;
            $parts = [];

            foreach ($data as $key => $row) {
                $label = $row['label'] ?? $key;
                $value = $row['value'] ?? '';

                if (is_array($value)) {
                    $value = implode(', ', $value);
                }

                // Спроба вгадати name/email за slug’ом
                $lower_key = strtolower($key);
                if (!$name && (str_contains($lower_key, 'name') || $lower_key === 'fullname')) {
                    $name = $value;
                }
                if (!$email && (str_contains($lower_key, 'email') || $lower_key === 'e-mail')) {
                    $email = $value;
                }

                $parts[] = sprintf('%s: %s', esc_html($label), esc_html($value));
            }

            if ($name || $email) {
                echo esc_html(trim($name . ' ' . ($email ? '(' . $email . ')' : '')));
                echo '<br /><span style="color:#6b7280;font-size:11px;">' . esc_html(implode(' | ', $parts)) . '</span>';
            } else {
                echo esc_html(implode(' | ', $parts));
            }
        }
    }
}

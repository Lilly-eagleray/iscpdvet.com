<?php
// ====================
// lessons meta box with WYSIWYG content and conditional file field
// ====================

function lessons_meta_box() {
    add_meta_box(
        'lessons_box',
        'שיעורים',
        'lessons_box_html',
        'course_post_type', // החליפי בשם ה-post type שלך
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'lessons_meta_box');

function lessons_box_html($post) {
    $count = get_post_meta($post->ID, 'lessons', true);
    $count = $count ? intval($count) : 0;
    ?>
    <div id="lessons-wrapper" style="max-height:500px; overflow-y:auto; padding:5px;">
        <?php for ($i = 0; $i < $count; $i++): 
            $type = get_post_meta($post->ID, "lessons_{$i}_type", true);
            $file_id = get_post_meta($post->ID, "lessons_{$i}_file", true);
            $file_name = '';
            $file_size = '';
            if($file_id){
                $file_path = get_attached_file($file_id);
                $file_name = basename($file_path);
                $file_size = size_format(filesize($file_path), 2);
            }
            $show_file = in_array($type, ['slideshow','pdf','video']); // Conditional Logic
        ?>
            <div class="lesson-item" style="border:1px solid #ccc; padding:10px; margin-bottom:10px; border-radius:5px; background:#f9f9f9; position: relative; cursor:move;">
                <span class="lesson-handle" style="position:absolute; top:5px; left:5px; cursor:move; font-weight:bold;">≡</span>
                <button type="button" class="remove-lesson" style="position:absolute; top:5px; right:5px; background:#ff4d4d; color:white; border:none; padding:3px 6px; cursor:pointer; border-radius:3px;">❌ מחק שיעור</button>
                
                <p><label>שם:</label>
                    <input type="text" name="lessons_data[<?php echo $i; ?>][name]" value="<?php echo esc_attr(get_post_meta($post->ID, "lessons_{$i}_name", true)); ?>" style="width:100%;">
                </p>

                <p><label>סוג שיעור:</label>
                    <select class="lesson-type" name="lessons_data[<?php echo $i; ?>][type]">
                        <?php 
                        $types = ['none'=>'ללא','iplayer'=>'iplayer','iplayer_playlist'=>'iplayer playlist','slideshow'=>'slideshow','pdf'=>'pdf','video'=>'video','youtube'=>'youtube'];
                        foreach($types as $key=>$label){
                            echo '<option value="'.esc_attr($key).'" '.selected($type,$key,false).'>'.esc_html($label).'</option>';
                        }
                        ?>
                    </select>
                </p>

                <p><label>קישור:</label>
                    <input type="text" name="lessons_data[<?php echo $i; ?>][url]" value="<?php echo esc_attr(get_post_meta($post->ID, "lessons_{$i}_url", true)); ?>" style="width:100%;">
                </p>

                <?php if($show_file): ?>
                <p class="lesson-file-wrapper">
                    <label>קובץ:</label>
                    <input type="hidden" class="lesson-file-url" name="lessons_data[<?php echo $i; ?>][file]" value="<?php echo esc_attr($file_id); ?>">
                    <button type="button" class="button upload-lesson-file">בחר קובץ</button>
                    <?php if($file_id): ?>
                        <span class="lesson-file-info" style="margin-left:10px;"><?php echo esc_html($file_name) . " ({$file_size})"; ?></span>
                    <?php else: ?>
                        <span class="lesson-file-info" style="margin-left:10px;">לא נבחר קובץ</span>
                    <?php endif; ?>
                </p>
                <?php endif; ?>

                <p><label>תוכן:</label>
                <?php 
                $editor_id = "lessons_{$i}_content";
                $editor_content = get_post_meta($post->ID, $editor_id, true);
                wp_editor( $editor_content, $editor_id, [
                    'textarea_name' => "lessons_data[{$i}][content]",
                    'textarea_rows' => 6,
                    'media_buttons' => true,
                    'teeny' => false,
                ] );
                ?>
                </p>
            </div>
        <?php endfor; ?>
    </div>

    <button type="button" id="add-lesson" style="background:#4CAF50; color:white; border:none; padding:6px 12px; cursor:pointer; border-radius:4px; margin-top:5px;">➕ הוסף שיעור</button>

    <script>
    jQuery(document).ready(function($){
        // Drag & Drop
        $("#lessons-wrapper").sortable({
            handle: ".lesson-handle",
            placeholder: "lesson-placeholder",
            forcePlaceholderSize: true
        });

        // Add new lesson
        $("#add-lesson").on('click', function(){
            const wrapper = $("#lessons-wrapper");
            const count = wrapper.children('.lesson-item').length;
            const html = `
                <div class="lesson-item" style="border:1px solid #ccc; padding:10px; margin-bottom:10px; border-radius:5px; background:#f9f9f9; position: relative; cursor:move;">
                    <span class="lesson-handle" style="position:absolute; top:5px; left:5px; cursor:move; font-weight:bold;">≡</span>
                    <button type="button" class="remove-lesson" style="position:absolute; top:5px; right:5px; background:#ff4d4d; color:white; border:none; padding:3px 6px; cursor:pointer; border-radius:3px;">❌ מחק שיעור</button>
                    <p><label>שם:</label><input type="text" name="lessons_data[${count}][name]" style="width:100%;"></p>
                    <p><label>סוג שיעור:</label>
                        <select class="lesson-type" name="lessons_data[${count}][type]">
                            <option value="none">ללא</option>
                            <option value="iplayer">iplayer</option>
                            <option value="iplayer_playlist">iplayer playlist</option>
                            <option value="slideshow">slideshow</option>
                            <option value="pdf">pdf</option>
                            <option value="video">video</option>
                            <option value="youtube">youtube</option>
                        </select>
                    </p>
                    <p><label>קישור:</label><input type="text" name="lessons_data[${count}][url]" style="width:100%;"></p>
                    <p class="lesson-file-wrapper" style="display:none;">
                        <label>קובץ:</label>
                        <input type="hidden" class="lesson-file-url" name="lessons_data[${count}][file]">
                        <button type="button" class="button upload-lesson-file">בחר קובץ</button>
                        <span class="lesson-file-info" style="margin-left:10px;">לא נבחר קובץ</span>
                    </p>
                    <p><label>תוכן:</label>
                        <textarea name="lessons_data[${count}][content]" style="width:100%;" rows="6"></textarea>
                    </p>
                </div>`;
            wrapper.append(html);
        });

        // Remove lesson
        $(document).on('click', '.remove-lesson', function(){
            $(this).closest('.lesson-item').remove();
        });

        // Media uploader
        var lesson_uploader;
        $(document).on('click', '.upload-lesson-file', function(e){
            e.preventDefault();
            var button = $(this);
            var input = button.prev('.lesson-file-url');
            var info_span = button.next('.lesson-file-info');

            if (lesson_uploader) {
                lesson_uploader.open();
                return;
            }

            lesson_uploader = wp.media.frames.file_frame = wp.media({
                title: 'בחר קובץ',
                button: { text: 'בחר קובץ' },
                multiple: false
            });

            lesson_uploader.on('select', function(){
                var attachment = lesson_uploader.state().get('selection').first().toJSON();
                input.val(attachment.id); // שמירת ID
                info_span.text(attachment.filename + ' (' + Math.round(attachment.filesize/1024) + ' KB)');
            });

            lesson_uploader.open();
        });

        // Conditional Logic – show/hide file field based on type
        $(document).on('change', '.lesson-type', function(){
            var type = $(this).val();
            var wrapper = $(this).closest('.lesson-item');
            if(['slideshow','pdf','video'].includes(type)){
                wrapper.find('.lesson-file-wrapper').show();
            } else {
                wrapper.find('.lesson-file-wrapper').hide();
                wrapper.find('.lesson-file-url').val('');
                wrapper.find('.lesson-file-info').text('לא נבחר קובץ');
            }
        }).trigger('change'); // הפעלה מיידית לטעינת ערכים קיימים
    });
    </script>

    <style>
        .lesson-placeholder {
            border: 2px dashed #ccc;
            background: #f0f0f0;
            height: 60px;
            margin-bottom: 10px;
            border-radius:5px;
        }
    </style>
    <?php
}

// ====================
// save meta
// ====================
function save_lessons_meta($post_id) {
    if (isset($_POST['lessons_data'])) {
        $lessons = $_POST['lessons_data'];
        $count   = count($lessons);

        update_post_meta($post_id, 'lessons', $count);

        foreach ($lessons as $i => $lesson) {
            update_post_meta($post_id, "lessons_{$i}_name", sanitize_text_field($lesson['name']));
            update_post_meta($post_id, "lessons_{$i}_type", sanitize_text_field($lesson['type']));
            update_post_meta($post_id, "lessons_{$i}_url", sanitize_text_field($lesson['url']));
            update_post_meta($post_id, "lessons_{$i}_file", intval($lesson['file'])); // שמירה כ-ID
            update_post_meta($post_id, "lessons_{$i}_content", wp_kses_post($lesson['content'])); // WYSIWYG
        }
    }
}
add_action('save_post', 'save_lessons_meta');

/**
 * Get lessons array for a course (ACF-Repeater compatible structure)
 *
 * @param int $course_id
 * @return array
 */
function get_course_lessons( $course_id ) {
    
    $course_id = intval($course_id);
    if (!$course_id) return [];

    $count = get_post_meta($course_id, 'lessons', true);
    $count = $count ? intval($count) : 0;

    $lessons = [];

    for ($i = 0; $i < $count; $i++) {

        $file_id = get_post_meta($course_id, "lessons_{$i}_file", true);
        $file_url = $file_id ? wp_get_attachment_url($file_id) : '';

        $lessons[$i] = [
            'name'    => get_post_meta($course_id, "lessons_{$i}_name", true),
            'type'    => get_post_meta($course_id, "lessons_{$i}_type", true),
            'url'     => get_post_meta($course_id, "lessons_{$i}_url", true),
            'file'    => $file_url,
            'file_id' => $file_id, // אם תרצי גם את ה-ID
            'content' => get_post_meta($course_id, "lessons_{$i}_content", true),
        ];
    }

    return $lessons;
}


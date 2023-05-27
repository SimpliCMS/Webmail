<script src="{{ url('vendor/tinymce/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>
const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;

tinymce.init({
    selector: 'textarea#{{$selector}}',
    plugins: 'a11ychecker advcode advlist anchor autolink codesample fullscreen help image imagetools tinydrive help image imagetools tinydrive lists link media noneditable powerpaste preview searchreplace table template tinymcespellchecker visualblocks wordcount',
    editimage_cors_hosts: ['picsum.photos'],
    menubar: 'file edit view insert format tools table help',
    toolbar: 'insertfile a11ycheck undo redo | bold italic | forecolor backcolor | template codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image tinydrive',
    toolbar_sticky: true,
    toolbar_sticky_offset: isSmallScreen ? 102 : 108,
    autosave_ask_before_unload: true,
    autosave_interval: '30s',
    autosave_prefix: '{path}{query}-{id}-',
    autosave_restore_when_empty: false,
    autosave_retention: '2m',
    image_advtab: true,
    image_class_list: [
        {title: 'None', value: ''},
        {title: 'Some class', value: 'class-name'}
    ],
    importcss_append: true,
    template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
    template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
    height: {!! $height !!},
    image_caption: true,
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    noneditable_class: 'mceNonEditable',
    toolbar_mode: 'wrap',
    promotion: false,
    branding: false,
    indent: false,
    contextmenu: 'link image table',
    skin: "oxide-dark",
     content_css : 'default',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
});
</script>
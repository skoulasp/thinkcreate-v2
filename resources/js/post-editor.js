import {
    ClassicEditor,
    Essentials,
    Paragraph,
    Bold,
    Italic,
    Underline,
    Link,
    List,
    Heading,
    Image,
    ImageToolbar,
    ImageCaption,
    ImageStyle,
    ImageResize,
    ImageUpload,
    SimpleUploadAdapter,
    Table,
    TableToolbar,
    SourceEditing,
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';

const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
};

const initPostEditor = () => {
    const editorField = document.querySelector('textarea[data-rich-text-editor]');

    if (!editorField) {
        return;
    }

    const uploadUrl = editorField.getAttribute('data-editor-upload-url');
    const csrfToken = getCsrfToken();

    ClassicEditor.create(editorField, {
        licenseKey: 'GPL',
        plugins: [
            Essentials,
            Paragraph,
            Bold,
            Italic,
            Underline,
            Link,
            List,
            Heading,
            Image,
            ImageToolbar,
            ImageCaption,
            ImageStyle,
            ImageResize,
            ImageUpload,
            SimpleUploadAdapter,
            Table,
            TableToolbar,
            SourceEditing,
        ],
        toolbar: [
            'undo',
            'redo',
            '|',
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            '|',
            'bulletedList',
            'numberedList',
            '|',
            'link',
            'insertTable',
            'uploadImage',
            '|',
            'sourceEditing',
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
            ],
        },
        image: {
            toolbar: [
                'imageStyle:inline',
                'imageStyle:wrapText',
                'imageStyle:breakText',
                '|',
                'toggleImageCaption',
                'imageTextAlternative',
            ],
        },
        simpleUpload: {
            uploadUrl,
            withCredentials: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
        },
    })
        .then((editor) => {
            const form = editorField.closest('form');

            if (!form) {
                return;
            }

            form.addEventListener('submit', (event) => {
                const fileRepository = editor.plugins.get('FileRepository');
                const hasUploadingFiles = Array.from(fileRepository.loaders).some((loader) => loader.status === 'uploading');

                if (hasUploadingFiles) {
                    event.preventDefault();
                    alert('Please wait for image uploads to finish before saving.');
                    return;
                }

                editor.updateSourceElement();
            });
        })
        .catch((error) => {
            console.error('CKEditor initialization failed:', error);
        });
};

export default initPostEditor;

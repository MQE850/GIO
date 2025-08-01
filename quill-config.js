// quill-config.js

let quill;

function initQuill() {
    quill = new Quill('#descriptionEditor', {
        theme: 'snow',
        placeholder: 'Escribe la descripción aquí...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean']
            ]
        }
    });
}

function setQuillContent(html) {
    quill.root.innerHTML = html;
}

function getQuillContent() {
    return quill.root.innerHTML;
}

document.addEventListener("DOMContentLoaded", () => {
    initQuill();
});

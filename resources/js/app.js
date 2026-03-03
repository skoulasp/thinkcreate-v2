import './bootstrap';
import Alpine from 'alpinejs';
import './menu-editor';

window.Alpine = Alpine;

window.slugForm = (initialName = '', initialSlug = '', initialManual = false) => ({
    name: initialName ?? '',
    slug: initialSlug ?? '',
    manualSlug: Boolean(initialManual),

    init() {
        if (!this.manualSlug) {
            this.slug = this.slugify(this.name);
        }
    },

    slugify(value) {
        return String(value ?? '')
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    },

    syncSlug() {
        if (!this.manualSlug) {
            this.slug = this.slugify(this.name);
        }
    },

    toggleManual() {
        if (!this.manualSlug) {
            this.slug = this.slugify(this.name);
        }
    },
});

const maybeInitPostEditor = () => {
    if (!document.querySelector('textarea[data-rich-text-editor]')) {
        return;
    }

    import('./post-editor')
        .then((module) => {
            module.default();
        })
        .catch((error) => {
            console.error('Failed to load post editor module:', error);
        });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', maybeInitPostEditor);
} else {
    maybeInitPostEditor();
}

Alpine.start();

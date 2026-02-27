import './bootstrap';
import Alpine from 'alpinejs';

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

Alpine.start();

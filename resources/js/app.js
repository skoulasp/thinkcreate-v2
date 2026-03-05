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

window.commentVote = ({
    canVote = false,
    voteUrl = '',
    csrfToken = '',
    likesCount = 0,
    dislikesCount = 0,
    currentVote = null,
}) => ({
    canVote: Boolean(canVote),
    voteUrl,
    csrfToken,
    likesCount: Number(likesCount) || 0,
    dislikesCount: Number(dislikesCount) || 0,
    currentVote,
    isSubmitting: false,
    errorMessage: '',

    async submitVote(vote) {
        if (!this.canVote || this.isSubmitting) {
            return;
        }

        this.isSubmitting = true;
        this.errorMessage = '';

        try {
            const response = await fetch(this.voteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({ vote }),
            });

            if (!response.ok) {
                throw new Error('Unable to save vote.');
            }

            const payload = await response.json();
            this.likesCount = Number(payload.likes_count) || 0;
            this.dislikesCount = Number(payload.dislikes_count) || 0;
            this.currentVote = payload.current_vote;
        } catch (error) {
            this.errorMessage = 'Vote failed. Please try again.';
            console.error(error);
        } finally {
            this.isSubmitting = false;
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

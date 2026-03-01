function initMenuEditors() {
    document.querySelectorAll('[data-menu-items-sortable]').forEach((editor) => {
        const list = editor.querySelector('[data-menu-items-list]');
        const reorderUrl = editor.dataset.reorderUrl;
        const csrfToken = editor.dataset.csrfToken;
        const status = editor.querySelector('[data-order-status]');

        if (!list || !reorderUrl || !csrfToken) {
            return;
        }

        let isSaving = false;
        let statusTimer;

        const setStatus = (message, isError = false) => {
            if (!status) {
                return;
            }

            window.clearTimeout(statusTimer);
            status.textContent = message;
            status.classList.toggle('is-error', isError);

            if (message && !isError) {
                statusTimer = window.setTimeout(() => {
                    status.textContent = '';
                    status.classList.remove('is-error');
                }, 1500);
            }
        };

        let draggedRow = null;

        const postOrder = async () => {
            if (isSaving) {
                return;
            }

            const items = Array.from(list.querySelectorAll('[data-item-id]'))
                .map((row) => Number(row.dataset.itemId))
                .filter((id) => Number.isInteger(id));

            isSaving = true;
            setStatus('Saving order...');

            try {
                const response = await fetch(reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ items }),
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                setStatus('Order saved.');
            } catch (error) {
                setStatus('Could not save order. Please refresh and try again.', true);
            } finally {
                isSaving = false;
            }
        };

        list.querySelectorAll('[data-item-id]').forEach((row) => {
            row.draggable = true;

            row.addEventListener('dragstart', () => {
                draggedRow = row;
                row.classList.add('is-dragging');
            });

            row.addEventListener('dragend', () => {
                row.classList.remove('is-dragging');
                draggedRow = null;
            });

            row.addEventListener('dragover', (event) => {
                event.preventDefault();

                if (!draggedRow || draggedRow === row) {
                    return;
                }

                const rect = row.getBoundingClientRect();
                const shouldInsertBefore = event.clientY < rect.top + rect.height / 2;

                if (shouldInsertBefore) {
                    list.insertBefore(draggedRow, row);
                } else {
                    list.insertBefore(draggedRow, row.nextSibling);
                }
            });
        });

        list.addEventListener('drop', async (event) => {
            event.preventDefault();

            if (!draggedRow) {
                return;
            }

            await postOrder();
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMenuEditors);
} else {
    initMenuEditors();
}

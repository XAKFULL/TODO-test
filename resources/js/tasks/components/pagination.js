// components/pagination.js
export class Pagination {
    constructor() {
        this.container = document.getElementById('pagination');
        this.currentPage = 1;
        this.lastPage = 1;

        this.container?.addEventListener('click', (e) => {
            if (e.target.dataset.page) {
                this.setPage(Number(e.target.dataset.page));
                this.dispatchChange();
            }
        });
    }

    render(meta) {
        if (!this.container) return;

        this.currentPage = meta.current_page;
        this.lastPage = meta.last_page;

        this.container.innerHTML = Array.from(
            { length: this.lastPage },
            (_, i) => i + 1
        ).map(page => `
            <button 
                class="${page === this.currentPage ? 'active' : ''}"
                data-page="${page}"
            >${page}</button>
        `).join('');
    }

    setPage(page) {
        this.currentPage = page;
    }

    dispatchChange() {
        window.dispatchEvent(new CustomEvent('pagination-changed', {
            detail: { page: this.currentPage }
        }));
    }
}
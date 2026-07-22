<!-- Confirmation Modal -->
<div id="confirmation-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h3 class="text-xl font-bold text-slate-900 text-center mt-4" id="modal-title">
            Confirm Action
        </h3>
        <p class="mt-2 text-sm text-slate-600 text-center" id="modal-message">
            Are you sure you want to proceed?
        </p>

        <div class="flex gap-3 mt-8">
            <button type="button" id="confirm-btn"
                class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-2xl transition cursor-pointer">
                Yes
            </button>
            <button type="button" id="cancel-btn"
                class="flex-1 py-3 border border-slate-300 text-slate-700 font-semibold rounded-2xl hover:bg-slate-50 transition cursor-pointer">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    // Initialize confirmation modal
    function initConfirmationModal() {
        const modal = document.getElementById('confirmation-modal');
        const titleEl = document.getElementById('modal-title');
        const messageEl = document.getElementById('modal-message');
        const confirmBtn = document.getElementById('confirm-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        if (!modal || !titleEl || !messageEl || !confirmBtn || !cancelBtn) {
            console.error('Modal elements not found');
            return;
        }

        let callback = null;

        const hide = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            callback = null;
        };

        const confirm = () => {
            if (callback && typeof callback === 'function') {
                callback();
            }
            hide();
        };

        confirmBtn.addEventListener('click', confirm);
        cancelBtn.addEventListener('click', hide);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hide();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('flex')) {
                hide();
            }
        });

        window.confirmationModal = {
            show: (title, message, cb) => {
                titleEl.textContent = title;
                messageEl.textContent = message;
                callback = cb;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                confirmBtn.focus();
            },
            hide: hide
        };
    }

    // Initialize when script loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initConfirmationModal);
    } else {
        initConfirmationModal();
    }
</script>

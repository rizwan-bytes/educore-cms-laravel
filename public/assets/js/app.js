// ============================================
// EDUCORE — Global Ajax + SweetAlert Helpers
// ============================================

// Axios defaults
axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]')?.content;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

// SweetAlert2 dark theme toast
const Toast = Swal.mixin({
    toast: true, position: 'top-end', showConfirmButton: false,
    timer: 3000, timerProgressBar: true,
    background: '#111827', color: '#f1f5f9', iconColor: '#10b981'
});

window.toastSuccess = (msg) => Toast.fire({ icon: 'success', title: msg });
window.toastError   = (msg) => Toast.fire({ icon: 'error',   title: msg, iconColor: '#ef4444' });
window.toastWarning = (msg) => Toast.fire({ icon: 'warning', title: msg, iconColor: '#f59e0b' });
window.toastInfo    = (msg) => Toast.fire({ icon: 'info',    title: msg, iconColor: '#06b6d4' });

// Delete confirm
window.confirmDelete = (url, onSuccess) => {
    Swal.fire({
        title: 'Delete?', text: 'This cannot be undone.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#374151',
        confirmButtonText: 'Yes, delete', cancelButtonText: 'Cancel',
        background: '#111827', color: '#f1f5f9'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(url)
                .then(res => { toastSuccess(res.data.message || 'Deleted!'); if (onSuccess) onSuccess(res); })
                .catch(err => toastError(err.response?.data?.message || 'Error!'));
        }
    });
};

// Generic Ajax form submit
window.ajaxSubmit = (formEl, options = {}) => {
    const form = formEl instanceof HTMLElement ? formEl : document.querySelector(formEl);
    const btn  = form.querySelector('[type=submit]');
    const url  = options.url    || form.action;
    const method = options.method || form.getAttribute('method') || 'POST';
    const origText = btn?.innerHTML;

    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...'; }

    axios({ method, url, data: new FormData(form), headers: { 'Content-Type': 'multipart/form-data' } })
        .then(res => {
            toastSuccess(res.data.message || 'Saved successfully!');
            if (options.onSuccess)  options.onSuccess(res);
            if (options.resetForm)  form.reset();
            if (options.closeModal) bootstrap.Modal.getInstance(document.querySelector(options.closeModal))?.hide();
            if (options.reloadTable && window[options.reloadTable]) window[options.reloadTable].ajax.reload(null, false);
        })
        .catch(err => {
            const errors = err.response?.data?.errors;
            if (errors) {
                Object.keys(errors).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        let fb = input.nextElementSibling;
                        if (!fb || !fb.classList.contains('invalid-feedback')) {
                            fb = document.createElement('div'); fb.className = 'invalid-feedback'; input.after(fb);
                        }
                        fb.textContent = errors[field][0];
                    }
                });
            }
            toastError(err.response?.data?.message || 'Something went wrong!');
        })
        .finally(() => { if (btn) { btn.disabled = false; btn.innerHTML = origText; } });
};

document.addEventListener('focusin', e => {
    if (e.target.classList.contains('is-invalid')) {
        e.target.classList.remove('is-invalid');
        if (e.target.nextElementSibling?.classList.contains('invalid-feedback')) e.target.nextElementSibling.remove();
    }
});

window.toggleSidebar = () => {
    document.getElementById('sidebar')?.classList.toggle('sidebar-collapsed');
    document.querySelector('.app-wrapper')?.classList.toggle('sidebar-open');
};

document.addEventListener('DOMContentLoaded', () => {
    const flash = document.getElementById('flashMsg');
    if (flash) setTimeout(() => { flash.style.opacity='0'; flash.style.transform='translateY(-10px)'; setTimeout(()=>flash.remove(),300); }, 5000);
});

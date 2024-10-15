
export function showAdminNotice(message, type = 'success') {
    const notice = document.createElement('div');
    notice.className = `notice notice-${type} is-dismissible`;
    notice.innerHTML = `<p>${message}</p>`;

    const noticesContainer = document.querySelector('.admin-notices');
    if (noticesContainer) {
        noticesContainer.prepend(notice);
    }

    setTimeout(() => {
        notice.style.transition = 'opacity 0.5s';
        notice.style.opacity = '0';
        setTimeout(() => {
            if (notice.parentNode) {
                notice.parentNode.removeChild(notice);
            }
        }, 500);
    }, 5000);
}
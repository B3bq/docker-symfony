import './styles/app.css';

document.addEventListener('DOMContentLoaded', function() {    
    // helper to escape HTML
    function escapeHtml(unsafe) {
        return String(unsafe || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    let selectedRow = null;
    const userSurveysList = document.getElementById('user-surveys-list');
    document.querySelectorAll('tr.trs').forEach(tr => {
        tr.addEventListener('click', async function() {
            if (selectedRow) selectedRow.classList.remove('table-active');
            selectedRow = this;
            selectedRow.classList.add('table-active');

            const userId = this.dataset.userId;
            if (!userId || !userSurveysList) return;

            userSurveysList.innerHTML = '<p class="text-muted">Ładowanie...</p>';
            try {
                const res = await fetch(`/adminpanel/user-surveys/${userId}`);
                if (!res.ok) {
                    userSurveysList.innerHTML = '<p class="text-danger">Błąd pobierania ankiet.</p>';
                    return;
                }
                const data = await res.json();
                if (!data.length) {
                    userSurveysList.innerHTML = '<p class="text-muted">Brak ankiet tego użytkownika.</p>';
                    return;
                }

                const html = data.map(s => `
                    <div class="card mb-2">
                        <div class="card-body p-2">
                            <h5 class="mb-1">${escapeHtml(s.title)}</h5>
                            <p class="mb-0 text-muted">${escapeHtml(s.description || '')}</p>
                        </div>
                    </div>
                `).join('');
                userSurveysList.innerHTML = html;
            } catch (e) {
                userSurveysList.innerHTML = '<p class="text-danger">Błąd sieci.</p>';
            }
        });
    });
});

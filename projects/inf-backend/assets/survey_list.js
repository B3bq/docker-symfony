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

                const html = `
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:30%">Tytuł ankiety</th>
                                <th style="width:50%">Opis</th>
                                <th style="width:15%">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.map((s, idx) => `
                                <tr>
                                    <td>${idx + 1}</td>
                                    <td>${escapeHtml(s.title)}</td>
                                    <td>${escapeHtml(s.description || '')}</td>
                                    <td>
                                        <a href="/survey/${s.id}/preview" class="btn btn-sm btn-primary me-2">
                                            <button>Podejrzyj</button>
                                        </a>
                                        <form action="/adminpanel/delete-survey/${s.id}" method="post" style="display:inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć tę ankietę? Ta operacja usunie ją wraz z pytaniami i odpowiedziami.');">
                                            <button type="submit" class="btn btn-sm btn-danger">Usuń</button>
                                        </form>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
                userSurveysList.innerHTML = html;
            } catch (e) {
                userSurveysList.innerHTML = '<p class="text-danger">Błąd sieci.</p>';
            }
        });
    });
});

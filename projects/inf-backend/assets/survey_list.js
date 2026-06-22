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


document.addEventListener('DOMContentLoaded', function(){
    (function(){
        function qs(selector, el=document){return el.querySelector(selector)}
        function qsa(selector, el=document){return Array.from(el.querySelectorAll(selector))}

        document.querySelectorAll('.edit-roles').forEach(function(btn){
            btn.addEventListener('click', function(e){
                var tr = e.target.closest('tr');
                tr.querySelector('.role-editor').style.display = 'block';
                tr.querySelectorAll('.remove-role').forEach(b=>b.style.display='inline-block');
                btn.style.display = 'none';
            });
        });

        document.addEventListener('click', function(e){
            if (e.target.matches('.add-role')) {
                var tr = e.target.closest('tr');
                var select = tr.querySelector('.role-select');
                var val = select.value;
                if (!val) return;
                var rolesList = tr.querySelector('.roles-list');
                if (rolesList.querySelector('[data-role="'+val+'"]')) return;
                var span = document.createElement('span');
                span.className = 'badge bg-success me-1 role-badge';
                span.setAttribute('data-role', val);
                span.innerText = val + ' ';
                var btn = document.createElement('button');
                btn.type='button'; btn.className='btn-close remove-role ms-2'; btn.setAttribute('aria-label','Usuń'); btn.style.display='inline-block'; btn.setAttribute('data-role', val);
                btn.textContent = 'X';
                span.appendChild(btn);
                rolesList.appendChild(span);
            }

            if (e.target.matches('.remove-role')) {
                var role = e.target.getAttribute('data-role');
                var badge = e.target.closest('.role-badge');
                if (badge) badge.remove();
            }

            if (e.target.matches('.confirm-roles')) {
                var tr = e.target.closest('tr');
                var updateUrl = tr.getAttribute('data-update-url');
                var badges = tr.querySelectorAll('.role-badge');
                var roles = [];
                badges.forEach(function(b){ roles.push(b.getAttribute('data-role')); });

                if (!updateUrl) {
                    console.error('No update URL on row');
                    return;
                }

                fetch(updateUrl, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({roles: roles})
                }).then(r=>r.json()).then(function(res){
                    if (res.success) {
                        tr.querySelector('.role-editor').style.display='none';
                        tr.querySelector('.edit-roles').style.display='inline-block';
                        tr.querySelectorAll('.remove-role').forEach(b=>b.style.display='none');
                    } else {
                        alert('Błąd podczas zapisu roli');
                    }
                }).catch(function(){ alert('Błąd sieci'); });
            }

            if (e.target.matches('.cancel-edit')) {
                var tr = e.target.closest('tr');
                tr.querySelector('.role-editor').style.display='none';
                tr.querySelector('.edit-roles').style.display='inline-block';
                tr.querySelectorAll('.remove-role').forEach(b=>b.style.display='none');
                window.location.reload();
            }
        });
    })();
});
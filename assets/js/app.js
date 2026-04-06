// ============================================================
// APP.JS — TopoGest — Funções de interface
// ============================================================

// --- Sidebar toggle (mobile) --------------------------------
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}

// Fechar sidebar ao clicar fora (mobile)
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('menuToggle');
    if (sidebar && toggle && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
    }
});

// --- Tabs ---------------------------------------------------
function initTabs() {
    const tabBtns   = document.querySelectorAll('.os-tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.tab;

            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));

            btn.classList.add('active');
            const panel = document.getElementById('tab-' + target);
            if (panel) panel.classList.add('active');

            // Salvar aba ativa na URL
            history.replaceState(null, '', '#tab-' + target);
        });
    });

    // Restaurar aba por hash
    const hash = window.location.hash;
    if (hash) {
        const tabId = hash.replace('#tab-', '');
        const btn   = document.querySelector(`.os-tab-btn[data-tab="${tabId}"]`);
        if (btn) btn.click();
    }
}

// --- Modais -------------------------------------------------
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('open');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('open');
}

// Fechar modal ao clicar no overlay
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('open');
    }
});

// Fechar modal com Esc
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
    }
});

// --- Adicionar membro de equipe (OS Detail) -----------------
let equipeCounter = 100;

function addEquipeMember() {
    const nome   = document.getElementById('eq_nome')?.value?.trim();
    const funcao = document.getElementById('eq_funcao')?.value?.trim();
    const horas  = parseFloat(document.getElementById('eq_horas')?.value) || 0;
    const custo  = parseFloat(document.getElementById('eq_custo')?.value) || 0;

    if (!nome || !funcao) {
        alert('Preencha nome e função do membro.');
        return;
    }

    const total = horas * custo;
    const initials = nome.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

    const list = document.getElementById('equipe-list');
    if (!list) return;

    const card = document.createElement('div');
    card.className = 'team-card';
    card.id = 'eq-' + (++equipeCounter);
    card.innerHTML = `
        <div class="team-avatar">${initials}</div>
        <div>
            <div class="team-name">${escHtml(nome)}</div>
            <div class="team-role">${escHtml(funcao)}</div>
        </div>
        <div class="team-stats">
            <div>
                <span class="team-stat-val">${horas}h</span>
                <span class="team-stat-label">Horas</span>
            </div>
            <div>
                <span class="team-stat-val">R$ ${custo.toFixed(2).replace('.',',')}/h</span>
                <span class="team-stat-label">Custo/h</span>
            </div>
            <div>
                <span class="team-stat-val">R$ ${total.toFixed(2).replace('.',',')}</span>
                <span class="team-stat-label">Total</span>
            </div>
        </div>
        <button class="btn-icon" onclick="removeEl('eq-${equipeCounter}')" title="Remover">✕</button>
    `;
    list.appendChild(card);

    // Atualizar total
    recalcEquipeTotal();
    updateTabCount('equipe', list.querySelectorAll('.team-card').length);

    // Limpar form e fechar modal
    ['eq_nome', 'eq_funcao', 'eq_horas', 'eq_custo'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    closeModal('modal-equipe');
}

function recalcEquipeTotal() {
    const cards = document.querySelectorAll('#equipe-list .team-card');
    let total = 0;
    cards.forEach(card => {
        const vals = card.querySelectorAll('.team-stat-val');
        if (vals.length >= 3) {
            const raw = vals[2].textContent.replace('R$ ', '').replace(',', '.');
            total += parseFloat(raw) || 0;
        }
    });
    const el = document.getElementById('equipe-total');
    if (el) el.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    updateFinanceCalc();
}

// --- Adicionar custo (OS Detail) ----------------------------
let custoCounter = 100;

function addCusto() {
    const tipo  = document.getElementById('custo_tipo')?.value;
    const desc  = document.getElementById('custo_desc')?.value?.trim();
    const valor = parseFloat(document.getElementById('custo_valor')?.value) || 0;
    const data  = document.getElementById('custo_data')?.value || '';

    if (!desc || !valor) {
        alert('Preencha descrição e valor do custo.');
        return;
    }

    const tipoLabel = {
        combustivel: 'Combustível', alimentacao: 'Alimentação',
        equipamento: 'Equipamento', outros: 'Outros'
    };
    const tipoIcon = { combustivel: '⛽', alimentacao: '🍽', equipamento: '📡', outros: '📎' };
    const dataFmt = data ? new Date(data + 'T00:00:00').toLocaleDateString('pt-BR') : '—';

    const list = document.getElementById('custos-list');
    if (!list) return;

    const item = document.createElement('div');
    item.className = 'cost-item';
    item.id = 'custo-' + (++custoCounter);
    item.innerHTML = `
        <div class="cost-icon ${tipo}">${tipoIcon[tipo] || '📎'}</div>
        <div>
            <div class="cost-desc">${escHtml(desc)}</div>
            <div class="cost-date">${dataFmt}</div>
        </div>
        <span class="cost-tipo-tag">${tipoLabel[tipo] || tipo}</span>
        <div class="cost-value">R$ ${valor.toFixed(2).replace('.', ',')}</div>
        <button class="btn-icon" onclick="removeEl('custo-${custoCounter}')" title="Remover">✕</button>
    `;
    list.appendChild(item);

    recalcCustosTotal();
    updateTabCount('custos', list.querySelectorAll('.cost-item').length);

    ['custo_tipo', 'custo_desc', 'custo_valor', 'custo_data'].forEach(id => {
        const el = document.getElementById(id);
        if (el && el.tagName !== 'SELECT') el.value = '';
    });
    closeModal('modal-custo');
}

function recalcCustosTotal() {
    const items = document.querySelectorAll('#custos-list .cost-item');
    let total = 0;
    items.forEach(item => {
        const valEl = item.querySelector('.cost-value');
        if (valEl) {
            const raw = valEl.textContent.replace('R$ ', '').replace(',', '.');
            total += parseFloat(raw) || 0;
        }
    });
    const el = document.getElementById('custos-total');
    if (el) el.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    updateFinanceCalc();
}

// --- Adicionar lançamento financeiro ------------------------
function addLancamento() {
    const tipo  = document.getElementById('fin_tipo')?.value;
    const desc  = document.getElementById('fin_desc')?.value?.trim();
    const valor = parseFloat(document.getElementById('fin_valor')?.value) || 0;
    const data  = document.getElementById('fin_data')?.value || '';

    if (!desc || !valor) {
        alert('Preencha descrição e valor.');
        return;
    }

    const dataFmt = data ? new Date(data + 'T00:00:00').toLocaleDateString('pt-BR') : '—';
    const list = document.getElementById('financeiro-list');
    if (!list) return;

    const row = document.createElement('tr');
    const colorClass = tipo === 'receita' ? 'finance-green' : 'finance-red';
    const sinal = tipo === 'receita' ? '+' : '-';
    row.innerHTML = `
        <td>${dataFmt}</td>
        <td><span class="badge ${tipo === 'receita' ? 'badge-done' : 'badge-danger'}">${tipo === 'receita' ? 'Receita' : 'Despesa'}</span></td>
        <td>${escHtml(desc)}</td>
        <td class="${colorClass} text-bold" style="font-family:var(--font-mono)">${sinal} R$ ${valor.toFixed(2).replace('.', ',')}</td>
    `;
    list.appendChild(row);

    recalcFinanceiro();
    ['fin_desc', 'fin_valor', 'fin_data'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    closeModal('modal-lancamento');
}

function recalcFinanceiro() {
    updateFinanceCalc();
}

// --- Recalc painel financeiro (OS Detail) -------------------
function updateFinanceCalc() {
    const propValEl = document.getElementById('prop-valor');
    const propVal = propValEl ? parseFloat(propValEl.dataset.valor || 0) : 0;

    // Custos equipe
    let custEquipe = 0;
    document.querySelectorAll('#equipe-list .team-card').forEach(card => {
        const vals = card.querySelectorAll('.team-stat-val');
        if (vals.length >= 3) {
            const raw = vals[2].textContent.replace('R$ ', '').replace(',', '.');
            custEquipe += parseFloat(raw) || 0;
        }
    });

    // Custos diretos
    let custDireto = 0;
    document.querySelectorAll('#custos-list .cost-item .cost-value').forEach(el => {
        const raw = el.textContent.replace('R$ ', '').replace(',', '.');
        custDireto += parseFloat(raw) || 0;
    });

    const totalCusto = custEquipe + custDireto;
    const lucro = propVal - totalCusto;
    const margem = propVal > 0 ? (lucro / propVal * 100).toFixed(1) : 0;

    setFinCard('fin-card-custo', totalCusto);
    setFinCard('fin-card-lucro', lucro, true);
    setFinCard('fin-card-margem', null, false, margem + '%');
}

function setFinCard(id, val, color = false, text = null) {
    const el = document.getElementById(id);
    if (!el) return;
    if (text !== null) { el.textContent = text; return; }
    el.textContent = 'R$ ' + Math.abs(val).toFixed(2).replace('.', ',');
    if (color) {
        el.classList.remove('finance-green', 'finance-red');
        el.classList.add(val >= 0 ? 'finance-green' : 'finance-red');
    }
}

// --- Simulação upload --------------------------------------- 
function simulateUpload() {
    const input = document.getElementById('file-input');
    if (!input || !input.files || !input.files.length) {
        alert('Selecione um arquivo para enviar.');
        return;
    }
    const file = input.files[0];
    const ext  = file.name.split('.').pop().toLowerCase();
    const tipo = ['jpg','jpeg','png','gif','webp'].includes(ext) ? 'imagem' :
                  ext === 'pdf' ? 'pdf' : ext === 'dwg' ? 'dwg' : 'outros';
    const icons = { pdf: '📄', imagem: '🖼', dwg: '📐', outros: '📎' };
    const tags  = { pdf: 'tag-pdf', imagem: 'tag-img', dwg: 'tag-dwg', outros: '' };
    const size  = (file.size / 1024 / 1024).toFixed(1) + ' MB';
    const grid  = document.getElementById('files-grid');

    if (!grid) return;
    const card = document.createElement('div');
    card.className = 'file-card';
    card.innerHTML = `
        <div class="file-icon">${icons[tipo]}</div>
        <div class="file-name">${escHtml(file.name)}</div>
        <div class="file-meta">
            <span class="file-size">${size}</span>
            <span class="${tags[tipo]}">${tipo.toUpperCase()}</span>
        </div>
    `;
    grid.appendChild(card);

    // Reset input
    input.value = '';
    closeModal('modal-upload');
    updateTabCount('anexos', grid.querySelectorAll('.file-card').length);
}

// --- Helpers ------------------------------------------------
function removeEl(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
    recalcEquipeTotal();
    recalcCustosTotal();
}

function updateTabCount(tab, count) {
    const btn = document.querySelector(`.os-tab-btn[data-tab="${tab}"] .tab-count`);
    if (btn) btn.textContent = count;
}

function escHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function confirmAction(msg) {
    return confirm(msg || 'Tem certeza?');
}

// --- Init ---------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    // Pequena animação de entrada nos cards
    document.querySelectorAll('.card, .stat-card').forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(8px)';
        setTimeout(() => {
            el.style.transition = 'opacity .2s ease, transform .2s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 50 + i * 30);
    });
});

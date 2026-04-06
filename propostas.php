<?php
require_once 'includes/auth_check.php';
$leads = db_where_raw('leads',"etapa NOT IN ('perdido','fechado')", [], 'nome ASC');
include 'includes/topnav.php';
?>
<div class="page-header">
  <div class="page-header-left"><h1>Propostas</h1><p>Gestão de propostas técnicas e comerciais</p></div>
  <div style="display:flex;gap:10px">
    <div class="filter-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="propSearch" placeholder="Buscar proposta..."></div>
    <select class="filter-select" id="filterStProp" onchange="loadPropostas()">
      <option value="">Todos</option><option value="rascunho">Rascunho</option><option value="enviada">Enviada</option>
      <option value="em_negociacao">Em Negociação</option><option value="aprovada">Aprovada</option>
      <option value="rejeitada">Rejeitada</option><option value="cancelada">Cancelada</option>
    </select>
    <button class="btn btn-primary" onclick="novaProposta()"><i class="fa-solid fa-plus"></i> Nova Proposta</button>
  </div>
</div>

<div class="kpi-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:24px">
  <div class="kpi-card" style="--kpi-color:var(--ts-blue);--kpi-soft:var(--blue-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-file-alt"></i></div><div><div class="kpi-label">Total</div><div class="kpi-value" id="kpiPropTotal">–</div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--orange);--kpi-soft:var(--orange-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-paper-plane"></i></div><div><div class="kpi-label">Enviadas</div><div class="kpi-value" id="kpiPropEnv">–</div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--green);--kpi-soft:var(--green-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-circle-check"></i></div><div><div class="kpi-label">Aprovadas</div><div class="kpi-value" id="kpiPropAprov">–</div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--ts-gold);--kpi-soft:var(--ts-gold-lt)"><div class="kpi-icon-wrap"><i class="fa-solid fa-sack-dollar"></i></div><div><div class="kpi-label">Valor Aprovado</div><div class="kpi-value" id="kpiPropValor" style="font-size:15px">–</div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--purple);--kpi-soft:var(--purple-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-percent"></i></div><div><div class="kpi-label">Conversão</div><div class="kpi-value" id="kpiPropTaxa">–</div></div></div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title"><i class="fa-solid fa-file-contract"></i> Lista de Propostas</div></div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Número</th><th>Título</th><th>Cliente</th><th>Valor</th><th>Status</th><th>Criado por</th><th>Data</th><th>Ações</th></tr></thead>
      <tbody id="tbodyProp"><tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text3)"><i class="fa-solid fa-spinner fa-spin"></i></td></tr></tbody>
    </table>
  </div>
</div>

<!-- ═══ MODAL NOVA PROPOSTA ════════════════════════════ -->
<div class="modal-overlay" id="modalProp" style="display:none">
<div class="modal-box" style="max-width:960px">
  <div class="modal-header">
    <div class="modal-title"><i class="fa-solid fa-file-contract" style="color:var(--ts-gold)"></i> Nova Proposta Técnica</div>
    <button class="modal-close" onclick="closeModal('modalProp')"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="modal-body" style="max-height:80vh;padding:20px 24px">
    <div id="propTabs">
      <div class="tabs">
        <button class="tab-btn active" data-tab="tabP1"><i class="fa-solid fa-building"></i> 1. Cliente</button>
        <button class="tab-btn" data-tab="tabP2"><i class="fa-solid fa-bullseye"></i> 2. Objetivos</button>
        <button class="tab-btn" data-tab="tabP3"><i class="fa-solid fa-flask"></i> 3. Metodologia</button>
        <button class="tab-btn" data-tab="tabP4"><i class="fa-solid fa-calendar-alt"></i> 4. Cronograma</button>
        <button class="tab-btn" data-tab="tabP5"><i class="fa-solid fa-users"></i> 5. Equipe</button>
        <button class="tab-btn" data-tab="tabP6"><i class="fa-solid fa-coins"></i> 6. Investimento</button>
        <button class="tab-btn" data-tab="tabP7"><i class="fa-solid fa-images"></i> 7. Fotos</button>
      </div>

      <!-- ABA 1: CLIENTE -->
      <div class="tab-pane active" id="tabP1">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Título da Proposta *</label><input type="text" class="form-control" id="propTitulo" placeholder="Ex: Acompanhamento de Supressão Vegetal e Monitoramento de Fauna"></div>
          <div class="form-group"><label class="form-label">Tipo de Serviço</label><input type="text" class="form-control" id="propServico" placeholder="Ex: Monitoramento Ambiental"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Lead Vinculado</label>
            <select class="form-control" id="propLeadId" onchange="preencherLead()">
              <option value="">Nenhum</option>
              <?php foreach($leads as $l): ?><option value="<?= $l['id'] ?>" data-nome="<?= htmlspecialchars($l['empresa_nome']??$l['nome']) ?>"><?= htmlspecialchars($l['nome'].' — '.($l['empresa_nome']??'')) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Status</label>
            <select class="form-control" id="propStatus"><option value="rascunho">Rascunho</option><option value="enviada">Enviada</option></select>
          </div>
        </div>
        <div class="form-section"><div class="form-section-title"><i class="fa-solid fa-building"></i> Dados do Cliente</div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Unidade de Negócio / Razão Social *</label><input type="text" class="form-control" id="propClienteNome" placeholder="Ex: MINERACAO VALE VERDE DO BRASIL LTDA"></div>
            <div class="form-group"><label class="form-label">CNPJ / CPF</label><input type="text" class="form-control" id="propClienteCnpj" data-mask="cnpj" placeholder="00.000.000/0000-00"></div>
          </div>
          <div class="form-group"><label class="form-label">Endereço da Área / Empreendimento</label><input type="text" class="form-control" id="propClienteEnd" placeholder="Ex: ROD AL-486, S/N, KM 4, CEP 57.320-000, CARAÍBAS/AL"></div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Contato / Responsável</label><input type="text" class="form-control" id="propClienteContato" placeholder="Nome e cargo do responsável"></div>
            <div class="form-group"><label class="form-label">Local / Cidade de Execução</label><input type="text" class="form-control" id="propLocalExec" value="Fortaleza/CE"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Data da Proposta</label><input type="date" class="form-control" id="propDataProp"></div>
            <div class="form-group"><label class="form-label">Validade da Proposta</label><input type="date" class="form-control" id="propValidade"></div>
          </div>
        </div>
      </div>

      <!-- ABA 2: OBJETIVOS -->
      <div class="tab-pane" id="tabP2">
        <div class="form-group">
          <label class="form-label">Introdução <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--text3)">(aparece como seção 2. Introdução no PDF)</span></label>
          <textarea class="form-control" id="propIntroducao" rows="5" placeholder="Ex: A Terra System Geologia e Meio Ambiente, sediada em Fortaleza/CE, apresenta a presente proposta técnica para execução dos serviços de acompanhamento de supressão de vegetação, monitoramento de fauna e monitoramento de avifauna..."></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Objetivo Geral <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--text3)">(seção 3. Objetivo)</span></label>
          <textarea class="form-control" id="propObjetivo" rows="4" placeholder="Ex: Executar, sob demanda, serviços técnicos especializados em meio biótico, incluindo acompanhamento de supressão de vegetação, monitoramento de fauna terrestre e monitoramento de avifauna..."></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Objetivos Específicos <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--text3)">(um por linha → bullet points no PDF)</span></label>
          <textarea class="form-control" id="propObjEsp" rows="6" placeholder="Realizar o manejo, resgate e destinação adequada da fauna e flora durante as atividades de supressão vegetal.&#10;Monitorar, de forma sistemática, grupos faunísticos terrestres (herpetofauna, mastofauna, ictiofauna, quiropterofauna) e a avifauna local, considerando variações sazonais.&#10;Consolidar, interpretar e apresentar os dados em relatórios técnicos georreferenciados, compatíveis com os formatos exigidos pelo GBIF e SISBIO.&#10;Garantir a conformidade dos estudos e relatórios às normas do IBAMA, IMA/AL e demais órgãos ambientais competentes."></textarea>
        </div>
      </div>

      <!-- ABA 3: METODOLOGIA -->
      <div class="tab-pane" id="tabP3">
        <div style="background:var(--blue-soft);padding:10px 14px;border-radius:8px;border-left:3px solid var(--ts-blue);margin-bottom:16px;font-size:12.5px;color:var(--ts-blue)">
          <i class="fa-solid fa-info-circle"></i> Cada escopo vira uma subseção <strong>4.x</strong> no PDF. Os itens da descrição (um por linha) viram bullets •.
        </div>
        <div id="metodBlocos"></div>
        <button class="btn btn-secondary btn-sm" onclick="addMetodBloco()"><i class="fa-solid fa-plus"></i> Adicionar Escopo / Método</button>
        <div style="margin-top:20px;border-top:1px solid var(--border);padding-top:16px">
          <div class="form-label" style="margin-bottom:10px">Etapas Para a Realização do Serviço <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--text3)">(seção 4.4)</span></div>
          <div id="etapasBlocos"></div>
          <button class="btn btn-secondary btn-sm" onclick="addEtapaBloco()"><i class="fa-solid fa-plus"></i> Adicionar Etapa</button>
        </div>
      </div>

      <!-- ABA 4: CRONOGRAMA -->
      <div class="tab-pane" id="tabP4">
        <div style="background:var(--blue-soft);padding:10px 14px;border-radius:8px;border-left:3px solid var(--ts-blue);margin-bottom:16px;font-size:12.5px;color:var(--ts-blue)">
          <i class="fa-solid fa-info-circle"></i> Tabela de cronograma — seção <strong>5. Cronograma</strong> no PDF.
        </div>
        <div id="cronoBlocos"></div>
        <button class="btn btn-secondary btn-sm" onclick="addCronoLinha()" style="margin-bottom:16px"><i class="fa-solid fa-plus"></i> Adicionar Linha</button>
        <div class="form-group">
          <label class="form-label">Prazo Total do Contrato</label>
          <input type="text" class="form-control" id="propPrazo" placeholder="Ex: 36 meses">
        </div>
      </div>

      <!-- ABA 5: EQUIPE -->
      <div class="tab-pane" id="tabP5">
        <div class="form-group">
          <label class="form-label">Descrição da Equipe <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--text3)">(parágrafo introdutório — seção 6. Equipe Técnica)</span></label>
          <textarea class="form-control" id="propEquipeDesc" rows="4" placeholder="Ex: A execução dos serviços será conduzida por uma equipe multidisciplinar composta por profissionais com experiência comprovada em trabalhos de campo no bioma Caatinga e ecossistemas associados. Os especialistas possuem formação acadêmica de nível superior e pós-graduação nas áreas de Biologia, Medicina Veterinária, Engenharia Florestal e áreas correlatas, todos devidamente registrados em seus respectivos Conselhos de Classe (CRBio, CRMV, CREA)."></textarea>
        </div>
        <div class="form-label" style="margin-bottom:10px">Membros da Equipe <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--text3)">(Cargo → Responsabilidade — bullets no PDF)</span></div>
        <div id="equipeBlocos"></div>
        <button class="btn btn-secondary btn-sm" onclick="addEquipeMembro()" style="margin-bottom:20px"><i class="fa-solid fa-plus"></i> Adicionar Membro</button>
        <div class="form-group" style="border-top:1px solid var(--border);padding-top:16px">
          <label class="form-label">7. Recursos e Logística</label>
          <textarea class="form-control" id="propLogistica" rows="4" placeholder="Ex: A proponente dispõe de infraestrutura própria e sede operacional em Fortaleza/CE, garantindo resposta rápida e controle direto sobre mobilização de pessoal e equipamentos...&#10;Os recursos e insumos previstos incluem:&#10;Equipamentos de captura (redes de neblina, armadilhas fotográficas, AIQ)&#10;Materiais para coleta e transporte de espécimes&#10;Equipamentos de georreferenciamento (GPS, drones)"></textarea>
          <div style="font-size:11px;color:var(--text3);margin-top:4px">Primeira linha = parágrafo introdutório. Demais linhas (a partir de "Os recursos...") = bullets •</div>
        </div>
        <div class="form-group">
          <label class="form-label">8. Garantia de Qualidade</label>
          <textarea class="form-control" id="propQualidade" rows="4" placeholder="Ex: O controle de qualidade será aplicado em todas as etapas...&#10;Padronização de procedimentos: aplicação rigorosa das metodologias descritas na Requisição Técnica&#10;Rastreabilidade de dados: todos os registros de campo serão georreferenciados&#10;Revisão técnica: cada relatório será submetido a dupla checagem por especialistas"></textarea>
          <div style="font-size:11px;color:var(--text3);margin-top:4px">Primeira linha = parágrafo introdutório. Demais linhas = bullets •</div>
        </div>
      </div>

      <!-- ABA 6: INVESTIMENTO -->
      <div class="tab-pane" id="tabP6">
        <div style="background:var(--blue-soft);padding:10px 14px;border-radius:8px;border-left:3px solid var(--ts-blue);margin-bottom:16px;font-size:12.5px;color:var(--ts-blue)">
          <i class="fa-solid fa-info-circle"></i> Organize os itens em <strong>grupos</strong> (Ex: Custos Administrativos, Escavação, Concretagem...). Cada grupo vira uma tabela separada no PDF.
        </div>
        <div id="gruposInvest"></div>
        <button class="btn btn-secondary btn-sm" onclick="addGrupoInvest()"><i class="fa-solid fa-plus"></i> Adicionar Grupo</button>
        <div style="display:none"><div id="itensProp"></div></div>
        <div style="background:var(--ts-blue-deep);color:#fff;border-radius:var(--radius);padding:16px 20px;margin-top:14px;display:flex;justify-content:space-between;align-items:center">
          <span style="font-family:'Syne',sans-serif;font-size:16px;font-weight:800">VALOR TOTAL</span>
          <span id="propTotal" style="font-family:'DM Mono',monospace;font-size:22px;font-weight:700;color:var(--ts-gold)">R$ 0,00</span>
        </div>
        <input type="hidden" id="propValorFinal">
        <div class="form-group" style="margin-top:18px">
          <label class="form-label">Condições de Pagamento <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--text3)">(bullets após a tabela)</span></label>
          <textarea class="form-control" id="propCondicoes" rows="3" placeholder="O pagamento poderá ser realizado via depósito em conta corrente, boleto bancário e/ou PIX.&#10;Dados Bancários: Banco INTER – 077, Agência 0001, Conta 29631673-3, CNPJ 50.822.206/0001-75&#10;Chave PIX: 50.822.206/0001-75 (CNPJ)"></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Obrigações das Partes <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--text3)">(seção 4 da última página — um por linha)</span></label>
          <textarea class="form-control" id="propObrigCont" rows="4" placeholder="Efetuar o pagamento dentro dos prazos estabelecidos.&#10;Oferecer informações necessárias para que a contratada possa executar os serviços acordados.&#10;Em caso de taxas Federais, Estaduais, Municipais ficam a cargo do CONTRATANTE.&#10;Executar os serviços contratados dentro do prazo previsto.&#10;Manter confidencialidade sobre as informações técnicas e estratégicas."></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Observações <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--text3)">(seção 5 da última página — um por linha)</span></label>
          <textarea class="form-control" id="propObs" rows="3" placeholder="A CONTRATADA prestará atendimento de toda informação de sua competência pelo e-mail contato@terrasystem.com.br.&#10;Documentos e informações para o andamento dos processos ficam a cargo do CONTRATANTE.&#10;A proposta contempla apenas os serviços nela descritos. Qualquer outro serviço deverá ser mediado por nova proposta.&#10;Esta proposta tem validade de 30 dias a partir da data de emissão."></textarea>
        </div>
      </div>

      <!-- ABA 7: FOTOS -->
      <div class="tab-pane" id="tabP7">
        <div style="background:var(--blue-soft);padding:10px 14px;border-radius:8px;border-left:3px solid var(--ts-blue);margin-bottom:16px;font-size:12.5px;color:var(--ts-blue)">
          <i class="fa-solid fa-info-circle"></i> Adicione fotos que aparecerão no PDF da proposta. Cada foto pode ter uma legenda. Arraste para reordenar.
        </div>

        <!-- Drop zone de upload -->
        <div id="fotosDropZone" style="border:2px dashed var(--border);border-radius:10px;padding:28px;text-align:center;cursor:pointer;transition:.2s;margin-bottom:16px"
          onclick="document.getElementById('fotosFileInput').click()"
          ondragover="event.preventDefault();document.getElementById('fotosDropZone').style.borderColor='var(--ts-blue)'"
          ondragleave="document.getElementById('fotosDropZone').style.borderColor='var(--border)'"
          ondrop="fotosHandleDrop(event)">
          <i class="fa-solid fa-camera" style="font-size:32px;color:var(--text3);display:block;margin-bottom:10px"></i>
          <div style="font-weight:600;font-size:14px;margin-bottom:4px">Clique ou arraste as fotos aqui</div>
          <div style="font-size:12px;color:var(--text3)">JPG, PNG, WEBP — máx. 5 MB por foto</div>
          <input type="file" id="fotosFileInput" accept="image/*" multiple style="display:none" onchange="fotosAdicionarArquivos(this.files)">
        </div>

        <!-- Grid de preview das fotos -->
        <div id="fotosGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px"></div>
        <div id="fotosVazio" style="text-align:center;color:var(--text3);padding:16px;font-size:13px;display:none">Nenhuma foto adicionada</div>
      </div>
    </div><!-- /propTabs -->
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('modalProp')">Cancelar</button>
    <button class="btn btn-gold" onclick="salvarProposta('rascunho')"><i class="fa-solid fa-floppy-disk"></i> Salvar Rascunho</button>
    <button class="btn btn-primary" onclick="salvarProposta('enviada')"><i class="fa-solid fa-paper-plane"></i> Salvar e Enviar</button>
  </div>
</div>
</div>

<!-- MODAL VER PROPOSTA -->
<div class="modal-overlay" id="modalViewProp" style="display:none">
<div class="modal-box" style="max-width:600px">
  <div class="modal-header">
    <div class="modal-title" id="viewPropTitle"><i class="fa-solid fa-file-contract" style="color:var(--ts-gold)"></i> Proposta</div>
    <div style="display:flex;gap:8px">
      <button class="btn btn-secondary btn-sm" id="btnPropPDF"><i class="fa-solid fa-file-pdf"></i> PDF</button>
      <button class="modal-close" onclick="closeModal('modalViewProp')"><i class="fa-solid fa-xmark"></i></button>
    </div>
  </div>
  <div class="modal-body" id="viewPropBody">Carregando...</div>
</div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
// Tabs
document.querySelectorAll('#propTabs .tab-btn').forEach(btn=>{
  btn.addEventListener('click',function(){
    document.querySelectorAll('#propTabs .tab-btn').forEach(b=>b.classList.remove('active'));
    document.querySelectorAll('#propTabs .tab-pane').forEach(p=>p.classList.remove('active'));
    this.classList.add('active');
    document.getElementById(this.dataset.tab).classList.add('active');
  });
});

// ── Grupos de investimento ─────────────────────────────
let propGrupos=[{nome:'',itens:[{desc:'',qtd:'1',valor:0}]}];
let propItens=[]; // mantido por compatibilidade

function renderGrupos(){
  document.getElementById('gruposInvest').innerHTML=propGrupos.map((g,gi)=>`
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:14px;margin-bottom:14px">
      <div style="display:flex;gap:10px;align-items:center;margin-bottom:12px">
        <input type="text" class="form-control" style="font-weight:600" placeholder="Nome do Grupo (Ex: CUSTOS ADMINISTRATIVOS E CANTEIRO DE OBRA)" value="${g.nome}" oninput="propGrupos[${gi}].nome=this.value">
        <button class="btn btn-danger btn-sm btn-icon" onclick="removerGrupo(${gi})"><i class="fa-solid fa-trash"></i></button>
      </div>
      <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:8px">
        <thead><tr style="background:var(--ts-blue);color:#fff"><th style="padding:6px 10px;width:40px">Item</th><th style="padding:6px 10px;text-align:left">Descrição</th><th style="padding:6px 10px;width:80px">Qtd</th><th style="padding:6px 10px;width:120px">Valor (R$)</th><th style="width:32px"></th></tr></thead>
        <tbody>${(g.itens||[]).map((item,ii)=>`
          <tr>
            <td style="padding:4px 6px;text-align:center">${ii+1}</td>
            <td style="padding:4px 6px"><input type="text" class="form-control" style="padding:4px 8px" value="${item.desc}" oninput="propGrupos[${gi}].itens[${ii}].desc=this.value" placeholder="Descrição do item"></td>
            <td style="padding:4px 6px"><input type="text" class="form-control" style="padding:4px 8px;text-align:center" value="${item.qtd}" oninput="propGrupos[${gi}].itens[${ii}].qtd=this.value"></td>
            <td style="padding:4px 6px"><input type="number" class="form-control" style="padding:4px 8px" step="0.01" value="${item.valor||''}" oninput="propGrupos[${gi}].itens[${ii}].valor=parseFloat(this.value)||0;calcTotal()"></td>
            <td style="padding:4px 6px"><button class="btn btn-danger btn-sm btn-icon" onclick="removerItemGrupo(${gi},${ii})"><i class="fa-solid fa-trash"></i></button></td>
          </tr>`).join('')}
        </tbody>
      </table>
      <button class="btn btn-secondary btn-sm" onclick="addItemGrupo(${gi})"><i class="fa-solid fa-plus"></i> Item</button>
    </div>`).join('');
  calcTotal();
}
function addGrupoInvest(){propGrupos.push({nome:'',itens:[{desc:'',qtd:'1',valor:0}]});renderGrupos();}
function removerGrupo(gi){propGrupos.splice(gi,1);if(!propGrupos.length)propGrupos=[{nome:'',itens:[{desc:'',qtd:'1',valor:0}]}];renderGrupos();}
function addItemGrupo(gi){propGrupos[gi].itens.push({desc:'',qtd:'1',valor:0});renderGrupos();}
function removerItemGrupo(gi,ii){propGrupos[gi].itens.splice(ii,1);renderGrupos();}
function addItemProp(){addItemGrupo(0);}  // compatibilidade
function renderItens(){renderGrupos();}   // compatibilidade
function calcTotal(){
  const t=propGrupos.reduce((s,g)=>s+(g.itens||[]).reduce((ss,i)=>ss+(parseFloat(i.valor)||0),0),0);
  document.getElementById('propTotal').textContent='R$ '+t.toLocaleString('pt-BR',{minimumFractionDigits:2});
  document.getElementById('propValorFinal').value=t;
}

// ── Metodologia ────────────────────────────────────────
let metodBlocos=[];
function renderMetodBlocos(){
  document.getElementById('metodBlocos').innerHTML=metodBlocos.map((b,i)=>`
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:14px;margin-bottom:12px">
      <div style="display:flex;gap:10px;align-items:center;margin-bottom:10px">
        <input type="text" class="form-control" style="font-weight:600" placeholder="Nome do Escopo (Ex: 4.1 Acompanhamento de Supressão de Vegetação — Escopo 01)" value="${b.titulo}" oninput="metodBlocos[${i}].titulo=this.value">
        <button class="btn btn-danger btn-sm btn-icon" onclick="metodBlocos.splice(${i},1);renderMetodBlocos()"><i class="fa-solid fa-trash"></i></button>
      </div>
      <textarea class="form-control" rows="5" placeholder="Cada linha vira um bullet • no PDF. Ex:&#10;Mobilização da equipe composta por biólogo, médico veterinário e auxiliares de campo, com veículo 4x4 equipado&#10;Atividades de resgate, salvamento, afugentamento e atendimento clínico da fauna&#10;Coleta e armazenamento de material propagativo (sementes, plântulas, mudas)" oninput="metodBlocos[${i}].itens=this.value">${b.itens}</textarea>
    </div>`).join('');
}
function addMetodBloco(){metodBlocos.push({titulo:'',itens:''});renderMetodBlocos();}

// ── Etapas ─────────────────────────────────────────────
let etapasBlocos=[];
function renderEtapas(){
  document.getElementById('etapasBlocos').innerHTML=etapasBlocos.map((e,i)=>`
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:10px">
      <div style="display:flex;gap:10px;align-items:center;margin-bottom:8px">
        <input type="text" class="form-control" style="max-width:200px;font-weight:600" placeholder="Ex: ETAPA 1 – Trabalho de Campo" value="${e.titulo}" oninput="etapasBlocos[${i}].titulo=this.value">
        <button class="btn btn-danger btn-sm btn-icon" onclick="etapasBlocos.splice(${i},1);renderEtapas()"><i class="fa-solid fa-trash"></i></button>
      </div>
      <textarea class="form-control" rows="3" placeholder="Descreva a etapa. Ex: Quarenta horas de trabalho de campo, para a realização do Levantamento da Fauna Silvestre por meio de dez metodologias distintas..." oninput="etapasBlocos[${i}].desc=this.value">${e.desc}</textarea>
    </div>`).join('');
}
function addEtapaBloco(){etapasBlocos.push({titulo:'',desc:''});renderEtapas();}

// ── Cronograma ─────────────────────────────────────────
let cronoLinhas=[];
function renderCrono(){
  document.getElementById('cronoBlocos').innerHTML=`
    <table style="width:100%;border-collapse:collapse;margin-bottom:10px;font-size:13px">
      <thead><tr style="background:var(--ts-blue);color:#fff">
        <th style="padding:8px 10px;width:55px">Etapa</th>
        <th style="padding:8px 10px">Atividade</th>
        <th style="padding:8px 10px;width:160px">Prazo Estimado</th>
        <th style="padding:8px 10px;width:32px"></th>
      </tr></thead>
      <tbody>${cronoLinhas.map((l,i)=>`
        <tr style="border-bottom:1px solid var(--border);background:${i%2?'var(--surface)':'#fff'}">
          <td style="padding:5px;width:55px"><input type="text" class="form-control" style="padding:4px 8px;text-align:center" value="${l.etapa}" oninput="cronoLinhas[${i}].etapa=this.value"></td>
          <td style="padding:5px"><input type="text" class="form-control" style="padding:4px 8px" value="${l.atividade}" oninput="cronoLinhas[${i}].atividade=this.value" placeholder="Ex: Mobilização de equipe e materiais"></td>
          <td style="padding:5px;width:160px"><input type="text" class="form-control" style="padding:4px 8px" value="${l.periodo||l.frequencia}" oninput="cronoLinhas[${i}].periodo=this.value" placeholder="Ex: Dia 1 a 2"></td>
          <td style="padding:5px;width:32px"><button class="btn btn-danger btn-sm btn-icon" onclick="cronoLinhas.splice(${i},1);renderCrono()"><i class="fa-solid fa-trash"></i></button></td>
        </tr>`).join('')}
      </tbody>
    </table>`;
}
function addCronoLinha(){cronoLinhas.push({etapa:String(cronoLinhas.length+1),atividade:'',frequencia:'',periodo:''});renderCrono();}

// ── Equipe ─────────────────────────────────────────────
let equipeMembers=[];
function renderEquipe(){
  document.getElementById('equipeBlocos').innerHTML=equipeMembers.map((m,i)=>`
    <div style="display:grid;grid-template-columns:220px 1fr 32px;gap:8px;margin-bottom:8px;align-items:center">
      <input type="text" class="form-control" placeholder="Cargo" value="${m.cargo}" oninput="equipeMembers[${i}].cargo=this.value">
      <input type="text" class="form-control" placeholder="Responsabilidade / Função" value="${m.funcao}" oninput="equipeMembers[${i}].funcao=this.value">
      <button class="btn btn-danger btn-sm btn-icon" onclick="equipeMembers.splice(${i},1);renderEquipe()"><i class="fa-solid fa-trash"></i></button>
    </div>`).join('');
}
function addEquipeMembro(){equipeMembers.push({cargo:'',funcao:''});renderEquipe();}

function preencherLead(){
  const opt=document.getElementById('propLeadId').selectedOptions[0];
  if(opt.value) document.getElementById('propClienteNome').value=opt.dataset.nome||'';
}

// ── Nova proposta com defaults ─────────────────────────
function novaProposta(){
  propItens=[]; propGrupos=[{nome:'',itens:[{desc:'',qtd:'1',valor:0}]}];
  metodBlocos=[];
  etapasBlocos=[
    {titulo:'ETAPA 1 – Trabalho de Campo',desc:'Quarenta horas de trabalho de campo, para a realização do Levantamento da Fauna Silvestre por meio de dez metodologias distintas, sendo 02 metodologias distintas para cada grupo faunístico (ictiofauna, herpetofauna, avifauna e mastofauna terrestre e alada), contendo a classificação taxonômica indicando a distribuição geográfica das espécies, casos de endemismo e status de conservação das espécies.\n\nTempo previsto para a realização da Etapa: 10 dias úteis, conforme termo de referência do IBAMA.'},
    {titulo:'ETAPA 2 – Elaboração do Relatório',desc:'Elaboração do relatório das atividades de campo contendo indicação dos métodos, técnicas e critérios para sua identificação, registro fotográfico, quantificação e interpretação, incluindo descrição e justificativa detalhadas das metodologias adotadas para cada grupo faunístico e seus respectivos indicadores estatísticos, com base em referências técnico-científicas.'},
  ];
  cronoLinhas=[];  // Inicia vazio para o usuário preencher conforme o tipo de proposta
  equipeMembers=[
    {cargo:'Coordenador Técnico',funcao:'responsável pela supervisão geral das atividades, validação de metodologias e entrega dos produtos finais'},
    {cargo:'Biólogos Especialistas',funcao:'responsáveis pelo levantamento, identificação e manejo da fauna e flora, bem como pela elaboração de relatórios'},
    {cargo:'Médico Veterinário de Fauna Silvestre',funcao:'responsável pelo manejo, atendimento clínico, avaliação sanitária e encaminhamento de espécimes'},
    {cargo:'Auxiliares de Campo',funcao:'capacitados para apoio em captura, transporte e instalação de equipamentos de monitoramento'},
    {cargo:'Técnicos em Geoprocessamento',funcao:'responsáveis pelo georreferenciamento e organização espacial dos dados obtidos'},
  ];
  ['propTitulo','propServico','propClienteNome','propClienteCnpj','propClienteEnd','propClienteContato',
   'propPrazo','propIntroducao','propObjetivo','propObjEsp','propEquipeDesc','propLogistica',
   'propQualidade','propCondicoes','propObrigCont','propObs'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
  document.getElementById('propLocalExec').value='Fortaleza/CE';
  document.getElementById('propDataProp').value=new Date().toISOString().slice(0,10);
  document.getElementById('propValidade').value='';
  document.getElementById('propLeadId').selectedIndex=0;
  document.getElementById('propStatus').value='rascunho';
  renderGrupos(); renderMetodBlocos(); renderEtapas(); renderCrono(); renderEquipe();
  propFotos = []; renderFotos();
  document.querySelectorAll('#propTabs .tab-btn').forEach((b,i)=>b.classList.toggle('active',i===0));
  document.querySelectorAll('#propTabs .tab-pane').forEach((p,i)=>p.classList.toggle('active',i===0));
  openModal('modalProp');
}

// ── Salvar ─────────────────────────────────────────────
async function salvarProposta(statusOverride){
  const titulo=document.getElementById('propTitulo').value.trim();
  const cliente=document.getElementById('propClienteNome').value.trim();
  if(!titulo){toast('Informe o título da proposta','error');return;}
  if(!cliente){toast('Informe o nome do cliente','error');return;}
  const valor=propItens.reduce((s,i)=>s+(parseFloat(i.valor)||0),0);
  const payload={
    action:'create_v2', titulo,
    status: statusOverride||document.getElementById('propStatus').value,
    servico:          document.getElementById('propServico').value,
    cliente_nome:     cliente,
    cliente_cnpj:     document.getElementById('propClienteCnpj').value,
    cliente_end:      document.getElementById('propClienteEnd').value,
    cliente_contato:  document.getElementById('propClienteContato').value,
    local_exec:       document.getElementById('propLocalExec').value||'Fortaleza/CE',
    data_prop:        document.getElementById('propDataProp').value,
    prazo:            document.getElementById('propPrazo').value,
    validade:         document.getElementById('propValidade').value||null,
    introducao:       document.getElementById('propIntroducao').value,
    objetivo:         document.getElementById('propObjetivo').value,
    objetivos_esp:    document.getElementById('propObjEsp').value,
    metodologias:     metodBlocos,
    etapas_exec:      etapasBlocos,
    cronograma:       cronoLinhas,
    equipe_desc:      document.getElementById('propEquipeDesc').value,
    equipe_membros:   equipeMembers,
    logistica:        document.getElementById('propLogistica').value,
    qualidade:        document.getElementById('propQualidade').value,
    condicoes:        document.getElementById('propCondicoes').value,
    obrig_contratante:document.getElementById('propObrigCont').value,
    observacoes:      document.getElementById('propObs').value,
    lead_id:          document.getElementById('propLeadId').value||null,
    valor, grupos:propGrupos,
    fotos: propFotos.map(f=>({legenda:f.legenda, base64:f.base64})),
  };
  const res=await api(BASE_URL+'/api/propostas.php',payload);
  if(res.success){toast(`Proposta ${res.numero} criada!`);closeModal('modalProp');loadPropostas();}
  else toast(res.error||'Erro','error');
}

// ── Lista ──────────────────────────────────────────────
async function loadPropostas(){
  const q=document.getElementById('propSearch').value.toLowerCase();
  const status=document.getElementById('filterStProp').value;
  const lista=await fetch(BASE_URL+'/api/propostas.php').then(r=>r.json());
  const filtered=lista.filter(p=>(!status||p.status===status)&&(!q||(p.titulo+p.numero+p.empresa_nome).toLowerCase().includes(q)));
  const total=lista.length,env=lista.filter(p=>p.status==='enviada').length,aprov=lista.filter(p=>p.status==='aprovada').length;
  const valAprov=lista.filter(p=>p.status==='aprovada').reduce((s,p)=>s+(parseFloat(p.valor)||0),0);
  document.getElementById('kpiPropTotal').textContent=total;
  document.getElementById('kpiPropEnv').textContent=env;
  document.getElementById('kpiPropAprov').textContent=aprov;
  document.getElementById('kpiPropValor').textContent='R$ '+valAprov.toLocaleString('pt-BR',{minimumFractionDigits:2});
  document.getElementById('kpiPropTaxa').textContent=(total>0?Math.round(aprov/total*100):0)+'%';
  const stBadge={rascunho:'badge-gray',enviada:'badge-orange',em_negociacao:'badge-yellow',aprovada:'badge-green',rejeitada:'badge-red',cancelada:'badge-red'};
  const stLabel={rascunho:'Rascunho',enviada:'Enviada',em_negociacao:'Em Negociação',aprovada:'Aprovada',rejeitada:'Rejeitada',cancelada:'Cancelada'};
  const tbody=document.getElementById('tbodyProp');
  if(!filtered.length){tbody.innerHTML='<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text3)"><i class="fa-solid fa-inbox fa-2x" style="display:block;margin-bottom:12px;opacity:.3"></i>Nenhuma proposta</td></tr>';return;}
  const fmtD=d=>d?d.slice(0,10).split('-').reverse().join('/'):'-';
  tbody.innerHTML=filtered.map(p=>`
    <tr>
      <td><span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--ts-blue);font-weight:700;cursor:pointer" onclick="viewProp(${p.id})">${p.numero}</span></td>
      <td><span style="font-weight:600;cursor:pointer" onclick="viewProp(${p.id})">${p.titulo.length>48?p.titulo.slice(0,48)+'…':p.titulo}</span></td>
      <td>${p.empresa_nome||'–'}</td>
      <td style="font-family:'DM Mono',monospace;color:var(--green);font-weight:700">R$ ${parseFloat(p.valor||0).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td>
      <td><span class="badge ${stBadge[p.status]||'badge-gray'}">${stLabel[p.status]||p.status}</span></td>
      <td>${p.user_nome||'–'}</td>
      <td>${fmtD(p.created_at)}</td>
      <td><div style="display:flex;gap:5px">
        <button class="btn btn-ghost btn-sm btn-icon" onclick="viewProp(${p.id})" title="Ver"><i class="fa-solid fa-eye"></i></button>
        <button class="btn btn-ghost btn-sm btn-icon" onclick="abrirPDFProp(${p.id})" title="PDF"><i class="fa-solid fa-file-pdf" style="color:#e53e3e"></i></button>
        <button class="btn btn-danger btn-sm btn-icon" onclick="deletarProp(${p.id})" title="Excluir"><i class="fa-solid fa-trash"></i></button>
      </div></td>
    </tr>`).join('');
}
document.getElementById('propSearch').addEventListener('input',loadPropostas);

async function viewProp(id){
  openModal('modalViewProp');
  document.getElementById('viewPropBody').innerHTML='<div style="text-align:center;padding:32px;color:var(--text3)"><i class="fa-solid fa-spinner fa-spin fa-2x"></i></div>';
  const p=await fetch(BASE_URL+'/api/propostas.php?id='+id).then(r=>r.json());
  const extra=p.extra_data?JSON.parse(p.extra_data):{};
  document.getElementById('viewPropTitle').innerHTML=`<i class="fa-solid fa-file-contract" style="color:var(--ts-gold)"></i> ${p.numero}`;
  document.getElementById('btnPropPDF').onclick=()=>abrirPDFProp(id);
  const stBadge={rascunho:'badge-gray',enviada:'badge-orange',em_negociacao:'badge-yellow',aprovada:'badge-green',rejeitada:'badge-red',cancelada:'badge-red'};
  const stLabel={rascunho:'Rascunho',enviada:'Enviada',em_negociacao:'Em Negociação',aprovada:'Aprovada',rejeitada:'Rejeitada',cancelada:'Cancelada'};
  const statusOpts=['rascunho','enviada','em_negociacao','aprovada','rejeitada','cancelada'].map(s=>`<option value="${s}" ${p.status===s?'selected':''}>${stLabel[s]}</option>`).join('');
  document.getElementById('viewPropBody').innerHTML=`
    <div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <div><div style="font-size:17px;font-weight:700">${p.titulo}</div><div style="color:var(--text2);font-size:13px">${extra.cliente_nome||p.empresa_nome||''}</div></div>
        <div style="font-family:'DM Mono',monospace;font-size:20px;font-weight:700;color:var(--green)">R$ ${parseFloat(p.valor||0).toLocaleString('pt-BR',{minimumFractionDigits:2})}</div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
        <div><div class="form-label">Status</div><span class="badge ${stBadge[p.status]||'badge-gray'}">${stLabel[p.status]||p.status}</span></div>
        <div><div class="form-label">Prazo</div><div style="font-size:13px">${p.prazo||'–'}</div></div>
        <div><div class="form-label">Serviço</div><div style="font-size:13px">${p.servico||'–'}</div></div>
        <div><div class="form-label">Criado por</div><div style="font-size:13px">${p.user_nome||'–'}</div></div>
      </div>
      ${extra.objetivo?`<div style="padding:12px;background:var(--surface);border-radius:8px;font-size:13px;margin-bottom:16px;line-height:1.6"><strong>Objetivo:</strong><br>${extra.objetivo}</div>`:''}
      <div style="border-top:1px solid var(--border);padding-top:16px">
        <div class="form-label" style="margin-bottom:8px">Alterar Status</div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
          <select class="form-control" id="novoStatusProp" style="max-width:200px">${statusOpts}</select>
          <button class="btn btn-primary btn-sm" onclick="alterarStatusProp(${p.id})"><i class="fa-solid fa-check"></i> Confirmar</button>
          <button class="btn btn-gold btn-sm" onclick="abrirPDFProp(${p.id})"><i class="fa-solid fa-file-pdf"></i> Abrir PDF</button>
        </div>
      </div>
    </div>`;
}

async function alterarStatusProp(id){
  const status=document.getElementById('novoStatusProp').value;
  const res=await api(BASE_URL+'/api/propostas.php',{action:'update_status',id,status});
  if(res.success){toast('Status atualizado!'+(status==='aprovada'?' Receita gerada no Financeiro!':''));closeModal('modalViewProp');loadPropostas();}
  else toast(res.error||'Erro','error');
}
async function deletarProp(id){
  if(!confirm('Excluir esta proposta?'))return;
  const res=await api(BASE_URL+'/api/propostas.php',{action:'delete',id});
  if(res.success){toast('Excluída');loadPropostas();}
  else toast(res.error||'Erro','error');
}
function abrirPDFProp(id){window.open(BASE_URL+'/api/proposta_print.php?id='+id,'_blank');}

// ══ FOTOS ══════════════════════════════════════════════
let propFotos = [];

function fotosHandleDrop(e) {
  e.preventDefault();
  document.getElementById('fotosDropZone').style.borderColor = 'var(--border)';
  fotosAdicionarArquivos(e.dataTransfer.files);
}
async function fotosAdicionarArquivos(files) {
  for (const file of files) {
    if (!file.type.startsWith('image/')) continue;
    if (file.size > 5*1024*1024) { toast(`${file.name}: arquivo muito grande (máx 5 MB)`,'error'); continue; }
    const base64 = await new Promise((res,rej) => { const r=new FileReader(); r.onload=()=>res(r.result); r.onerror=rej; r.readAsDataURL(file); });
    propFotos.push({ nome: file.name, legenda: '', base64 });
  }
  renderFotos();
}
function renderFotos() {
  const grid = document.getElementById('fotosGrid');
  const vazio = document.getElementById('fotosVazio');
  if (!grid) return;
  if (!propFotos.length) { grid.innerHTML=''; if(vazio) vazio.style.display=''; return; }
  if (vazio) vazio.style.display = 'none';
  grid.innerHTML = propFotos.map((f, i) => `
    <div style="border:1px solid var(--border);border-radius:10px;overflow:hidden;background:var(--surface);position:relative">
      <img src="${f.base64}" style="width:100%;height:130px;object-fit:cover;display:block">
      <button onclick="removerFoto(${i})" style="position:absolute;top:6px;right:6px;background:rgba(0,0,0,.55);border:none;border-radius:50%;width:26px;height:26px;color:#fff;cursor:pointer;font-size:13px;line-height:1;display:flex;align-items:center;justify-content:center;z-index:2">✕</button>
      <div style="padding:8px 10px">
        <input type="text" class="form-control" style="padding:5px 8px;font-size:12px" placeholder="Legenda (opcional)"
          value="${f.legenda}" oninput="propFotos[${i}].legenda=this.value">
        <div style="font-size:10px;color:var(--text3);margin-top:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${f.nome}">${f.nome}</div>
      </div>
    </div>`).join('');
}
function removerFoto(i) { propFotos.splice(i,1); renderFotos(); }

renderGrupos();
loadPropostas();
</script>
<?php include 'includes/footer.php'; ?>

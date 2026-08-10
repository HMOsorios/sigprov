# Plano de Execução — SisProv

## Fase 1 — Fundação Técnica (Semanas 1-2)

### 1.1 Console / Scheduler
- [ ] Criar `app/Console/Kernel.php` com schedule
- [ ] `php artisan make:command GenerateInvoices` — geração em lote de faturas do mês
- [ ] `php artisan make:command CheckOverdueInvoices` — verifica vencidos e aplica régua
- [ ] `php artisan make:command AutoBlockOverdue` — bloqueia links de contratos inadimplentes
- [ ] `php artisan make:command AutoUnblockPaid` — desbloqueia links após confirmação
- [ ] `php artisan make:command SendCollectionNotifications` — dispara notificações da régua
- [ ] Agendar no `Kernel::schedule()`: execução diária dos comandos

### 1.2 Queue / Async
- [ ] Configurar `QUEUE_CONNECTION=database` no `.env`
- [ ] `php artisan queue:table` e `php artisan migrate`
- [ ] Criar `app/Jobs/GenerateInvoiceJob.php`
- [ ] Criar `app/Jobs/SendNotificationJob.php`
- [ ] Criar `app/Jobs/SyncRadiusJob.php`
- [ ] Criar `app/Jobs/ProcessPaymentWebhookJob.php`
- [ ] Configurar worker no Laragon / supervisor

### 1.3 Eventos e Listeners
- [ ] `php artisan make:event PaymentConfirmed`
- [ ] `php artisan make:event InvoiceGenerated`
- [ ] `php artisan make:event ContractSuspended`
- [ ] `php artisan make:event TicketCreated`
- [ ] `php artisan make:listener UnblockContractLinks` → escuta `PaymentConfirmed`
- [ ] `php artisan make:listener BlockContractLinks` → escuta `InvoiceOverdue`
- [ ] `php artisan make:listener SendTicketNotification` → escuta `TicketCreated`
- [ ] `php artisan make:listener LogPaymentActivity` → escuta `PaymentConfirmed`

### 1.4 API REST (Mobile + Integrações)
- [ ] `composer require laravel/sanctum`
- [ ] `php artisan install:api`
- [ ] Criar `routes/api.php` com grupos versionados (`api/v1/...`)
- [ ] Criar `app/Http/Controllers/Api/AuthController.php` (login, refresh, logout)
- [ ] Criar `app/Http/Controllers/Api/ClientController.php` (dados do cliente)
- [ ] Criar `app/Http/Controllers/Api/InvoiceController.php` (listar, baixar boleto)
- [ ] Criar `app/Http/Controllers/Api/TicketController.php` (abrir, responder)
- [ ] Criar `app/Http/Controllers/Api/WorkOrderController.php` (OS do técnico)
- [ ] Middleware `api.auth` com Sanctum token abilities por role
- [ ] Rate limiting por endpoint
- [ ] Testar com Postman / Insomnia

---

## Fase 2 — Automação Financeira (Semanas 3-5)

### 2.1 Integração com Gateway de Pagamento (Asaas)
- [ ] Criar `app/Services/PaymentGatewayService.php` (interface)
- [ ] Criar `app/Services/Gateways/AsaasGateway.php`
- [ ] Criar `app/Services/Gateways/MercadoPagoGateway.php`
- [ ] Config no `config/gateways.php`: api_key, environment, webhook_secret
- [ ] Métodos: `createPix()`, `createBoleto()`, `createCard()`, `getStatus()`, `cancel()`
- [ ] Rota webhook: `POST /api/webhooks/payment` → `PaymentWebhookController`
- [ ] Processar `PaymentConfirmed` e `PaymentFailed` no webhook
- [ ] Testar em sandbox

### 2.2 NF Telecom (Modelos 21/22)
- [ ] `composer require nfephp-org/sped-nfse` (ou similar)
- [ ] Criar `app/Services/NFService.php`
- [ ] Criar `database/migrations/xxxx_create_nf_telecom_table.php`
- [ ] Model `NfTelecom`: numero, serie, cliente, contrato, competencia, valor, xml, protocolo, status
- [ ] Controller `Admin\NFController.php`: emitir, cancelar, inutilizar, listar
- [ ] Integrar com schedule: emitir NF automaticamente ao gerar fatura
- [ ] Config `config/nfse.php`: certificado, ambiente, municipio, inscricao

### 2.3 Geração em Lote de Faturas
- [ ] Comando `GenerateInvoices`: 
  - [ ] Buscar contratos ativos com dia de vencimento = hoje
  - [ ] Criar faturas com itens (plano, adicionais, descontos)
  - [ ] Associar ao cliente e contrato
  - [ ] Disparar evento `InvoiceGenerated`
- [ ] Job `GenerateInvoiceJob` para processamento assíncrono
- [ ] Notificar cliente via e-mail/WhatsApp na geração

### 2.4 Régua de Cobrança
- [ ] Criar `app/Models/CollectionRule.php`: nome, dias_apos_vencimento, acao, canais, template
- [ ] Criar template de mensagens por canal em `config/collection.php`
- [ ] Criar `app/Services/CollectionService.php`
- [ ] Comando `SendCollectionNotifications`:
  - D+0: e-mail/SMS aviso vencimento
  - D+1: WhatsApp cobrança
  - D+3: e-mail 2º aviso com boleto atualizado
  - D+5: ligação automática (se houver integração)
  - D+7: bloqueio parcial (speed reduzido) — opcional
  - D+10: bloqueio total + notificação
- [ ] Registrar actions em `AuditLog`

### 2.5 Bloqueio/Desbloqueio Automático
- [ ] Comando `AutoBlockOverdue`:
  - [ ] Buscar faturas vencidas há X dias (configurável)
  - [ ] Suspend contract + block link
  - [ ] Disparar evento `ContractSuspended`
  - [ ] Notificar cliente
- [ ] Comando `AutoUnblockPaid`:
  - [ ] Buscar pagamentos confirmados (webhook ou retorno)
  - [ ] Reactivate contract + unblock link
  - [ ] Disparar evento `PaymentConfirmed`
  - [ ] Notificar cliente

---

## Fase 3 — Gestão de Estoque (Semanas 5-6)

### 3.1 Comodato / Equipamentos
- [ ] Criar migration `create_equipment_table`
- [ ] Model `Equipment`: serial, brand, model, type (router, onu, modem, cto, splitter, etc.), mac, status
- [ ] Criar migration `create_equipment_assignments_table`
- [ ] Model `EquipmentAssignment`: equipment_id, client_id, contract_id, assigned_at, returned_at, condition_in, condition_out, notes
- [ ] Controller `Admin\EquipmentController.php`: CRUD equipamentos
- [ ] Controller `Admin\AssignmentController.php`: vincular/desvincular equipamento a cliente
- [ ] View: listar equipamentos por cliente, histórico de movimentação
- [ ] Importação em lote via CSV

### 3.2 Almoxarifado / Materiais
- [ ] Criar migration `create_warehouse_items_table`
- [ ] Model `WarehouseItem`: sku, name, category, unit, unit_price, min_stock, current_qty
- [ ] Criar migration `create_warehouse_movements_table`
- [ ] Model `WarehouseMovement`: item_id, type (in/out), qty, reference_type, reference_id, responsible, notes
- [ ] Controller `Admin\WarehouseController.php`: CRUD + movimentação
- [ ] Controller `Admin\WarehouseReportController.php`: estoque baixo, valor total, giro
- [ ] View: dashboard de estoque com alertas de estoque mínimo

### 3.3 Fornecedores
- [ ] Criar migration `create_suppliers_table`
- [ ] Model `Supplier`: cnpj, name, contact, email, phone, address, category
- [ ] Controller `Admin\SupplierController.php`: CRUD
- [ ] Relacionar equipamentos e materiais a fornecedores

---

## Fase 4 — Operação de Campo (Semanas 7-9)

### 4.1 Ordem de Serviço
- [ ] Migration `create_work_orders_table`
- [ ] Model `WorkOrder`: numero, type (install, maintenance, repair, remove), client_id, contract_id, technician_id, priority, status, scheduled_at, started_at, finished_at, description, resolution, client_signature, photos
- [ ] Status: pending, scheduled, in_progress, completed, canceled
- [ ] Controller `Admin\WorkOrderController.php`: CRUD + agendamento
- [ ] Controller `Api\WorkOrderController.php`: técnico via API
- [ ] View admin: kanban/lista de OS, calendário de agendamento
- [ ] View admin: mapa com OS agendadas (lat/lng do contrato)

### 4.2 Agendamento Inteligente
- [ ] Criar `app/Services/SchedulingService.php`
- [ ] Algoritmo: agrupar OS por região (bairro/cidade), slot de horário, skills do técnico
- [ ] Sugerir agenda otimizada para o despachante
- [ ] Arrastar e soltar no calendário (JS)

### 4.3 App Técnico (API + Offline)
- [ ] Endpoints API já criados na Fase 1
- [ ] Endpoints específicos:
  - `GET /api/v1/tech/work-orders` — OS do dia
  - `GET /api/v1/tech/work-orders/{id}` — detalhes
  - `POST /api/v1/tech/work-orders/{id}/start` — iniciar
  - `POST /api/v1/tech/work-orders/{id}/complete` — finalizar (fotos + assinatura em base64)
  - `POST /api/v1/tech/work-orders/{id}/photo` — upload foto
  - `POST /api/v1/tech/work-orders/{id}/signature` — upload assinatura
  - `GET /api/v1/tech/equipment/{serial}` — consultar equipamento por serial
  - `POST /api/v1/tech/equipment/{serial}/assign` — vincular equipamento no local
- [ ] Suporte a sync offline (Last-Modified / If-Modified-Since)
- [ ] Criar `app/Http/Resources/` para padronizar responses

### 4.4 Check-in/Check-out de Equipamentos para Técnicos
- [ ] Migration `create_tech_inventory_table`
- [ ] Model `TechInventory`: technician_id, equipment_id, checked_out_at, checked_in_at
- [ ] Controller `Admin\TechInventoryController.php`
- [ ] Rota API para técnico consultar e registrar

---

## Fase 5 — Integração de Rede (Semanas 9-12)

### 5.1 MikroTik / RouterOS
- [ ] `composer require routeros-api/routeros-api`
- [ ] Criar `app/Services/RouterOsService.php`
- [ ] Métodos: `connect()`, `addPppoeUser()`, `removePppoeUser()`, `enableUser()`, `disableUser()`, `setQueue()`, `getQueue()`, `getResourceUsage()`
- [ ] Atrelar a `Server` (tipo router/mikrotik)
- [ ] Comando `SyncRadiusToMikrotik`: sincroniza PPPoE do BD para os roteadores
- [ ] Comando `ApplyBandwidthProfiles`: aplica filas/queues conforme plano
- [ ] Testar em MikroTik virtual (CHR/VirtualBox)

### 5.2 FreeRADIUS
- [ ] Criar `app/Services/RadiusService.php`
- [ ] Métodos: `addUser()`, `removeUser()`, `enableUser()`, `disableUser()`, `addGroup()`, `getAccounting()`
- [ ] Pode ser via API CoA (Change of Authorization) ou DB direto (se MySQL compartilhado)
- [ ] Comando `SyncToRadius`: sincroniza PPPoE com FreeRADIUS

### 5.3 Pools de IP
- [ ] Migration `create_ip_pools_table`
- [ ] Model `IpPool`: name, subnet (CIDR), gateway, dns1, dns2, range_start, range_end, type (ipv4, ipv6), used, total
- [ ] Migration `create_ip_assignments_table`
- [ ] Model `IpAssignment`: pool_id, link_id, ip_address, assigned_at, released_at
- [ ] Controller `Admin\IpPoolController.php`: CRUD + visualização de ocupação
- [ ] Auto-assign: ao criar link, pegar próximo IP disponível do pool
- [ ] Suporte a CGNAT: pools dedicados com flag `is_cgnat`

### 5.4 Monitoramento
- [ ] Criar `app/Services/MonitoringService.php`
- [ ] SNMP polling via `php-snmp` ou comando externo
- [ ] Comando `PingAllServers`: ping em lote e atualiza status
- [ ] Comando `PollOltOntSignals`: consulta OLT (SNMP ou SSH) e atualiza signal_rx/signal_tx dos links
- [ ] Salvar histórico em `link_logs` e `server_logs`
- [ ] Threshold de alerta: signal_rx < -27dBm → notificar

### 5.5 Mapa de Rede / FTTH
- [ ] Criar `app/Services/NetworkMapService.php`
- [ ] Model `NetworkElement`: type (olt, splitter, cto, drop, client), parent_id, coordinates, address
- [ ] Criar rotas para alimentar dados no frontend
- [ ] Integrar Leaflet.js ou Google Maps no admin
- [ ] Visualizar árvore óptica: OLT → splitter → CTO → cliente

---

## Fase 6 — CRM Avançado (Semanas 12-14)

### 6.1 Notificações Multicanal
- [ ] Criar `app/Notifications/` classes:
  - `InvoiceGeneratedNotification`
  - `PaymentConfirmedNotification`
  - `ContractSuspendedNotification`
  - `TicketUpdatedNotification`
  - `WorkOrderScheduledNotification`
- [ ] Canais:
  - **E-mail**: `mail` (Laravel Mail via SMTP)
  - **WhatsApp**: `App\Channels\WhatsAppChannel` (Evolution API / WATI / Z-API)
  - **SMS**: `App\Channels\SmsChannel` (Twilio / Zenvia)
- [ ] Preferências de canal por cliente em `Client.notification_preferences` JSON

### 6.2 Integração WhatsApp
- [ ] Criar `app/Services/WhatsAppService.php`
- [ ] Implementar para Evolution API (open source) ou WATI
- [ ] Métodos: `sendText()`, `sendTemplate()`, `sendDocument()`, `sendImage()`
- [ ] Webhook para receber mensagens → criar/atualizar ticket
- [ ] Filas por número (rate limiting do WhatsApp)

### 6.3 Assinatura Digital de Contratos
- [ ] Criar `app/Services/ContractSigningService.php`
- [ ] Integrar com Clicksign / Zapsign API
- [ ] Workflow:
  - Admin cria contrato → sistema gera PDF com dados
  - Envia para assinatura digital via API
  - Cliente recebe link por e-mail/WhatsApp
  - Webhook confirma assinatura
  - Contrato marcado como signed com timestamp
- [ ] Armazenar PDF assinado no storage

### 6.4 Fidelidade / Carência
- [ ] Adicionar campos em `Contract`: `minimum_duration_months`, `start_date`, `cancellation_fine_formula`
- [ ] Criar `app/Services/FidelityService.php`
- [ ] Método `calculateCancellationFine()`: calcula multa com base no período restante + regulamento ANATEL
- [ ] Exibir no painel do admin e na solicitação de cancelamento

### 6.5 Central do Assinante — Melhorias
- [ ] Tela de edição de perfil (dados cadastrais, senha)
- [ ] Download de contrato (PDF)
- [ ] Teste de velocidade (iframe ou lib JS tipo speedtest.net custom)
- [ ] Histórico de pagamentos
- [ ] Notificações in-app (lidas/não lidas)
- [ ] Solicitação de cancelamento com fluxo de retenção

### 6.6 Pipeline de Leads / Vendas
- [ ] Migration `create_leads_table`
- [ ] Model `Lead`: name, phone, email, interest_plan, source, status, notes
- [ ] Status: new, contacted, proposal, negotiation, won, lost
- [ ] Controller `Admin\LeadController.php`
- [ ] View kanban (drag & drop) para leads
- [ ] Conversão de lead em cliente com 1 clique

---

## Fase 7 — Relatórios e BI (Semanas 14-16)

### 7.1 Novos Relatórios
- [ ] **Churn**: `reports/churn` — taxa de cancelamento mensal, por motivo, por região
- [ ] **CAC**: `reports/cac` — custo de aquisição (marketing + comissão) / novos clientes
- [ ] **Inadimplência**: `reports/delinquency` — aging 30/60/90+, total em aberto, % da carteira
- [ ] **Previsto vs Realizado**: `reports/budget` — meta de faturamento vs real, por mês
- [ ] **Efetividade de Cobrança**: `reports/collection` — % recuperado por mês
- [ ] **SLA Chamados**: `reports/sla` — tempo médio de resposta/resolução por categoria/técnico
- [ ] **NPS**: `reports/nps` — pesquisas de satisfação pós-atendimento

### 7.2 Exportação
- [ ] Criar `app/Exports/` (Maatwebsite/Laravel Excel ou raw CSV)
- [ ] `InvoiceExport`
- [ ] `ClientExport`
- [ ] `TicketExport`
- [ ] `FinancialReportExport`
- [ ] Botões de exportar em todas as listagens e relatórios
- [ ] Formatos: CSV, XLSX, PDF (dompdf/barryvdh)

### 7.3 Dashboard Executivo
- [ ] Criar `Admin\DashboardController@executive`
- [ ] KPIs em tempo real (com cache de 5 min):
  - Receita do mês (realizada / prevista)
  - Inadimplência total e %
  - Churn do mês
  - Novos clientes
  - Tickets abertos
  - OS pendentes
- [ ] Gráficos: receita últimos 12 meses, evolução de clientes, taxa de conversão

---

## Cronograma Resumido

| Fase | Duração | Marcos |
|---|---|---|
| F1 — Fundação | 2 sem | Console operacional, API rodando, eventos disparando |
| F2 — Financeiro | 3 sem | Gateway integrado, boletos/PIX reais, régua ativa |
| F3 — Estoque | 2 sem | Equipamentos rastreados, almoxarifado funcionando |
| F4 — Campo | 3 sem | OS sendo criadas, técnico usando API |
| F5 — Rede | 4 sem | MikroTik integrado, IP pools, monitoramento ativo |
| F6 — CRM | 3 sem | WhatsApp integrado, contratos digitais, leads |
| F7 — BI | 2 sem | Todos relatórios disponíveis com exportação |

**Total estimado: ~19 semanas (~5 meses)**

---

## Dependências entre Fases

```
F1 (Fundação) ──┬── F2 (Financeiro) ── F7 (BI)
                ├── F3 (Estoque) ────── F4 (Campo)
                ├── F5 (Rede)
                └── F6 (CRM)
```

- F1 é pré-requisito de **todas** as demais (console, queue, API)
- F2 é pré-requisito de indicadores financeiros em F7
- F3 é pré-requisito de check-in/check-out em F4
- F5 pode rodar paralelamente a F3/F4
- F6 pode rodar paralelamente a F3/F4/F5
- F7 depende de todas as fases para ter dados completos

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Contrato {{ $contract->contract_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 12px; line-height: 1.6; color: #333; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 15px; }
        .header h1 { font-size: 18px; color: #2563eb; margin-bottom: 5px; }
        .header p { font-size: 13px; color: #666; }
        .contract-number { font-size: 14px; font-weight: bold; color: #2563eb; text-align: right; margin-bottom: 20px; }
        h2 { font-size: 14px; color: #2563eb; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin: 20px 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table td, table th { padding: 6px 8px; border: 1px solid #ddd; text-align: left; }
        table th { background: #f3f4f6; font-weight: 600; font-size: 11px; }
        .info-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
        .info-grid .item { flex: 1 1 45%; padding: 6px 0; }
        .info-grid .label { font-size: 10px; color: #888; text-transform: uppercase; }
        .info-grid .value { font-size: 12px; font-weight: 600; color: #333; }
        .terms { margin: 20px 0; padding: 15px; background: #f9fafb; border-left: 3px solid #2563eb; }
        .terms p { margin-bottom: 8px; text-align: justify; }
        .signatures { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 45%; }
        .signature-line { border-top: 1px solid #333; margin-top: 60px; padding-top: 8px; font-size: 11px; color: #555; }
        .footer { text-align: center; margin-top: 40px; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .price { font-size: 16px; font-weight: bold; color: #059669; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; }
        .badge-active { background: #d1fae5; color: #065f46; }
        .badge-suspended { background: #fef3c7; color: #92400e; }
        .badge-canceled { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SisProv - Contrato de Prestação de Serviços</h1>
        <p>Provedor de Acesso à Internet</p>
    </div>

    <div class="contract-number">Contrato Nº {{ $contract->contract_number }}</div>

    <p style="text-align:right;font-size:11px;color:#666;">
        Data: {{ $contract->start_date->format('d/m/Y') }}
    </p>

    <h2>Dados do Contrato</h2>
    <div class="info-grid">
        <div class="item"><div class="label">Nº Contrato</div><div class="value">{{ $contract->contract_number }}</div></div>
        <div class="item"><div class="label">Status</div><div class="value"><span class="badge badge-{{ $contract->status }}">{{ $contract->status_label }}</span></div></div>
        <div class="item"><div class="label">Data de Início</div><div class="value">{{ $contract->start_date->format('d/m/Y') }}</div></div>
        <div class="item"><div class="label">Data de Término</div><div class="value">{{ $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Indeterminado' }}</div></div>
        <div class="item"><div class="label">Dia de Vencimento</div><div class="value">Dia {{ $contract->due_day }} de cada mês</div></div>
    </div>

    <h2>Contratante (Cliente)</h2>
    <div class="info-grid">
        <div class="item"><div class="label">Razão Social</div><div class="value">{{ $contract->client->company_name }}</div></div>
        <div class="item"><div class="label">CPF/CNPJ</div><div class="value">{{ $contract->client->document_formatted ?? $contract->client->cpf_cnpj }}</div></div>
        <div class="item"><div class="label">Email</div><div class="value">{{ $contract->client->email }}</div></div>
        <div class="item"><div class="label">Telefone</div><div class="value">{{ $contract->client->phone }}</div></div>
        <div class="item" style="flex:1 1 100%"><div class="label">Endereço</div><div class="value">{{ $contract->client->address_full ?? $contract->client->address }}, {{ $contract->client->city }}/{{ $contract->client->state }}</div></div>
    </div>

    <h2>Plano de Serviço</h2>
    <table>
        <tr><th>Plano</th><th>Velocidade</th><th>Valor</th></tr>
        <tr>
            <td>{{ $contract->plan->name ?? '-' }}</td>
            <td>{{ $contract->plan->speed_label ?? $contract->plan->speed ?? '-' }}</td>
            <td>{{ $contract->plan->price_formatted ?? 'R$ '.number_format($contract->plan->price ?? 0, 2, ',', '.') }}</td>
        </tr>
    </table>

    <h2>Valores</h2>
    <table>
        <tr><th>Descrição</th><th>Valor</th></tr>
        <tr><td>Valor Contratado</td><td>R$ {{ number_format($contract->signed_price, 2, ',', '.') }}</td></tr>
        @if($contract->discount_percent || $contract->discount_value)
        <tr>
            <td>Desconto ({{ $contract->discount_type == 'percent' ? $contract->discount_percent.'%' : 'Fixo' }})</td>
            <td>- R$ {{ number_format($contract->discount_type == 'percent' ? ($contract->signed_price * $contract->discount_percent / 100) : ($contract->discount_value ?? 0), 2, ',', '.') }}</td>
        </tr>
        @endif
        <tr><td><strong>Valor Efetivo</strong></td><td class="price">R$ {{ number_format($contract->effective_price, 2, ',', '.') }}</td></tr>
    </table>

    <h2>Endereço de Instalação</h2>
    <p>{{ $contract->installation_address ?? $contract->client->address }}, {{ $contract->installation_neighborhood ?? '' }} - {{ $contract->installation_city ?? $contract->client->city }}/{{ $contract->installation_state ?? $contract->client->state }}</p>
    @if($contract->installation_complement)
    <p>Complemento: {{ $contract->installation_complement }}</p>
    @endif

    <div class="terms">
        <h2>Termos e Condições</h2>
        <p>1. O presente contrato tem por objeto a prestação de serviços de acesso à internet banda larga, conforme plano contratado.</p>
        <p>2. O pagamento deverá ser efetuado até a data de vencimento indicada, sob pena de suspensão do serviço após inadimplência.</p>
        <p>3. A rescisão antecipada poderá acarretar multa conforme período de fidelidade contratual previsto.</p>
        <p>4. O contratante declara estar ciente das condições do plano, velocidades contratadas e política de uso justo.</p>
        <p>5. Este contrato é regido pela legislação brasileira. Fica eleito o foro da comarca do contratante para dirimir dúvidas.</p>
        @if($contract->notes)
        <p style="margin-top:10px;"><strong>Observações:</strong> {{ $contract->notes }}</p>
        @endif
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">{{ $contract->client->company_name }}</div>
            <p style="font-size:10px;color:#666;margin-top:4px;">CPF/CNPJ: {{ $contract->client->document_formatted ?? $contract->client->cpf_cnpj }}</p>
        </div>
        <div class="signature-box">
            <div class="signature-line">SisProv - Provedor de Internet</div>
            <p style="font-size:10px;color:#666;margin-top:4px;">Representante Legal</p>
        </div>
    </div>

    <div class="footer">
        <p>SisProv - Sistema de Gestão de Provedores | Este documento é uma representação do contrato registrado em sistema.</p>
        <p>Emitido em {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>

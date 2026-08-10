@extends('layouts.admin')

@section('title', 'Relatórios')
@section('page-title', 'Relatórios')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <a href="{{ route('admin.dashboard.executive') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-primary-500">
        <div class="text-3xl mb-3">📊</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Dashboard Executivo</h3>
        <p class="text-sm text-gray-500">Visão geral com KPIs, receita 12 meses, evolução de clientes.</p>
    </a>
    <a href="{{ route('admin.reports.financial') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-emerald-500">
        <div class="text-3xl mb-3">💰</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Relatório Financeiro</h3>
        <p class="text-sm text-gray-500">Receitas, faturas, tickets médios e gráficos de desempenho financeiro.</p>
    </a>
    <a href="{{ route('admin.reports.clients') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-blue-500">
        <div class="text-3xl mb-3">👥</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Relatório de Clientes</h3>
        <p class="text-sm text-gray-500">Distribuição por status, top clientes, clientes por cidade.</p>
    </a>
    <a href="{{ route('admin.reports.tickets') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-amber-500">
        <div class="text-3xl mb-3">🎫</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Relatório de Chamados</h3>
        <p class="text-sm text-gray-500">Chamados por categoria, prioridade, tempo médio de resolução.</p>
    </a>
    <a href="{{ route('admin.reports.churn') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-red-500">
        <div class="text-3xl mb-3">📉</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Relatório de Churn</h3>
        <p class="text-sm text-gray-500">Taxa de cancelamento, contratos ativos, churn rate mensal.</p>
    </a>
    <a href="{{ route('admin.reports.cac') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-violet-500">
        <div class="text-3xl mb-3">📈</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Relatório de CAC</h3>
        <p class="text-sm text-gray-500">Custo de aquisição de clientes, novos clientes, investimento marketing.</p>
    </a>
    <a href="{{ route('admin.reports.delinquency') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-red-500">
        <div class="text-3xl mb-3">⚠️</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Relatório de Inadimplência</h3>
        <p class="text-sm text-gray-500">Aging de inadimplência, total em aberto, percentual da carteira.</p>
    </a>
    <a href="{{ route('admin.reports.budget') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-teal-500">
        <div class="text-3xl mb-3">🎯</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Relatório de Orçamento</h3>
        <p class="text-sm text-gray-500">Metas mensais, faturado, recebido e percentual de atingimento.</p>
    </a>
    <a href="{{ route('admin.reports.collection') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-cyan-500">
        <div class="text-3xl mb-3">💳</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Relatório de Cobrança</h3>
        <p class="text-sm text-gray-500">Total recebido, em aberto, taxa de cobrança mensal.</p>
    </a>
    <a href="{{ route('admin.reports.sla') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-indigo-500">
        <div class="text-3xl mb-3">⏱️</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Relatório de SLA</h3>
        <p class="text-sm text-gray-500">Tempo médio de resolução, SLA por categoria e técnico.</p>
    </a>
    <a href="{{ route('admin.reports.nps') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition border-l-4 border-pink-500">
        <div class="text-3xl mb-3">⭐</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Relatório de NPS</h3>
        <p class="text-sm text-gray-500">Net Promoter Score, promotores, neutros, detratores.</p>
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Exportar Dados</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.reports.export.invoices') }}" class="flex items-center gap-3 p-4 border rounded-lg hover:bg-gray-50 transition">
            <span class="text-2xl">📄</span>
            <div>
                <p class="font-medium text-gray-800">Exportar Faturas</p>
                <p class="text-xs text-gray-500">CSV</p>
            </div>
        </a>
        <a href="{{ route('admin.reports.export.clients') }}" class="flex items-center gap-3 p-4 border rounded-lg hover:bg-gray-50 transition">
            <span class="text-2xl">📄</span>
            <div>
                <p class="font-medium text-gray-800">Exportar Clientes</p>
                <p class="text-xs text-gray-500">CSV</p>
            </div>
        </a>
        <a href="{{ route('admin.reports.export.tickets') }}" class="flex items-center gap-3 p-4 border rounded-lg hover:bg-gray-50 transition">
            <span class="text-2xl">📄</span>
            <div>
                <p class="font-medium text-gray-800">Exportar Chamados</p>
                <p class="text-xs text-gray-500">CSV</p>
            </div>
        </a>
        <a href="{{ route('admin.reports.export.financial') }}" class="flex items-center gap-3 p-4 border rounded-lg hover:bg-gray-50 transition">
            <span class="text-2xl">📄</span>
            <div>
                <p class="font-medium text-gray-800">Exportar Financeiro</p>
                <p class="text-xs text-gray-500">CSV</p>
            </div>
        </a>
    </div>
</div>
@endsection

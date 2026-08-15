<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SisProv - Sistema de Gestão para Provedores de Internet</title>
    <meta name="description" content="SisProv é o sistema completo para gestão de provedores de internet. Automatize cobranças, gerencie sua rede, controle estoque e encante seus clientes.">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌐</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:{50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a'}}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Inter',sans-serif}
        html{scroll-behavior:smooth}
        .gradient-hero{background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 50%,#2563eb 100%)}
        .gradient-card{background:linear-gradient(135deg,#1e3a8a,#2563eb)}
        .animate-float{animation:float 3s ease-in-out infinite}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
        .scroll-to-top{opacity:0;visibility:hidden;transition:opacity 0.3s,visibility 0.3s}
        .scroll-to-top.show{opacity:1;visibility:visible}
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

<header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="#" class="flex items-center gap-2 text-xl font-bold text-gray-900">
                <span class="text-2xl">🌐</span>
                <span>SisProv</span>
            </a>
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#funcionalidades" class="nav-link hover:text-primary-600 transition" data-section="funcionalidades">Funcionalidades</a>
                <a href="#modulos" class="nav-link hover:text-primary-600 transition" data-section="modulos">Módulos</a>
                <a href="#vantagens" class="nav-link hover:text-primary-600 transition" data-section="vantagens">Vantagens</a>
                <a href="#contato" class="nav-link hover:text-primary-600 transition" data-section="contato">Contato</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">Acessar</a>
                <a href="#contato" class="hidden sm:inline-flex bg-primary-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-primary-700 transition shadow-lg shadow-primary-200">Solicitar Demonstração</a>
            </div>
        </div>
    </div>
</header>

<section class="gradient-hero pt-24 pb-20 sm:pt-32 sm:pb-28 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 25% 50%, rgba(255,255,255,0.3) 0%, transparent 50%), radial-gradient(circle at 75% 30%, rgba(255,255,255,0.15) 0%, transparent 50%)"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white/90 text-sm px-4 py-2 rounded-full mb-6 border border-white/10">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Sistema 100% web
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                    Gestão completa<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-cyan-200">para seu provedor</span>
                </h1>
                <p class="text-lg sm:text-xl text-blue-100/80 max-w-xl mx-auto lg:mx-0 mb-8 leading-relaxed">
                    Automatize cobranças, gerencie sua rede FTTH, controle estoque, organize ordens de serviço e reduza a inadimplência do seu ISP com uma plataforma integrada.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#contato" class="inline-flex items-center justify-center gap-2 bg-white text-primary-700 px-8 py-4 rounded-xl text-base font-bold hover:bg-gray-50 transition shadow-2xl shadow-blue-900/30">
                        📅 Solicitar Demonstração
                    </a>
                    <a href="#funcionalidades" class="inline-flex items-center justify-center gap-2 border border-white/30 text-white px-8 py-4 rounded-xl text-base font-semibold hover:bg-white/10 transition">
                        Ver Funcionalidades →
                    </a>
                </div>
                <div class="flex items-center gap-6 mt-10 justify-center lg:justify-start text-blue-200/70 text-sm">
                    <span>✅ Sem fidelidade</span>
                    <span>🔧 Suporte dedicado</span>
                    <span>☁️ Cloud ou On-premise</span>
                </div>
            </div>
            <div class="hidden lg:flex justify-center">
                <div class="relative">
                    <div class="w-[500px] h-[400px] bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6 animate-float">
                        <div class="grid grid-cols-2 gap-4 h-full">
                            <div class="bg-white/10 rounded-xl p-4 flex flex-col justify-between">
                                <div class="text-3xl">💰</div>
                                <div>
                                    <div class="text-white text-2xl font-bold">R$ 0</div>
                                    <div class="text-blue-200/60 text-xs">Inadimplência<br>automatizada</div>
                                </div>
                            </div>
                            <div class="bg-white/10 rounded-xl p-4 flex flex-col justify-between">
                                <div class="text-3xl">📊</div>
                                <div>
                                    <div class="text-white text-2xl font-bold">98%</div>
                                    <div class="text-blue-200/60 text-xs">Receita<br>recorrente</div>
                                </div>
                            </div>
                            <div class="bg-white/10 rounded-xl p-4 flex flex-col justify-between">
                                <div class="text-3xl">⚡</div>
                                <div>
                                    <div class="text-white text-2xl font-bold">-60%</div>
                                    <div class="text-blue-200/60 text-xs">Tempo em<br>atendimento</div>
                                </div>
                            </div>
                            <div class="bg-white/10 rounded-xl p-4 flex flex-col justify-between">
                                <div class="text-3xl">🛡️</div>
                                <div>
                                    <div class="text-white text-2xl font-bold">100%</div>
                                    <div class="text-blue-200/60 text-xs">Equipamentos<br>rastreados</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="funcionalidades" class="py-20 sm:py-28 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-widest">Funcionalidades</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-3 mb-4">Tudo que seu ISP precisa em um só lugar</h2>
            <p class="text-lg text-gray-500">Do financeiro à operação de campo, o SisProv centraliza a gestão do seu provedor em uma plataforma moderna e integrada.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg border border-gray-100 hover:border-primary-100 transition-all group">
                <div class="w-14 h-14 bg-primary-50 rounded-xl flex items-center justify-center text-2xl mb-5 group-hover:bg-primary-100 transition">💰</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Gestão Financeira</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Emissão automática de boletos e PIX, régua de cobrança inteligente, bloqueio/desbloqueio automático por inadimplência e notas fiscais de telecom (modelo 21/22).</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg border border-gray-100 hover:border-primary-100 transition-all group">
                <div class="w-14 h-14 bg-primary-50 rounded-xl flex items-center justify-center text-2xl mb-5 group-hover:bg-primary-100 transition">🌐</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Gestão de Rede</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Integração com MikroTik e FreeRADIUS, provisionamento PPPoE/IPoE, pools de IP com CGNAT, monitoramento de OLTs e mapa FTTH interativo.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg border border-gray-100 hover:border-primary-100 transition-all group">
                <div class="w-14 h-14 bg-primary-50 rounded-xl flex items-center justify-center text-2xl mb-5 group-hover:bg-primary-100 transition">👥</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">CRM & Atendimento</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Central do assinante com 2ª via de boleto e abertura de chamados, atendimento multicanal (WhatsApp integrado), assinatura digital de contratos e pesquisa NPS.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg border border-gray-100 hover:border-primary-100 transition-all group">
                <div class="w-14 h-14 bg-primary-50 rounded-xl flex items-center justify-center text-2xl mb-5 group-hover:bg-primary-100 transition">🔧</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Ordem de Serviço</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Agendamento inteligente de instalações e reparos, app técnico com funcionalidade offline, captura de fotos e assinatura digital do cliente.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg border border-gray-100 hover:border-primary-100 transition-all group">
                <div class="w-14 h-14 bg-primary-50 rounded-xl flex items-center justify-center text-2xl mb-5 group-hover:bg-primary-100 transition">📦</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Controle de Estoque</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Rastreamento completo de equipamentos em comodato (roteadores, ONUs), almoxarifado com controle de entrada/saída e histórico por cliente.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg border border-gray-100 hover:border-primary-100 transition-all group">
                <div class="w-14 h-14 bg-primary-50 rounded-xl flex items-center justify-center text-2xl mb-5 group-hover:bg-primary-100 transition">📈</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Relatórios & BI</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Dashboards executivos com KPIs de churn, CAC e inadimplência, relatórios financeiros e operacionais exportáveis em CSV, XLSX e PDF.</p>
            </div>
        </div>
    </div>
</section>

<section id="modulos" class="py-20 sm:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-widest">Módulos</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-3 mb-4">Uma plataforma, seis módulos integrados</h2>
            <p class="text-lg text-gray-500">Cada módulo funciona de forma independente ou integrada, permitindo que você contrate apenas o que precisa.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="border border-gray-200 rounded-2xl p-6 hover:border-primary-300 hover:shadow-md transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">💳</span>
                    <h3 class="font-bold text-gray-900">Financeiro</h3>
                </div>
                <p class="text-gray-500 text-sm">Faturamento recorrente, gateway de pagamento, NF telecom, régua de cobrança e conciliação.</p>
            </div>
            <div class="border border-gray-200 rounded-2xl p-6 hover:border-primary-300 hover:shadow-md transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">🖥️</span>
                    <h3 class="font-bold text-gray-900">Rede</h3>
                </div>
                <p class="text-gray-500 text-sm">Provisionamento MikroTik/Radius, controle de banda, pools de IP, CGNAT e mapa FTTH.</p>
            </div>
            <div class="border border-gray-200 rounded-2xl p-6 hover:border-primary-300 hover:shadow-md transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">🎫</span>
                    <h3 class="font-bold text-gray-900">CRM</h3>
                </div>
                <p class="text-gray-500 text-sm">Central do assinante, chamados, WhatsApp integrado, assinatura digital e pesquisa NPS.</p>
            </div>
            <div class="border border-gray-200 rounded-2xl p-6 hover:border-primary-300 hover:shadow-md transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">🔧</span>
                    <h3 class="font-bold text-gray-900">Ordem de Serviço</h3>
                </div>
                <p class="text-gray-500 text-sm">Agendamento, app técnico offline, check-in/check-out de equipamentos e assinatura digital.</p>
            </div>
            <div class="border border-gray-200 rounded-2xl p-6 hover:border-primary-300 hover:shadow-md transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">📦</span>
                    <h3 class="font-bold text-gray-900">Estoque</h3>
                </div>
                <p class="text-gray-500 text-sm">Comodato, almoxarifado, fornecedores e rastreabilidade serializada de equipamentos.</p>
            </div>
            <div class="border border-gray-200 rounded-2xl p-6 hover:border-primary-300 hover:shadow-md transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">📊</span>
                    <h3 class="font-bold text-gray-900">BI</h3>
                </div>
                <p class="text-gray-500 text-sm">Dashboards, relatórios exportáveis, indicadores de performance e auditoria completa.</p>
            </div>
        </div>
    </div>
</section>

<section id="vantagens" class="py-20 sm:py-28 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-widest">Vantagens</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-3 mb-4">Por que escolher o SisProv?</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">⚡</div>
                <h3 class="font-bold text-gray-900 mb-2">Automação Total</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Da emissão de boletos ao bloqueio por inadimplência, tudo automatizado para você focar no que importa: crescer.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">🔒</div>
                <h3 class="font-bold text-gray-900 mb-2">Segurança</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Autenticação em dois fatores, auditoria completa de todas as ações e RBAC com perfis granulares de acesso.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">☁️</div>
                <h3 class="font-bold text-gray-900 mb-2">Cloud ou Local</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Funciona na nuvem (SaaS) ou instalado nos seus servidores. Você escolhe o modelo que atende sua necessidade.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">📱</div>
                <h3 class="font-bold text-gray-900 mb-2">App Técnico</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Aplicativo para equipe de campo com funcionamento offline, ideal para áreas com cobertura de internet limitada.</p>
            </div>
        </div>
    </div>
</section>

<section id="contato" class="py-20 sm:py-28 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-widest">Contato</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-3 mb-4">Solicite uma demonstração</h2>
            <p class="text-lg text-gray-500">Preencha o formulário e nossa equipe entrará em contato para apresentar o SisProv personalizado para a realidade do seu provedor.</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12">
            <form action="#" method="POST" class="grid sm:grid-cols-2 gap-6">
                @csrf
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone / WhatsApp</label>
                    <input type="tel" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provedor</label>
                    <input type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nº de clientes</label>
                    <select class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                        <option>Até 200</option>
                        <option>200 a 500</option>
                        <option>500 a 1.000</option>
                        <option>1.000 a 5.000</option>
                        <option>5.000+</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
                    <textarea rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="w-full bg-primary-600 text-white font-bold py-4 rounded-xl hover:bg-primary-700 transition shadow-lg shadow-primary-200 text-base">
                        📅 Solicitar Demonstração
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<footer class="bg-gray-900 text-gray-400 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-2 text-white font-bold text-lg mb-4">
                    <span class="text-2xl">🌐</span>
                    <span>SisProv</span>
                </div>
                <p class="text-sm leading-relaxed">Sistema completo para gestão de provedores de internet. Automatize, gerencie e cresça.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Produto</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#funcionalidades" class="hover:text-white transition">Funcionalidades</a></li>
                    <li><a href="#modulos" class="hover:text-white transition">Módulos</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Suporte</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition">Documentação</a></li>
                    <li><a href="#" class="hover:text-white transition">API</a></li>
                    <li><a href="#contato" class="hover:text-white transition">Contato</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Contato</h4>
                <ul class="space-y-2 text-sm">
                    <li>contato@sisprov.com.br</li>
                    <li>(11) 99999-9999</li>
                    <li>Seg a Sex, 8h às 18h</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-10 pt-8 text-sm text-center">
            <p>&copy; {{ date('Y') }} SisProv. Todos os direitos reservados.</p>
            <p class="mt-2 text-xs text-gray-600">Desenvolvido por <a href="https://hmosorio.com.br" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition">HMOsorio c/IA</a></p>
        </div>
    </div>
</footer>

<button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="scroll-to-top fixed bottom-8 right-8 z-50 w-12 h-12 bg-primary-600 text-white rounded-xl shadow-lg shadow-primary-200 hover:bg-primary-700 transition-all flex items-center justify-center text-xl">↑</button>

<script>
const scrollBtn = document.querySelector('.scroll-to-top');
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-link');

window.addEventListener('scroll', function() {
    scrollBtn.classList.toggle('show', window.scrollY > 600);

    let current = '';
    sections.forEach(s => {
        if (window.scrollY >= s.offsetTop - 200) current = s.id;
    });
    navLinks.forEach(l => {
        l.classList.toggle('text-primary-600', l.dataset.section === current);
        l.classList.toggle('font-semibold', l.dataset.section === current);
    });
});
</script>
</body>
</html>

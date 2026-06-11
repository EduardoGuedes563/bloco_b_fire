<?php
session_start();
require_once '../includes/auth.php';
exigirLogin('admin');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação — Sistema de Incêndio Bloco B</title>
    <link rel="stylesheet" href="/bloco_b_fire/assets/css/style.css">
    <style>
        .doc-nav {
            position: sticky;
            top: 24px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
            padding: 20px;
        }
        .doc-nav a {
            display: block;
            padding: 7px 12px;
            font-size: 13px;
            color: #555;
            border-radius: 6px;
            text-decoration: none;
            transition: all .2s;
        }
        .doc-nav a:hover { background: #f4f6f7; color: #c0392b; }
        .doc-nav .nav-titulo {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #aaa;
            padding: 10px 12px 4px;
            letter-spacing: .5px;
        }
        .doc-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
            padding: 32px;
            margin-bottom: 24px;
            scroll-margin-top: 24px;
        }
        .doc-section h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 8px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }
        .doc-section h3 {
            font-size: 15px;
            font-weight: 700;
            margin: 20px 0 8px;
            color: #c0392b;
        }
        .doc-section p, .doc-section li {
            font-size: 14px;
            color: #444;
            line-height: 1.8;
        }
        .doc-section ul { padding-left: 20px; margin: 8px 0; }
        .doc-section table { width: 100%; border-collapse: collapse; font-size: 13px; margin: 12px 0; }
        .doc-section th { background: #f4f6f7; padding: 10px 14px; text-align: left; font-size: 12px; text-transform: uppercase; color: #888; }
        .doc-section td { padding: 10px 14px; border-bottom: 1px solid #f0f0f0; }
        .code-inline {
            background: #f4f6f7;
            padding: 2px 7px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #c0392b;
        }
        .code-block {
            background: #1e1e2e;
            color: #cdd6f4;
            padding: 16px 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 12px 0;
            overflow-x: auto;
            white-space: pre;
            line-height: 1.6;
        }
        .tag-metodo {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            margin-right: 6px;
        }
        .get    { background: #d6eaf8; color: #1a5276; }
        .post   { background: #d5f5e3; color: #1e8449; }
        .put    { background: #fef9e7; color: #b7950b; }
        .delete { background: #fadbd8; color: #c0392b; }
    </style>
</head>
<body>
<div class="wrapper">
    <?php require_once '../includes/sidebar_admin.php'; ?>

    <main class="conteudo">

        <div style="margin-bottom:24px">
            <h2 style="font-size:22px;font-weight:700;margin:0">Documentação do Projeto</h2>
            <p style="color:#888;font-size:13px;margin:4px 0 0">
                Sistema de Gerenciamento de Extintores — Bloco B · UNESC
            </p>
        </div>

        <div style="display:grid;grid-template-columns:200px 1fr;gap:24px;align-items:start">

            <!-- Navegação lateral -->
            <nav class="doc-nav">
                <div class="nav-titulo">Conteúdo</div>
                <a href="#introducao">1. Introdução</a>
                <a href="#tecnologias">2. Tecnologias</a>
                <a href="#banco">3. Banco de Dados</a>
                <a href="#seguranca">4. Segurança</a>
                <a href="#api">5. API REST</a>
                <a href="#acesso">6. Níveis de Acesso</a>
                <a href="#estrutura">7. Estrutura de Pastas</a>
                <a href="#como-rodar">8. Como Rodar</a>
            </nav>

            <!-- Conteúdo -->
            <div>

                <!-- 1. Introdução -->
                <div class="doc-section" id="introducao">
                    <h2>1. Introdução</h2>
                    <p>
                        O <strong>Sistema de Gerenciamento de Extintores do Bloco B</strong> é uma aplicação web
                        desenvolvida como projeto acadêmico na UNESC. O sistema permite o cadastro, monitoramento
                        e controle dos 28 extintores instalados no Bloco B, distribuídos entre Térreo, 1º Andar e 2º Andar.
                    </p>
                    <h3>Objetivo</h3>
                    <p>
                        Digitalizar o controle de inspeção dos extintores, substituindo planilhas manuais por um
                        sistema web com autenticação segura, controle de acesso por perfil e API REST para consulta
                        dos dados.
                    </p>
                    <h3>Funcionalidades principais</h3>
                    <ul>
                        <li>Dashboard com resumo de status dos extintores em tempo real</li>
                        <li>CRUD completo de extintores (apenas administrador)</li>
                        <li>Atualização automática de status baseada na data de inspeção</li>
                        <li>Autenticação com dois fatores (2FA) via e-mail</li>
                        <li>API REST com autenticação por token Bearer</li>
                        <li>Demonstração prática de SQL Injection e proteção</li>
                    </ul>
                </div>

                <!-- 2. Tecnologias -->
                <div class="doc-section" id="tecnologias">
                    <h2>2. Tecnologias Utilizadas</h2>
                    <table>
                        <thead>
                            <tr><th>Tecnologia</th><th>Versão</th><th>Uso</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>PHP</td><td>8.2</td><td>Backend e lógica do servidor</td></tr>
                            <tr><td>MySQL</td><td>8.0</td><td>Banco de dados relacional</td></tr>
                            <tr><td>HTML5 + CSS3</td><td>—</td><td>Estrutura e estilo das páginas</td></tr>
                            <tr><td>JavaScript</td><td>ES6+</td><td>Interações no frontend</td></tr>
                            <tr><td>Bootstrap</td><td>5.3</td><td>Framework CSS responsivo</td></tr>
                            <tr><td>PDO</td><td>—</td><td>Acesso seguro ao banco de dados</td></tr>
                            <tr><td>PHPMailer</td><td>6.x</td><td>Envio de e-mail para 2FA</td></tr>
                            <tr><td>Mailtrap</td><td>—</td><td>SMTP para testes de e-mail</td></tr>
                            <tr><td>XAMPP</td><td>8.2</td><td>Servidor local (Apache + MySQL)</td></tr>
                            <tr><td>Postman</td><td>—</td><td>Teste e documentação da API</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- 3. Banco de Dados -->
                <div class="doc-section" id="banco">
                    <h2>3. Banco de Dados</h2>
                    <p>Banco: <span class="code-inline">bloco_b_fire</span> — charset UTF-8mb4</p>

                    <h3>Tabela: usuarios</h3>
                    <table>
                        <thead>
                            <tr><th>Campo</th><th>Tipo</th><th>Descrição</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>id</td><td>INT PK AUTO_INCREMENT</td><td>Identificador único</td></tr>
                            <tr><td>nome</td><td>VARCHAR(100)</td><td>Nome completo</td></tr>
                            <tr><td>email</td><td>VARCHAR(150) UNIQUE</td><td>E-mail de acesso</td></tr>
                            <tr><td>senha</td><td>VARCHAR(255)</td><td>Hash bcrypt da senha</td></tr>
                            <tr><td>tipo</td><td>ENUM('admin','usuario')</td><td>Nível de acesso</td></tr>
                            <tr><td>codigo_2fa</td><td>VARCHAR(6)</td><td>Código OTP temporário</td></tr>
                            <tr><td>expiracao_2fa</td><td>DATETIME</td><td>Expiração do código 2FA</td></tr>
                            <tr><td>criado_em</td><td>TIMESTAMP</td><td>Data de cadastro</td></tr>
                        </tbody>
                    </table>

                    <h3>Tabela: extintores</h3>
                    <table>
                        <thead>
                            <tr><th>Campo</th><th>Tipo</th><th>Descrição</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>id</td><td>INT PK AUTO_INCREMENT</td><td>Identificador único</td></tr>
                            <tr><td>codigo</td><td>VARCHAR(20) UNIQUE</td><td>Código do extintor</td></tr>
                            <tr><td>tipo</td><td>VARCHAR(50)</td><td>Tipo (PQS, CO2, Água)</td></tr>
                            <tr><td>capacidade</td><td>VARCHAR(20)</td><td>Capacidade (6kg, 10L)</td></tr>
                            <tr><td>fabricante</td><td>VARCHAR(100)</td><td>Fabricante</td></tr>
                            <tr><td>numero_serie</td><td>VARCHAR(100)</td><td>Número de série</td></tr>
                            <tr><td>data_instalacao</td><td>DATE</td><td>Data de instalação</td></tr>
                            <tr><td>localizacao</td><td>VARCHAR(200)</td><td>Localização no bloco</td></tr>
                            <tr><td>pavimento</td><td>VARCHAR(50)</td><td>Térreo, 1º ou 2º Andar</td></tr>
                            <tr><td>proxima_inspecao</td><td>DATE</td><td>Data da próxima inspeção</td></tr>
                            <tr><td>status</td><td>ENUM</td><td>normal / a_vencer / vencido</td></tr>
                            <tr><td>ativo</td><td>TINYINT(1)</td><td>Soft delete (0 = removido)</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- 4. Segurança -->
                <div class="doc-section" id="seguranca">
                    <h2>4. Segurança</h2>

                    <h3>SQL Injection — PDO Prepared Statements</h3>
                    <p>Toda interação com o banco usa PDO com parâmetros vinculados. O input nunca é concatenado diretamente na query.</p>
                    <div class="code-block">// INSEGURO (nunca usado no sistema)
$sql = "SELECT * FROM usuarios WHERE email = '$email'";

// SEGURO (padrão em todo o sistema)
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->execute([':email' => $email]);</div>

                    <h3>Senhas — Bcrypt</h3>
                    <p>Senhas são armazenadas com <span class="code-inline">password_hash($senha, PASSWORD_BCRYPT)</span> e verificadas com <span class="code-inline">password_verify()</span>. Nunca em texto puro.</p>

                    <h3>Autenticação em Dois Fatores (2FA)</h3>
                    <p>Após validar e-mail e senha, o sistema gera um código OTP de 6 dígitos, salva no banco com expiração de 10 minutos e envia por e-mail. Sem o código, o acesso é negado mesmo com credenciais corretas.</p>

                    <h3>CSRF — Cross-Site Request Forgery</h3>
                    <p>Todo formulário inclui um token único por sessão gerado com <span class="code-inline">bin2hex(random_bytes(32))</span>. O servidor valida o token antes de processar qualquer POST.</p>

                    <h3>XSS — Cross-Site Scripting</h3>
                    <p>Todo dado exibido na tela passa por <span class="code-inline">htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')</span>, neutralizando scripts maliciosos.</p>
                </div>

                <!-- 5. API -->
                <div class="doc-section" id="api">
                    <h2>5. API REST</h2>
                    <p>Base URL: <span class="code-inline">http://localhost/bloco_b_fire/api/extintores.php</span></p>
                    <p>Autenticação: <span class="code-inline">Authorization: Bearer bloco_b_fire_token_2024</span> (obrigatório para POST, PUT e DELETE)</p>

                    <h3>Endpoints</h3>
                    <table>
                        <thead>
                            <tr><th>Método</th><th>URL</th><th>Descrição</th><th>Auth</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="tag-metodo get">GET</span></td>
                                <td><span class="code-inline">/api/extintores.php</span></td>
                                <td>Lista todos os extintores</td>
                                <td>Não</td>
                            </tr>
                            <tr>
                                <td><span class="tag-metodo get">GET</span></td>
                                <td><span class="code-inline">/api/extintores.php?id=1</span></td>
                                <td>Busca extintor por ID</td>
                                <td>Não</td>
                            </tr>
                            <tr>
                                <td><span class="tag-metodo get">GET</span></td>
                                <td><span class="code-inline">/api/extintores.php?status=vencido</span></td>
                                <td>Filtra por status</td>
                                <td>Não</td>
                            </tr>
                            <tr>
                                <td><span class="tag-metodo post">POST</span></td>
                                <td><span class="code-inline">/api/extintores.php</span></td>
                                <td>Cria novo extintor</td>
                                <td>Sim</td>
                            </tr>
                            <tr>
                                <td><span class="tag-metodo put">PUT</span></td>
                                <td><span class="code-inline">/api/extintores.php?id=1</span></td>
                                <td>Atualiza extintor</td>
                                <td>Sim</td>
                            </tr>
                            <tr>
                                <td><span class="tag-metodo delete">DELETE</span></td>
                                <td><span class="code-inline">/api/extintores.php?id=1</span></td>
                                <td>Remove extintor (soft delete)</td>
                                <td>Sim</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 6. Níveis de Acesso -->
                <div class="doc-section" id="acesso">
                    <h2>6. Níveis de Acesso</h2>
                    <table>
                        <thead>
                            <tr><th>Funcionalidade</th><th>Usuário</th><th>Admin</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Ver dashboard</td><td>✅</td><td>✅</td></tr>
                            <tr><td>Listar extintores</td><td>✅</td><td>✅</td></tr>
                            <tr><td>Filtrar extintores</td><td>✅</td><td>✅</td></tr>
                            <tr><td>Adicionar extintor</td><td>❌</td><td>✅</td></tr>
                            <tr><td>Editar extintor</td><td>❌</td><td>✅</td></tr>
                            <tr><td>Excluir extintor</td><td>❌</td><td>✅</td></tr>
                            <tr><td>Página de segurança</td><td>❌</td><td>✅</td></tr>
                            <tr><td>Documentação</td><td>❌</td><td>✅</td></tr>
                            <tr><td>API GET</td><td>✅</td><td>✅</td></tr>
                            <tr><td>API POST/PUT/DELETE</td><td>❌</td><td>✅</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- 7. Estrutura de Pastas -->
                <div class="doc-section" id="estrutura">
                    <h2>7. Estrutura de Pastas</h2>
                    <div class="code-block">bloco_b_fire/
├── admin/
│   ├── dashboard.php       # Dashboard do administrador
│   ├── extintores.php      # Listagem com CRUD
│   ├── extintor_form.php   # Formulário criar/editar
│   └── excluir_extintor.php
├── api/
│   └── extintores.php      # API REST (GET/POST/PUT/DELETE)
├── assets/
│   ├── css/style.css       # Estilos globais
│   └── js/main.js          # JavaScript
├── docs/
│   └── index.php           # Esta documentação
├── includes/
│   ├── auth.php            # Controle de sessão e acesso
│   ├── db.php              # Conexão PDO com o banco
│   ├── functions.php       # Funções auxiliares
│   ├── mailer.php          # Envio de e-mail 2FA
│   ├── sidebar_admin.php   # Sidebar do admin
│   └── sidebar_usuario.php # Sidebar do usuário
├── seguranca/
│   └── index.php           # Demo SQL Injection
├── usuario/
│   ├── dashboard.php       # Dashboard somente leitura
│   └── extintores.php      # Listagem somente leitura
├── vendor/                 # PHPMailer (Composer)
├── index.php               # Redireciona para login
├── login_admin.php         # Login do administrador
├── login_usuario.php       # Login do usuário
├── verificar_2fa.php       # Verificação do código 2FA
└── logout.php              # Encerra sessão</div>
                </div>

                <!-- 8. Como Rodar -->
                <div class="doc-section" id="como-rodar">
                    <h2>8. Como Rodar o Projeto</h2>

                    <h3>Pré-requisitos</h3>
                    <ul>
                        <li>XAMPP com Apache e MySQL rodando</li>
                        <li>Composer instalado</li>
                        <li>Conta no Mailtrap (gratuita)</li>
                    </ul>

                    <h3>Passo a passo</h3>
                    <div class="code-block">1. Copie a pasta bloco_b_fire para:
   C:\xampp\htdocs\bloco_b_fire\

2. No phpMyAdmin, execute o arquivo:
   bloco_b_fire.sql

3. Configure o Mailtrap em:
   includes/mailer.php

4. Acesse no navegador:
   http://localhost/bloco_b_fire

5. Login admin:
   E-mail: admin@blocob.edu.br
   Senha:  Admin@123</div>

                    <h3>Credenciais padrão</h3>
                    <table>
                        <thead>
                            <tr><th>Perfil</th><th>E-mail</th><th>Senha</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Administrador</td><td>admin@blocob.edu.br</td><td>Admin@123</td></tr>
                            <tr><td>Usuário</td><td>visitante@blocob.edu.br</td><td>Teste@123</td></tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </main>
</div>
<script src="/bloco_b_fire/assets/js/main.js"></script>
</body>
</html>
# Sistema de Gestão de Extintores - Bloco B

## 📋 Descrição

Sistema web desenvolvido para o gerenciamento e monitoramento dos extintores de incêndio do Bloco B da faculdade.

O sistema permite o controle de localização, validade, inspeções e status dos extintores, além de oferecer diferentes níveis de acesso para usuários e administradores.

## 🎯 Objetivos

* Controlar os extintores do Bloco B.
* Monitorar datas de validade e inspeção.
* Facilitar a consulta das informações pelos usuários.
* Permitir que administradores realizem atualizações no sistema.
* Aplicar boas práticas de segurança da informação.

## 🚀 Funcionalidades

### Usuário

* Visualizar extintores cadastrados.
* Consultar localização dos equipamentos.
* Verificar status e validade.

### Administrador

* Cadastrar extintores.
* Editar informações.
* Atualizar datas de inspeção.
* Excluir registros.

## 🔒 Segurança

* Autenticação de usuários.
* Controle de acesso por perfil.
* Proteção contra SQL Injection utilizando Prepared Statements.
* Senhas criptografadas com password_hash().
* Autenticação em dois fatores (2FA).

## 🛠 Tecnologias Utilizadas

* PHP
* HTML5
* CSS3
* JavaScript
* MySQL
* XAMPP
* Bootstrap
* Postman (testes da API)

## ⚙️ Instalação

1. Instale o XAMPP.

2. Inicie Apache e MySQL.

3. Clone este repositório:
   git clone https://github.com/seuusuario/seu-repositorio.git

4. Importe o banco de dados no phpMyAdmin.

5. Configure as credenciais do banco no arquivo database.php.

6. Acesse o sistema pelo navegador.

## 📊 Banco de Dados

Principais informações armazenadas:

* Código do extintor
* Tipo
* Capacidade
* Número de série
* Localização
* Pavimento
* Data de instalação
* Próxima inspeção
* Status

## 👨‍💻 Autor

Eduardo Guedes, Arthur Milke, Rhyan Valt, Pedro Henrique Lopes e João Victor Machado

## 📚 Projeto Acadêmico

Projeto desenvolvido para a disciplina de Segurança da Informação e Desenvolvimento Web.

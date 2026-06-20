=== Athena Lead Rotation ===
Contributors: athena
Tags: leads, fluent-forms, fluentcrm, rodizio, sdr
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Distribui automaticamente os leads recebidos via Fluent Forms entre uma lista de SDRs cadastrados, aplicando a tag correspondente no FluentCRM em sistema de rodízio (round-robin).

Funcionalidades:
* Captura submissões do Fluent Forms (hooks moderno e legado)
* Agenda o processamento via WP-Cron (5s após o envio)
* Localiza o contato no FluentCRM pelo e-mail
* Aplica a tag do próximo SDR na fila, em rodízio
* Painel em Configurações > Lead Rotation com status do rodízio e reset manual

== Requisitos ==
* Fluent Forms
* FluentCRM

== Instalação ==
1. Envie a pasta do plugin para `/wp-content/plugins/`
2. Ative o plugin no painel do WordPress
3. Ajuste a lista de SDRs (nome e tag_id) na função `alr_get_sdrs()`
4. Acesse Configurações > Lead Rotation para acompanhar o status

== Changelog ==
= 1.0.0 =
* Versão inicial

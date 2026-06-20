=== Athena Lead Rotation ===
Contributors: athena
Tags: leads, fluent-forms, fluentcrm, round-robin, sdr
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

🇧🇷 Português

Distribui automaticamente os leads recebidos via Fluent Forms entre uma lista de SDRs cadastrados, aplicando a tag correspondente no FluentCRM em sistema de rodízio (round-robin).

Funcionalidades:

* Captura submissões do Fluent Forms (hooks moderno e legado)
* Agenda o processamento via WP-Cron (5 segundos após o envio)
* Localiza o contato no FluentCRM pelo e-mail
* Aplica a tag do próximo SDR na fila em sistema de rodízio
* Painel em Configurações > Lead Rotation com status do rodízio e reset manual

🇺🇸 English

Automatically distributes leads submitted through Fluent Forms among registered SDRs, applying the corresponding FluentCRM tag using a round-robin assignment system.

Features:

* Captures Fluent Forms submissions (modern and legacy hooks)
* Schedules processing via WP-Cron (5 seconds after submission)
* Finds the contact in FluentCRM by email address
* Assigns the next SDR tag in the rotation queue
* Admin panel under Settings > Lead Rotation with rotation status and manual reset

== Requirements ==

🇧🇷 Português

* Fluent Forms
* FluentCRM

🇺🇸 English

* Fluent Forms
* FluentCRM

== Installation ==

🇧🇷 Português

1. Envie a pasta do plugin para `/wp-content/plugins/`
2. Ative o plugin no painel do WordPress
3. Ajuste a lista de SDRs (nome e tag_id) na função `alr_get_sdrs()`
4. Acesse Configurações > Lead Rotation para acompanhar o status

🇺🇸 English

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin from the WordPress admin panel
3. Configure the SDR list (name and tag_id) inside the `alr_get_sdrs()` function
4. Go to Settings > Lead Rotation to monitor the rotation status

== Changelog ==

= 1.0.0 =

* Initial release
* Versão inicial

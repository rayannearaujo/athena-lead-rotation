<?php
/**
 * Plugin Name: Athena Lead Rotation
 * Description: Distribui leads do Fluent Forms entre SDRs no FluentCRM em rodízio automático.
 * Version:    1.0.0
 * Author:      Rayanne Araújo
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// =============================================================================
// CONFIGURAÇÕES
// =============================================================================

define( 'ALR_OPTION_KEY', 'alr_last_sdr_index' );
define( 'ALR_CRON_HOOK',  'athena_process_lead_rotation' );

function alr_get_sdrs() {
    return [
        [ 'name' => 'SDR 1', 'tag_id' => 3 ],
        [ 'name' => 'SDR 2',   'tag_id' => 4 ],
        [ 'name' => 'SDR 3',   'tag_id' => 5 ],
    ];
}

// =============================================================================
// PASSO 1 — CAPTURA A SUBMISSÃO
// Registra os dois hooks possíveis do Fluent Forms para garantir compatibilidade
// com qualquer versão instalada.
// =============================================================================

add_action( 'plugins_loaded', function() {

    if ( ! function_exists( 'wpFluentForm' ) ) {
        error_log( '[ALR] ERRO: Fluent Forms não encontrado.' );
        return;
    }

    // Hook moderno (Fluent Forms 4.x+)
    add_action( 'fluentform/after_submission', 'alr_hook_modern', 99, 3 );

    // Hook legado (versões anteriores)
    add_action( 'fluentform_submission_inserted', 'alr_hook_legacy', 99, 3 );

    error_log( '[ALR] Hooks registrados: fluentform/after_submission + fluentform_submission_inserted' );
} );

// Wrapper do hook moderno — ordem: $formData, $form, $entryId
function alr_hook_modern( $formData, $form, $entryId ) {
    error_log( '[ALR] Hook MODERNO disparou — Form ID: ' . ( isset( $form->id ) ? $form->id : 'desconhecido' ) . ' | Entry: ' . $entryId );
    alr_schedule( $formData, $entryId );
}

// Wrapper do hook legado — ordem: $entryId, $formData, $form
function alr_hook_legacy( $entryId, $formData, $form ) {
    error_log( '[ALR] Hook LEGADO disparou — Form ID: ' . ( isset( $form->id ) ? $form->id : 'desconhecido' ) . ' | Entry: ' . $entryId );
    alr_schedule( $formData, $entryId );
}

// =============================================================================
// PASSO 2 — AGENDA O PROCESSAMENTO PARA 30 SEGUNDOS DEPOIS
// =============================================================================

function alr_schedule( $formData, $entryId ) {

    $email = alr_extract_email( $formData );

    if ( empty( $email ) ) {
        error_log( '[ALR] ERRO: e-mail não encontrado. Chaves disponíveis: ' . implode( ', ', array_keys( (array) $formData ) ) );
        error_log( '[ALR] Conteúdo de $formData: ' . print_r( $formData, true ) );
        return;
    }

    error_log( '[ALR] E-mail encontrado: ' . $email );

    wp_schedule_single_event( time() + 5, ALR_CRON_HOOK, [ $email, (int) $entryId ] );

    error_log( '[ALR] Evento agendado para 30s — E-mail: ' . $email . ' | Entry: ' . $entryId );
}

// =============================================================================
// PASSO 3 — PROCESSA O RODÍZIO (WP-CRON, 30s depois)
// =============================================================================

add_action( ALR_CRON_HOOK, 'alr_process_rotation', 10, 2 );

function alr_process_rotation( $email, $entryId ) {

    error_log( '[ALR] Cron executando — E-mail: ' . $email . ' | Entry: ' . $entryId );

    if ( ! function_exists( 'FluentCrmApi' ) ) {
        error_log( '[ALR] ERRO: FluentCrmApi() não disponível no momento do cron.' );
        return;
    }

    $contact = FluentCrmApi( 'contacts' )->getContact( $email );

    if ( ! $contact ) {
        error_log( '[ALR] ERRO: contato não encontrado no FluentCRM para ' . $email );
        return;
    }

    error_log( '[ALR] Contato encontrado — ID: ' . $contact->id );

    // Determina o próximo SDR
    $sdrs       = alr_get_sdrs();
    $last_index = (int) get_option( ALR_OPTION_KEY, -1 );
    $next_index = ( $last_index + 1 ) % count( $sdrs );
    $sdr        = $sdrs[ $next_index ];

    error_log( '[ALR] SDR selecionado: ' . $sdr['name'] . ' (tag_id: ' . $sdr['tag_id'] . ')' );

    // Aplica a tag
    try {
        $contact->attachTags( [ (int) $sdr['tag_id'] ] );
    } catch ( Exception $e ) {
        error_log( '[ALR] ERRO ao aplicar tag: ' . $e->getMessage() );
        return;
    }

    error_log( '[ALR] Tag aplicada com sucesso — SDR: ' . $sdr['name'] );

    // Só atualiza o índice após sucesso
    update_option( ALR_OPTION_KEY, $next_index, 'no' );

    error_log( '[ALR] Índice salvo: ' . $next_index . ' | Próximo: ' . $sdrs[ ( $next_index + 1 ) % count( $sdrs ) ]['name'] );
}

// =============================================================================
// FUNÇÃO AUXILIAR — EXTRAI E-MAIL DO $formData
// =============================================================================

function alr_extract_email( $formData ) {
    $data = (array) $formData;

    foreach ( [ 'email', 'Email', 'EMAIL', 'your-email', 'your_email', 'e-mail', 'mail' ] as $key ) {
        if ( ! empty( $data[ $key ] ) && is_string( $data[ $key ] ) ) {
            $candidate = sanitize_email( trim( $data[ $key ] ) );
            if ( is_email( $candidate ) ) return $candidate;
        }
    }

    // Fallback: varre todos os valores
    foreach ( $data as $value ) {
        if ( is_string( $value ) ) {
            $candidate = sanitize_email( trim( $value ) );
            if ( is_email( $candidate ) ) return $candidate;
        }
    }

    return '';
}

// =============================================================================
// PAINEL — CONFIGURAÇÕES > LEAD ROTATION
// =============================================================================

add_action( 'admin_menu', function() {
    add_options_page( 'Lead Rotation', 'Lead Rotation', 'manage_options', 'alr-status', 'alr_admin_page' );
} );

function alr_admin_page() {
    $sdrs       = alr_get_sdrs();
    $last_index = (int) get_option( ALR_OPTION_KEY, -1 );
    $next_index = ( $last_index + 1 ) % count( $sdrs );

    if ( isset( $_POST['alr_reset'] ) && check_admin_referer( 'alr_reset' ) ) {
        update_option( ALR_OPTION_KEY, -1, 'no' );
        $last_index = -1;
        $next_index = 0;
        echo '<div class="notice notice-success"><p>Rodízio resetado. Próximo: <strong>' . esc_html( $sdrs[0]['name'] ) . '</strong>.</p></div>';
    }

    $last_name = $last_index === -1 ? '—' : $sdrs[ $last_index ]['name'];
    $next_name = $sdrs[ $next_index ]['name'];

    // Verifica eventos pendentes no cron
    $crons  = _get_cron_array();
    $events = [];
    if ( $crons ) {
        foreach ( $crons as $timestamp => $hooks ) {
            if ( isset( $hooks[ ALR_CRON_HOOK ] ) ) {
                foreach ( $hooks[ ALR_CRON_HOOK ] as $data ) {
                    $args     = isset( $data['args'] ) ? $data['args'] : [];
                    $events[] = date( 'd/m H:i:s', $timestamp ) . ' → ' . esc_html( implode( ', ', $args ) );
                }
            }
        }
    }
    ?>
    <div class="wrap">
        <h1>Lead Rotation</h1>

        <table class="widefat fixed" style="max-width:420px;margin-top:16px;">
            <tbody>
                <tr>
                    <td><strong>Último SDR usado</strong></td>
                    <td><?php echo esc_html( $last_name ); ?></td>
                </tr>
                <tr style="background:#fffbdd;">
                    <td><strong>Próximo SDR</strong></td>
                    <td><strong><?php echo esc_html( $next_name ); ?></strong></td>
                </tr>
                <tr>
                    <td><strong>Eventos agendados</strong></td>
                    <td><?php echo $events ? implode( '<br>', $events ) : '—'; ?></td>
                </tr>
            </tbody>
        </table>

        <form method="post" style="margin-top:16px;">
            <?php wp_nonce_field( 'alr_reset' ); ?>
            <input type="hidden" name="alr_reset" value="1">
            <?php submit_button( 'Resetar rodízio', 'secondary', 'submit', false ); ?>
        </form>
    </div>
    <?php
}
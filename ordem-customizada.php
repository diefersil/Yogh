<?php

// Adiciona o status 'Pedido Enviado' aos Pedidos.
add_action( 'init', 'register_custom_post_status', 20 );
function register_custom_post_status() {
    register_post_status( 'pedido-enviado', array(
        'label'                     => _x( 'Pedido Enviado', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Pedido Enviado <span class="count">(%s)</span>', 'Pedido Enviado <span class="count">(%s)</span>', 'woocommerce' )
    ) );
}

// // Adiciona o status 'Pedido Enviado' ao -Menu Drop Down- na -Página de Edição do Pedido-.
add_filter( 'wc_order_statuses', 'custom_wc_order_statuses', 20, 1 );
function custom_wc_order_statuses( $order_statuses ) {
    $order_statuses['pedido-enviado'] = _x( 'Pedido Enviado', 'Order status', 'woocommerce' );
    return $order_statuses;
}

// Adiciona o status 'Pedido Enviado' na parte de Administrador, na lista -Drop Down- para 'Alteração em Massa'
add_filter( 'bulk_actions-edit-shop_order', 'custom_dropdown_bulk_actions_shop_order', 20, 1 );
function custom_dropdown_bulk_actions_shop_order( $actions ) {
    $actions['definir_pedido_enviado'] = __( 'Pedido Enviado', 'woocommerce' );
    return $actions;
}

// Cria a Ação para Enviar o email após alteração do status do pedido para 'Pedido Enviado'
add_filter( 'woocommerce_email_actions', 'custom_email_actions', 20, 1 );
function custom_email_actions( $action ) {
    $actions[] = 'woocommerce_order_status_pedido-enviado';
    return $actions; 
}

add_action( 'woocommerce_order_status_wc-pedido-enviado', array( WC(), 'send_transactional_email' ), 10, 1 );

// Enviando o email para o cliente com o status 'pedido-enviado' 
add_action('woocommerce_order_status_pedido-enviado', 'backorder_status_custom_notification', 20, 2);
function backorder_status_custom_notification( $order_id, $order ) {
    // HERE below your settings
    $heading   = __('Seu Pedido foi Enviado!','woocommerce');
    $subject   = '[{site_title}] Pedido Enviado order({order_number}) - {order_date}';

    // Configurações da Mensagem
    $mailer = WC()->mailer()->get_emails();

    // Customizando os Cabeçalhos 
    $mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
    $mailer['WC_Email_Customer_Processing_Order']->subject = $subject;

    // Enviando o Email
    $mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );
}
    
?>
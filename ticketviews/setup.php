<?php
function plugin_init_ticketviews() {
    global $PLUGIN_HOOKS;
    
    $PLUGIN_HOOKS['csrf_compliant']['ticketviews'] = true;
    $PLUGIN_HOOKS['add_javascript']['ticketviews'] = 'js/ticketviews.js';
    $PLUGIN_HOOKS['add_css']['ticketviews'] = 'css/ticketviews.css';
    
    // Hook para aba principal do ticket
    $PLUGIN_HOOKS['post_item_form']['ticketviews'] = 'plugin_ticketviews_post_item_form';
    
    // Hook para a aba de visualizadores
    Plugin::registerClass('PluginTicketviewsTicket', [
        'addtabon' => ['Ticket']
    ]);
}

// Função que será chamada após o formulário principal do ticket
function plugin_ticketviews_post_item_form(array $params) {
    if (isset($params['item']) && $params['item'] instanceof Ticket) {
        global $DB;
        
        $tickets_id = $params['item']->getID();
        $current_user = Session::getLoginUserID();
        $current_date = date('Y-m-d H:i:s');
        
        // Registra a visualização
        $query = "INSERT INTO glpi_plugin_ticketviews_viewers 
                  (tickets_id, users_id, view_date) 
                  VALUES ($tickets_id, $current_user, '$current_date')
                  ON DUPLICATE KEY UPDATE view_date = '$current_date'";
                  
        $DB->query($query);
        
        // Adiciona JavaScript para atualizar o contador
        echo "<script type='text/javascript'>
            $(document).ready(function() {
                function updateViewersCount() {
                    $.ajax({
                        url: '" . Plugin::getWebDir('ticketviews') . "/ajax/get_viewers_count.php',
                        type: 'GET',
                        data: {
                            tickets_id: $tickets_id
                        },
                        success: function(response) {
                            if (response.success) {
                                $('li a').each(function() {
                                    if ($(this).text().includes('Visualizadores')) {
                                        $(this).find('.badge').remove();
                                        $(this).append('<span class=\"badge bg-secondary ms-2\">' + response.count + '</span>');
                                    }
                                });
                            }
                        }
                    });
                }
                
                // Atualiza imediatamente e a cada 15 segundos
                updateViewersCount();
                setInterval(updateViewersCount, 15000);
            });
        </script>";
    }
}

function plugin_version_ticketviews() {
    return [
        'name' => 'Contador de visualizações do Ticket',
        'version' => '1.0.0',
        'author' => 'Adriano Marinho',
        'license' => 'GPLv2+',
        'homepage' => 'https://github.com/malakaygames',
        'minGlpiVersion' => '9.5'
    ];
}

function plugin_ticketviews_check_prerequisites() {
    if (version_compare(GLPI_VERSION, '9.5', 'lt')) {
        return false;
    }
    return true;
}

function plugin_ticketviews_check_config() {
    return true;
}
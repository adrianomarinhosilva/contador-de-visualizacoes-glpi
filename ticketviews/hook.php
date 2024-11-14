<?php
function plugin_ticketviews_install() {
    global $DB;
    
    if (!$DB->tableExists("glpi_plugin_ticketviews_viewers")) {
        $query = "CREATE TABLE IF NOT EXISTS `glpi_plugin_ticketviews_viewers` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `tickets_id` int(11) NOT NULL,
            `users_id` int(11) NOT NULL,
            `view_date` datetime NOT NULL,
            UNIQUE KEY `ticket_user` (`tickets_id`, `users_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $DB->query($query) or die($DB->error());
    }
    return true;
}

function plugin_ticketviews_uninstall() {
    global $DB;
    $DB->query("DROP TABLE IF EXISTS `glpi_plugin_ticketviews_viewers`");
    return true;
}

function plugin_ticketviews_display_content(CommonGLPI $item) {
    // Verifica se é um ticket
    if ($item->getType() !== 'Ticket') {
        return;
    }

    $tickets_id = $item->getID();
    
    // Registra a visualização do usuário atual
    global $DB;
    $current_user = Session::getLoginUserID();
    $current_date = date('Y-m-d H:i:s');
    
    $query = "INSERT INTO glpi_plugin_ticketviews_viewers 
              (tickets_id, users_id, view_date) 
              VALUES ($tickets_id, $current_user, '$current_date')
              ON DUPLICATE KEY UPDATE view_date = '$current_date'";
              
    $DB->query($query);
    
    // Busca todos os visualizadores
    $query = "SELECT v.*, u.realname, u.firstname 
             FROM glpi_plugin_ticketviews_viewers v
             LEFT JOIN glpi_users u ON u.id = v.users_id
             WHERE v.tickets_id = $tickets_id
             ORDER BY v.view_date DESC";
             
    $result = $DB->query($query);
    
    // Exibe o box com os visualizadores
    echo "<div class='tab_cadre_fixe'>";
    echo "<div style='background-color: #f8f9fa; padding: 10px; margin-bottom: 10px; border: 1px solid #dee2e6; border-radius: 4px;'>";
    echo "<h4 style='margin: 0 0 10px 0;'><i class='fa fa-eye'></i> Visualizações do Chamado</h4>";
    
    // Lista os visualizadores
    while ($data = $DB->fetchAssoc($result)) {
        $user_name = $data['firstname'] . ' ' . $data['realname'];
        $view_date = Html::convDateTime($data['view_date']);
        
        echo "<div style='margin: 5px 0; padding: 5px; background-color: white; border-radius: 3px;'>";
        echo "<i class='fa fa-user'></i> <strong>$user_name</strong>";
        echo "<span style='float: right; color: #666;'>$view_date</span>";
        echo "</div>";
    }
    
    echo "</div>";
    echo "</div>";
}
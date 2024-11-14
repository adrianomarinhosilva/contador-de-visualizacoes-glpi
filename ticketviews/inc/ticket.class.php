<?php
// Diretório: ticketviews/inc/ticket.class.php

class PluginTicketviewsTicket extends CommonDBTM {
    
    public static function getTypeName($nb = 0) {
        return __('Visualizadores', 'ticketviews');
    }
    
    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        if ($item->getType() == 'Ticket') {
            global $DB;
            
            $tickets_id = $item->getID();
            $query = "SELECT COUNT(DISTINCT users_id) as total 
                     FROM glpi_plugin_ticketviews_viewers 
                     WHERE tickets_id = $tickets_id";
            
            $result = $DB->query($query);
            $count = $DB->result($result, 0, 'total');
            
            return self::createTabEntry(
                __('Visualizadores', 'ticketviews'),
                $count
            );
        }
        return '';
    }
    
    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        if ($item->getType() == 'Ticket') {
            global $DB;
            
            // Registra a visualização imediatamente
            $tickets_id = $item->getID();
            $current_user = Session::getLoginUserID();
            $current_date = date('Y-m-d H:i:s');
            
            $query = "INSERT INTO glpi_plugin_ticketviews_viewers 
                    (tickets_id, users_id, view_date) 
                    VALUES ($tickets_id, $current_user, '$current_date')
                    ON DUPLICATE KEY UPDATE view_date = '$current_date'";
            
            $DB->query($query);
            
            self::showForTicket($item);
        }
        return true;
    }
    
    static function showForTicket(Ticket $ticket) {
        global $DB;
        
        $tickets_id = $ticket->getID();
        
        echo "<div class='tab_cadre_fixe'>";
        echo "<h3><i class='ti ti-users me-2'></i>" . __('Usuários que visualizaram este chamado') . "</h3>";
        
        // Mostra o criador primeiro
        $ticket_creator = new User();
        $ticket_creator->getFromDB($ticket->fields['users_id_recipient']);
        
        echo "<div class='alert alert-info mb-3'>";
        echo "<i class='ti ti-user-check me-2'></i><strong>" . __('Criador do Chamado') . ":</strong> ";
        echo $ticket_creator->getFriendlyName();
        echo " <small class='text-muted'>(" . Html::convDateTime($ticket->fields['date_creation']) . ")</small>";
        echo "</div>";
        
        // Busca todos os visualizadores
        $query = "SELECT DISTINCT v.users_id, v.view_date, u.realname, u.firstname, u.name as username
                 FROM glpi_plugin_ticketviews_viewers v
                 LEFT JOIN glpi_users u ON u.id = v.users_id
                 WHERE v.tickets_id = $tickets_id
                 ORDER BY v.view_date DESC";
                 
        $result = $DB->query($query);
        
        if ($DB->numrows($result) > 0) {
            echo "<table class='tab_cadre_fixehov'>";
            echo "<tr class='noHover'>";
            echo "<th>" . __('USUÁRIO') . "</th>";
            echo "<th>" . __('DATA DA VISUALIZAÇÃO') . "</th>";
            echo "</tr>";
            
            while ($data = $DB->fetchAssoc($result)) {
                $row_class = ($data['users_id'] == Session::getLoginUserID()) ? ' class="tab_bg_2"' : '';
                echo "<tr$row_class>";
                echo "<td><i class='ti ti-user me-2'></i>" . $data['firstname'] . " " . $data['realname'] . " (@" . $data['username'] . ")</td>";
                echo "<td>" . Html::convDateTime($data['view_date']) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<div class='alert alert-warning'>";
            echo "<i class='ti ti-alert-circle me-2'></i>" . __('Nenhuma visualização registrada além do criador.');
            echo "</div>";
        }
        
        echo "</div>";
    }
}
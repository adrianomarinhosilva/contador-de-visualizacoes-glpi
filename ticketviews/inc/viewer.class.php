// inc/viewer.class.php
// Diretório: ticketviews/inc/viewer.class.php
<?php
class PluginTicketviewsViewer extends CommonDBTM {
    
    public static function addView($tickets_id, $users_id) {
        global $DB;
        
        $current_date = date('Y-m-d H:i:s');
        
        $query = "INSERT INTO glpi_plugin_ticketviews_viewers 
                 (tickets_id, users_id, view_date) 
                 VALUES ($tickets_id, $users_id, '$current_date')
                 ON DUPLICATE KEY UPDATE view_date = '$current_date'";
                 
        $DB->query($query);
    }
    
    public static function getViewers($tickets_id) {
        global $DB;
        
        $query = "SELECT v.*, u.realname, u.firstname 
                 FROM glpi_plugin_ticketviews_viewers v
                 LEFT JOIN glpi_users u ON u.id = v.users_id
                 WHERE v.tickets_id = $tickets_id
                 ORDER BY v.view_date DESC";
                 
        $result = $DB->query($query);
        $viewers = [];
        
        while ($data = $DB->fetchAssoc($result)) {
            $viewers[] = [
                'name' => $data['firstname'] . ' ' . $data['realname'],
                'date' => $data['view_date']
            ];
        }
        
        return $viewers;
    }
}
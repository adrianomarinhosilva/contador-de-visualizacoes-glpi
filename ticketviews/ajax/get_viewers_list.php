<?php
// Diretório: ticketviews/ajax/get_viewers_list.php

include ('../../../inc/includes.php');
header('Content-Type: application/json');

// Verifica se tem permissão
Session::checkLoginUser();

if (!isset($_GET['tickets_id'])) {
    die(json_encode(['success' => false, 'error' => 'No ticket ID provided']));
}

$tickets_id = intval($_GET['tickets_id']);

// Verifica se o usuário tem permissão para ver este ticket
$ticket = new Ticket();
if (!$ticket->can($tickets_id, READ)) {
    die(json_encode(['success' => false, 'error' => 'Permission denied']));
}

// Busca o ticket para mostrar o criador
$ticket->getFromDB($tickets_id);

// Início do HTML
$html = "<h3><i class='ti ti-users me-2'></i>" . __('Usuários que visualizaram este chamado') . "</h3>";

// Mostra o criador primeiro
$ticket_creator = new User();
$ticket_creator->getFromDB($ticket->fields['users_id_recipient']);

$html .= "<div class='alert alert-info mb-3'>";
$html .= "<i class='ti ti-user-check me-2'></i><strong>" . __('Criador do Chamado') . ":</strong> ";
$html .= $ticket_creator->getFriendlyName();
$html .= " <small class='text-muted'>(" . Html::convDateTime($ticket->fields['date_creation']) . ")</small>";
$html .= "</div>";

// Busca todos os visualizadores
global $DB;
$query = "SELECT DISTINCT v.users_id, v.view_date, u.realname, u.firstname, u.name as username,
                (SELECT COUNT(*) FROM glpi_plugin_ticketviews_viewers v2 
                 WHERE v2.tickets_id = v.tickets_id AND v2.users_id = v.users_id) as view_count
         FROM glpi_plugin_ticketviews_viewers v
         LEFT JOIN glpi_users u ON u.id = v.users_id
         WHERE v.tickets_id = $tickets_id
         ORDER BY v.view_date DESC";
         
$result = $DB->query($query);

if ($DB->numrows($result) > 0) {
    $html .= "<table class='tab_cadre_fixehov'>";
    $html .= "<tr class='noHover'>";
    $html .= "<th>" . __('USUÁRIO') . "</th>";
    $html .= "<th>" . __('DATA DA VISUALIZAÇÃO') . "</th>";
    $html .= "<th>" . __('NÚMERO DE VISUALIZAÇÕES') . "</th>";
    $html .= "</tr>";
    
    while ($data = $DB->fetchAssoc($result)) {
        $row_class = ($data['users_id'] == Session::getLoginUserID()) ? ' class="tab_bg_2"' : '';
        $html .= "<tr$row_class>";
        $html .= "<td><i class='ti ti-user me-2'></i>" . $data['firstname'] . " " . $data['realname'] . " (@" . $data['username'] . ")</td>";
        $html .= "<td>" . Html::convDateTime($data['view_date']) . "</td>";
        $html .= "<td><span class='badge bg-info'>" . $data['view_count'] . "</span></td>";
        $html .= "</tr>";
    }
    
    $html .= "</table>";
} else {
    $html .= "<div class='alert alert-warning'>";
    $html .= "<i class='ti ti-alert-circle me-2'></i>" . __('Nenhuma visualização registrada além do criador.');
    $html .= "</div>";
}

echo json_encode([
    'success' => true,
    'html' => $html
]);
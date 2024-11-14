<?php
include ('../../../inc/includes.php');
header('Content-Type: application/json');

// Verifica se tem permissão
Session::checkLoginUser();

if (!isset($_POST['tickets_id'])) {
    die(json_encode(['success' => false, 'error' => 'No ticket ID provided']));
}

$tickets_id = intval($_POST['tickets_id']);

// Verifica se o usuário tem permissão para ver este ticket
$ticket = new Ticket();
if (!$ticket->can($tickets_id, READ)) {
    die(json_encode(['success' => false, 'error' => 'Permission denied']));
}

// Registra a visualização
global $DB;
$current_user = Session::getLoginUserID();
$current_date = date('Y-m-d H:i:s');

$query = "INSERT INTO glpi_plugin_ticketviews_viewers 
          (tickets_id, users_id, view_date) 
          VALUES ($tickets_id, $current_user, '$current_date')
          ON DUPLICATE KEY UPDATE view_date = '$current_date'";

$DB->query($query);

// Retorna o novo número de visualizadores
$query = "SELECT COUNT(DISTINCT users_id) as total 
          FROM glpi_plugin_ticketviews_viewers 
          WHERE tickets_id = $tickets_id";

$result = $DB->query($query);
$count = $DB->result($result, 0, 'total');

echo json_encode([
    'success' => true,
    'count' => $count
]);
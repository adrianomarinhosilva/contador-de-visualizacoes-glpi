<?php
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

// Busca o número atual de visualizadores
global $DB;
$query = "SELECT COUNT(DISTINCT users_id) as total 
          FROM glpi_plugin_ticketviews_viewers 
          WHERE tickets_id = $tickets_id";

$result = $DB->query($query);
$count = $DB->result($result, 0, 'total');

echo json_encode([
    'success' => true,
    'count' => $count
]);
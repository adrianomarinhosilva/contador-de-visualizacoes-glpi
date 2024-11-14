// front/ajax/save_view.php
// Diretório: ticketviews/front/ajax/save_view.php
<?php
include ('../../../inc/includes.php');

Header('Content-Type: application/json');

if (!isset($_POST['tickets_id'])) {
    http_response_code(400);
    die();
}

$tickets_id = intval($_POST['tickets_id']);
$users_id = Session::getLoginUserID();

$viewer = new PluginTicketviewsViewer();
$viewer->addView($tickets_id, $users_id);

echo json_encode(['success' => true]);
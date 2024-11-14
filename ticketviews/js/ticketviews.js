$(document).ready(function() {
    // Verifica se estamos em uma página de ticket
    if (location.pathname.includes('ticket.form.php')) {
        const tickets_id = getUrlParameter('id');
        if (tickets_id) {
            // Atualiza o contador a cada 15 segundos
            setInterval(function() {
                updateViewersCount(tickets_id);
            }, 15000);
        }
    }

    function updateViewersCount(tickets_id) {
        $.ajax({
            url: CFG_GLPI.root_doc + '/plugins/ticketviews/ajax/get_viewers_count.php',
            type: 'GET',
            data: {
                tickets_id: tickets_id
            },
            success: function(response) {
                if (response.success) {
                    $('li a').each(function() {
                        if ($(this).text().includes('Visualizadores')) {
                            $(this).find('.badge').remove();
                            $(this).append(`<span class="badge bg-secondary ms-2">${response.count}</span>`);
                        }
                    });
                }
            }
        });
    }

    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
});
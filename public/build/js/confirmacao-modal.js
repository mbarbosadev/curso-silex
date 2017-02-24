$('#confirmacao').on('show.bs.modal', function (event) {
	
	var botao = $(event.relatedTarget);
	var title = botao.data('title');
	var url = botao.data('url');
	
	var modal = $(this);
	var form = modal.find('form');
	var url = form.attr('action', url);
	modal.find('.modal-body p').html('Tem ceteza que deseja excluir strong>' + title + '</strong>?');
});
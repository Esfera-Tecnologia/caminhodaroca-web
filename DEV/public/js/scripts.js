$(document).ready(function () {

  //Dashboard
  $('#menu-toggle').click(function () {
    $('#sidebar-wrapper').toggleClass('d-none');
  });

  // Garante que no resize o menu volte ao estado correto
  $(window).on('resize', function () {
    if ($(window).width() >= 992) {
      $('#sidebar-wrapper').removeClass('d-none');
    } else {
      $('#sidebar-wrapper').addClass('d-none');
    }
  });

   // aciona modal de exclusão 

    $('.btn-delete').on('click', function () {
      const route = $(this).data('route');
      $('#deleteForm').attr('action', route);
    });


  // Alertas temporários
    setTimeout(function () {
        $('.alert').fadeOut(500, function () {
            $(this).remove();
        });
    }, 3500);


  // inicaliza todos os datatables
    $('.datatable').each(function () {
      if (!$.fn.DataTable.isDataTable(this)) {
        $(this).DataTable({
          language: {
           url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
          }
        });
      }
    });

//inicializa os tootlips
   $('[data-bs-toggle="tooltip"]').each(function () {
    new bootstrap.Tooltip(this, {
      delay: { show: 0, hide: 0 }, // mostra imediatamente
      trigger: 'hover focus'
    });
  });

  //mascara de whats
   $('.telefone').mask('(00) 00000-0000');

   //inicia select2
  $('.select2').each(function () {
      const $this = $(this);
      if ($this.hasClass('select2-hidden-accessible')) {
        $this.select2('destroy');
      }

      setTimeout(() => {
        $this.select2({
          theme: 'bootstrap-5',
          width: '100%',
          language: 'pt-BR'
        });

        // Aplica borda e altura padrão do Bootstrap
        $this.next('.select2-container').find('.select2-selection').addClass('form-select');
      }, 10);
    });
});

function adicionarCategoria() {
  const select = $('#category_id');
  const categoriaId = select.val();
  const categoriaNome = select.find('option:selected').text();
  const categoriaHtmlId = 'categoria_' + categoriaId;

  if (!categoriaId) {
    alert('Por favor, selecione uma categoria válida.');
    return;
  }

  if ($('#' + categoriaHtmlId).length > 0) return;

  // Cria o bloco container
  const $col = $('<div>', { class: 'col-md-6', id: categoriaHtmlId });
  const $block = $('<div>', { class: 'categoria-block border p-3 h-100' });

  $block.append(`
    <div class="d-flex justify-content-between">
      <strong>${categoriaNome}</strong>
      <button type="button" class="btn btn-sm btn-outline-danger">Remover</button>
    </div>
  `);

  // Evento remover
  $block.find('button').on('click', () => $col.remove());

  const subcategorias = window.subcategoriasPorCategoria?.[categoriaId] || [];

  if (subcategorias.length > 0) {
    subcategorias.forEach(sub => {
      $block.append(`
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox"
                 name="categoria_ids[${categoriaId}][]" value="${sub.id}"
                 id="sub${sub.id}-${categoriaId}">
          <label class="form-check-label" for="sub${sub.id}-${categoriaId}">
            ${sub.nome}
          </label>
        </div>
      `);
    });
  } else {
    // Exibe mensagem se não houver subcategorias
    $block.append(`
      <div class="text-muted mt-2 small">
        Essa categoria não possui subcategorias.
      </div>
    `);
    // Cria input hidden para enviar valor da categoria sem subcategoria
    $block.append(`
      <input type="hidden" name="categoria_ids[${categoriaId}][]" value="">
    `);
  }

  $col.append($block);
  $('#categorias-container').append($col);
   $('#category_id').val('');
}


 
function toggleProdutosArtesanais() {
  const val = $('#vende-produtos-artesanais').val();
  if (val === '1') {
    $('#produtos-artesanais').slideDown();
  } else {
    $('#produtos-artesanais').slideUp();
    $('#produtos-artesanais input[type=checkbox]').prop('checked', false);
  }
}

$(document).on('change', '#vende-produtos-artesanais', toggleProdutosArtesanais);
$(document).ready(toggleProdutosArtesanais);


function gerarAgendaSemanal() {
  const dias = [
    'segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'
  ];

  const container = $('#agendaSemanal');
  container.empty();

  const agenda = window.agendaPersonalizada || {};

  dias.forEach((dia) => {
    const dados = agenda[dia] || {};
    const ativo = dados.ativo === 1 || dados.ativo === true || dados.ativo === '1';
    const fechaAlmoco = dados.fecha_almoco === 1 || dados.fecha_almoco === true || dados.fecha_almoco === '1';

    const bloco = `
      <div class="day-block border rounded p-3 mb-3" data-dia="${dia}">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <strong>${dia.charAt(0).toUpperCase() + dia.slice(1)}</strong>
          <div>
            <input type="hidden" name="agenda_personalizada[${dia}][ativo]" value="0">
            <input type="checkbox" class="form-check-input me-1 ativar-dia"
              id="check_${dia}" name="agenda_personalizada[${dia}][ativo]" value="1"
              ${ativo ? 'checked' : ''}>
            <label for="check_${dia}" class="form-check-label">Ativo</label>
          </div>
        </div>

        <input type="hidden" name="agenda_personalizada[${dia}][dia]" value="${dia}">

        <div class="row g-2">
          <div class="col-6">
            <label class="form-label">Abertura</label>
            <input type="time" class="form-control horario-abertura"
              name="agenda_personalizada[${dia}][abertura]" value="${dados.abertura || ''}">
          </div>
          <div class="col-6">
            <label class="form-label">Fechamento</label>
            <input type="time" class="form-control horario-fechamento"
              name="agenda_personalizada[${dia}][fechamento]" value="${dados.fechamento || ''}">
          </div>
        </div>

        <div class="form-check mt-2">
          <input type="hidden" name="agenda_personalizada[${dia}][fecha_almoco]" value="0">
          <input type="checkbox" class="form-check-input fechar-almoco"
            id="almoco_${dia}" name="agenda_personalizada[${dia}][fecha_almoco]" value="1"
            ${fechaAlmoco ? 'checked' : ''}>
          <label class="form-check-label" for="almoco_${dia}">Fecha no almoço</label>
        </div>
      </div>
    `;

    container.append(bloco);
  });

  controlarInputsPorDia(); // Desabilita campos se o checkbox "Ativo" estiver desmarcado
}




$(document).ready(() => {
  gerarAgendaSemanal();

  $('input[name="tipo_funcionamento"]').on('change', function () {
    const tipo = $(this).val();
    $('#horarioTexto').toggleClass('d-none', !['feriados','agendamento','personalizado'].includes(tipo));
  });
});


function ajustarHorarioFuncionamento() {
  const tipo = $('input[name="tipo_funcionamento"]:checked').val();

  if (tipo === 'todos' || tipo === 'fins') {
    $('#agendaSemanal').removeClass('d-none');
    $('#horarioTexto').addClass('d-none');
    gerarAgendaSemanal();

    if (tipo === 'fins') {
      const diasUteis = ['segunda','terça','quarta','quinta','sexta'];

      diasUteis.forEach(function(dia) {
        const bloco = $(`.day-block[data-dia="${dia}"]`);
        bloco.find('.ativar-dia').prop('checked', false).prop('disabled', true);
        bloco.find('input[type="time"]').val('');
        bloco.find('.fechar-almoco').prop('checked', false);
      });
    }

  } else {
    // Tipos de texto livre: feriados, agendamento, personalizado
    $('#agendaSemanal').addClass('d-none');
    $('#horarioTexto').removeClass('d-none');
    $('#horarioTexto textarea').val('');

    // ⚠️ Limpa todos os campos de horário e checkboxes da agenda
    $('.day-block').each(function () {
      const bloco = $(this);
      bloco.find('input[type="time"]').val('');
      bloco.find('.fechar-almoco').prop('checked', false);
      bloco.find('.ativar-dia').prop('checked', false).prop('disabled', false);
    });
  }
}


$('#form-propriedade').on('submit', function (e) {
  const tipo = $('input[name="tipo_funcionamento"]:checked').val();

  if (tipo === 'todos' || tipo === 'fins') {
    let algumValido = false;

    $('.day-block').each(function () {
      const ativo = $(this).find('.ativar-dia').is(':checked');
      const abertura = $(this).find('input[type="time"][name*="[abertura]"]').val();
      const fechamento = $(this).find('input[type="time"][name*="[fechamento]"]').val();

      if (ativo && abertura && fechamento) {
        algumValido = true;
      }
    });

    if (!algumValido) {
      e.preventDefault();
      const modal = new bootstrap.Modal(document.getElementById('alertHorarioModal'));
      modal.show();
      return false;
    }
  }
});




// Aplica ao carregar e ao trocar opção
$(document).ready(() => {
  ajustarHorarioFuncionamento();
    // Evento de mudança de tipo de funcionamento
    $('input[name="tipo_funcionamento"]').on('change', function () {
      const tipo = $(this).val();
      
      if (['feriados', 'agendamento', 'personalizado'].includes(tipo)) {
        $('#horarioTexto').removeClass('d-none');
        $('#agendaSemanal').addClass('d-none');
        $('#horarioTexto textarea').val(''); // limpa campo texto
      } else {
        $('#agendaSemanal').removeClass('d-none');
        $('#horarioTexto').addClass('d-none');
      }

      ajustarHorarioFuncionamento(tipo);
    });
});

function controlarInputsPorDia() {
  $('.ativar-dia').each(function () {
    const isChecked = $(this).is(':checked');
    const bloco = $(this).closest('.day-block');

    bloco.find('input[type="time"], .fechar-almoco').prop('disabled', !isChecked);
  });
}

// Ativa a escuta para quando o checkbox "Ativo" de cada dia for alterado
$(document).on('change', '.ativar-dia', controlarInputsPorDia);

// Executa ao carregar a página
$(document).ready(controlarInputsPorDia);


function previewLogo(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => $('#preview-logo').attr('src', e.target.result);
    reader.readAsDataURL(input.files[0]);
  }
}
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

   $('.telefone').on('blur', function () {
    const val = $(this).val();

    if (val.length < 15 || val.includes('_')) {
      $(this).addClass('is-invalid');
      // Mensagem opcional
      if (!$('#erro-whatsapp').length) {
        $(this).after('<div id="erro-whatsapp" class="invalid-feedback d-block">Número incompleto</div>');
      }
    } else {
      $(this).removeClass('is-invalid');
      $('#erro-whatsapp').remove();
    }
  });

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

     $block.append(`
      <input type="hidden" class="subcategoria-hidden" name="categoria_ids[${categoriaId}][]" value="">
    `);

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
 $('#erro-categorias').remove();
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
    const abertura = dados.abertura || '';
    const fechamento = dados.fechamento || '';

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
              name="agenda_personalizada[${dia}][abertura]" value="${abertura}">
          </div>
          <div class="col-6">
            <label class="form-label">Fechamento</label>
            <input type="time" class="form-control horario-fechamento"
              name="agenda_personalizada[${dia}][fechamento]" value="${fechamento}">
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

  controlarInputsPorDia(); // Desabilita campos de dias inativos
}





$(document).ready(() => {
  gerarAgendaSemanal();

  $('input[name="tipo_funcionamento"]').on('change', function () {
    const tipo = $(this).val();
    $('#horarioTexto').toggleClass('d-none', !['feriados','agendamento','personalizado'].includes(tipo));
  });
});


function ajustarHorarioFuncionamento(tipoSelecionado = null) {
  const tipo = tipoSelecionado || $('input[name="tipo_funcionamento"]:checked').val();

  const diasUteis = ['segunda','terça','quarta','quinta','sexta'];
  const diasFinais = ['sábado', 'domingo'];
  const todosDias = [...diasUteis, ...diasFinais];

  //  Limpa agendas ou texto conforme o tipo atual
  if (['agendamento', 'personalizado', 'feriados'].includes(tipo)) {
    $('#agendaSemanal').addClass('d-none');
    $('#horarioTexto').removeClass('d-none');
    return;
  } else {
    $('#agendaSemanal').removeClass('d-none');
    $('#horarioTexto').addClass('d-none');

    // Se tipo é agendamento/personalizado → NÃO limpar!
    if (!['agendamento', 'personalizado'].includes(tipo)) {
      $('textarea[name="observacoes_funcionamento"]').val('');
    }

  }

  gerarAgendaSemanal();

  // 🧠 Regras específicas por tipo
  if (tipo === 'todos') {
    // Mantém tudo conforme estado do banco (ou default inativo, já tratado em gerarAgendaSemanal)
    controlarInputsPorDia(); // ativa/desativa campos com base no checkbox "Ativo"

  } else if (tipo === 'fins') {
    todosDias.forEach((dia) => {
      const bloco = $(`.day-block[data-dia="${dia}"]`);
      const isUtil = diasUteis.includes(dia);

      // Desativa e limpa dias úteis
      if (isUtil) {
        bloco.find('.ativar-dia').prop('checked', false).prop('disabled', true);
        bloco.find('input[type="time"]').val('').prop('disabled', true);
        bloco.find('.fechar-almoco').prop('checked', false).prop('disabled', true);
      } else {
        // Mantém finais de semana com base no estado atual (não força)
        bloco.find('.ativar-dia').prop('disabled', false);
      }
    });
  }
}




// Aplica ao carregar e ao trocar opção
$(document).ready(() => {
  ajustarHorarioFuncionamento();
    // Evento de mudança de tipo de funcionamento
    $('input[name="tipo_funcionamento"]').on('change', function () {
      const tipo = $(this).val();
      
      if (['feriados', 'agendamento', 'personalizado'].includes(tipo)) {
        $('#horarioTexto').removeClass('d-none');
        $('#agendaSemanal').addClass('d-none');
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




$('#form-propriedade').on('submit', function (e) {
  let temErro = false;
  const tipo = $('input[name="tipo_funcionamento"]:checked').val();

  // Validação: pelo menos uma categoria
  const temCategoria = $('#categorias-container .categoria-block').length > 0;
  $('#erro-categorias').remove();
  if (!temCategoria) {
    const alerta = `<div id="erro-categorias" class="invalid-feedback d-block" style="margin-top: -25px;">Você deve adicionar pelo menos uma categoria antes de salvar.</div>`;
    $('#categorias-container').before(alerta);
    temErro = true;
  }

  // Validação: campo obrigatório para tipos com texto
  $('#erro-observacoes').remove();
  if (['agendamento', 'personalizado'].includes(tipo)) {
    const texto = $('textarea[name="observacoes_funcionamento"]').val().trim();
    if (!texto) {
      const alerta = `<div id="erro-observacoes" class="invalid-feedback d-block">Informe os dias ou observações de funcionamento.</div>`;
      $('#horarioTexto').after(alerta);
      temErro = true;
    }
  } else {
    // Se não for tipo com texto, limpamos para evitar envio indevido
    $('textarea[name="observacoes_funcionamento"]').val('');
  }


  if (!['todos', 'fins'].includes(tipo)) {
    // Remove valores da agenda semanal desativando todos os dias
    $('.day-block').each(function () {
      const bloco = $(this);
      bloco.find('.ativar-dia').prop('checked', false);
      bloco.find('input[type="time"]').val('');
      bloco.find('.fechar-almoco').prop('checked', false);
    });
  }

  // Validação de horários obrigatórios para dias ativos
  $('.day-block').each(function () {
    const bloco = $(this);
    const dia = bloco.data('dia');
    const ativo = bloco.find('.ativar-dia').is(':checked');

    if (ativo) {
      const abertura = bloco.find('input[name="agenda_personalizada[' + dia + '][abertura]"]').val();
      const fechamento = bloco.find('input[name="agenda_personalizada[' + dia + '][fechamento]"]').val();

      if (!abertura || !fechamento) {
        temErro = true;

        //  Adiciona feedback visual
        if (!bloco.find('.erro-horario').length) {
          const alerta = `<div class="erro-horario invalid-feedback d-block mt-1">Informe os horários de abertura e fechamento.</div>`;
          bloco.append(alerta);
        }

        // Scroll até o erro (primeiro bloco com problema)
        $('html, body').animate({
          scrollTop: bloco.offset().top - 100
        }, 300);

        return false; // interrompe .each()
      }
    }
  });

  // Impede envio se houver erros
  if (temErro) {
    e.preventDefault();
    $('html, body').animate({
      scrollTop: $('.alert').first().offset().top - 100
    }, 500);
  }

});

// Remove erro de observação ao digitar
$('textarea[name="observacoes_funcionamento"]').on('input', function () {
  $('#erro-observacoes').remove();
});

$(document).on('input change', '.horario-abertura, .horario-fechamento, .ativar-dia', function () {
  $(this).closest('.day-block').find('.erro-horario').remove();
});


// Exibe alerta visual bootstrap
function showBootstrapAlert(message, type = 'danger') {
    const alertBox = document.getElementById('custom-alert');
    alertBox.className = `alert alert-${type} alert-dismissible fade show`;
    alertBox.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    alertBox.classList.remove('d-none');
}
// IMAGENS 

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

// Plugins
// Importante: registre os plugins
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType
);

// Inicialize com atenção ao name e input original
const inputElement = document.querySelector('#imageUploader');

const pond = FilePond.create(inputElement, {
    name: 'images[]', 
    allowMultiple: true,
    instantUpload: false,
    allowRemove: true,
    allowReorder: true,
    storeAsFile: true, 
    labelIdle: 'Arraste e solte as imagens ou <span class="filepond--label-action">clique para selecionar</span>'
});




$('.property-gallery').on('click', '.delete-image', function (event) {
    event.preventDefault();
    event.stopPropagation();

    const imageId = $(this).data('id');
    if (!imageId) {
        alert('ID da imagem não encontrado.');
        return;
    }

    if (confirm('Deseja excluir esta imagem?')) {
        $.ajax({
            url: `/property-images/${imageId}`,
            type: 'DELETE',
            success: function () {
                // Remove o bloco com data-id correspondente
                $(`.filepond--item[data-id="${imageId}"]`).remove();
            },
            error: function (xhr) {
                alert('Erro ao excluir imagem.');
                console.error(xhr.responseText);
            }
        });
    }
});

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


document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form').forEach(form => {

        const validator = new JustValidate(form, {
            validateBeforeSubmitting: true,
            focusInvalidField: false,
            lockForm: true,
            validateOnChange: true,
            errorLabelStyle: {
                color: '#dc3545',
                fontSize: '0.875rem',
                marginTop: '0.25rem',
                display: 'block'
            }
        });

        // Detecta se já existe logo carregada (edição)
        const previewLogo = document.querySelector('#preview-logo');
        const hasExistingLogo = previewLogo && previewLogo.src.includes('/storage/');

        // Valida todos os campos obrigatórios, ignorando plugins
        form.querySelectorAll('[required]').forEach(field => {
            // Ignora campos controlados por plugins ou inválidos
            if (
                !field.id ||
                field.disabled ||
                field.type === 'hidden' ||
                $(field).hasClass('select2-hidden-accessible') || // Select2
                field.closest('.filepond--root') // FilePond
            ) {
                return;
            }

            const rules = [
                { rule: 'required', errorMessage: 'Este campo é obrigatório.' }
            ];

            if (field.type === 'email') {
                rules.push({ rule: 'email', errorMessage: 'Informe um e-mail válido.' });
            }

            if (field.type === 'number') {
                rules.push({ rule: 'number', errorMessage: 'Informe um número válido.' });
            }

            validator.addField(`#${field.id}`, rules);
        });

        // Valida o grupo de checkboxes (products)
        const productCheckboxes = form.querySelectorAll('input[name="products[]"]');
        if (productCheckboxes.length) {
            validator.addField(productCheckboxes[0], [
                {
                    validator: () => {
                        return Array.from(productCheckboxes).some(cb => cb.checked);
                    },
                    errorMessage: 'Selecione pelo menos 1 produto.'
                }
            ]);
        }

        // Validação específica para o logo (só exige upload se não houver logo já salva)
        const logoInput = form.querySelector('#logo');
        if (logoInput) {
            validator.addField('#logo', [
                {
                    validator: () => {
                        if (hasExistingLogo) return true;
                        return logoInput.files.length >= 1;
                    },
                    errorMessage: 'Selecione a logo da propriedade.'
                },
                {
                    validator: () => logoInput.files.length <= 1,
                    errorMessage: 'Selecione apenas uma logo.'
                },
                {
                    validator: () => {
                        if (!logoInput.files.length) return true;
                        const file = logoInput.files[0];
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        return (
                            allowedTypes.includes(file.type) &&
                            file.size <= 2000000
                        );
                    },
                    errorMessage: 'A logo deve ser JPG, PNG ou GIF de até 2MB.'
                }
            ]);
        }


        // Callback final de submit
        validator.onSuccess((event) => {
          const form = event.target;
          const formId = form.getAttribute('id');
          let temErro = false;

          // Executa validações específicas por formulário
          switch (formId) {
            case 'form-propriedade':
              temErro = !validateFormPropriedade(form);
              break;

            // Exemplo: outros formulários podem ser adicionados aqui
            // case 'form-produto':
            //   temErro = !validateFormProduto(form);
            //   break;

            default:
              // Nenhuma validação extra, segue normalmente
              break;
          }

          if (!temErro) {
            form.submit();
          }
        });



        // Captura os campos que estão falhando
        validator.onFail((fields) => {
            console.warn('Campos inválidos:', fields);
        });

         // Garante que Select2 revalide ao alterar
        form.querySelectorAll('.select2').forEach(select => {
            $(select).on('change', function () {
                validator.revalidateField(`#${this.id}`);
            });
        });
    });
});


function validateFormPropriedade(form) {
  let isValid = true;
  const tipo = form.querySelector('input[name="tipo_funcionamento"]:checked')?.value || '';

  // Validação: Pelo menos uma categoria adicionada
  $('#erro-categorias').remove();
  const temCategoria = $('#categorias-container .categoria-block').length > 0;
  if (!temCategoria) {
    $('#categorias-container').before(`
      <div id="erro-categorias" class="invalid-feedback d-block" style="margin-top: -25px;">
        Você deve adicionar pelo menos uma categoria antes de salvar.
      </div>
    `);
    isValid = false;
  }

  // Validação: Observações obrigatórias
  $('#erro-observacoes').remove();
  if (['agendamento', 'personalizado'].includes(tipo)) {
    const texto = $('textarea[name="observacoes_funcionamento"]').val().trim();
    if (!texto) {
      $('#horarioTexto').after(`
        <div id="erro-observacoes" class="invalid-feedback d-block">
          Informe os dias ou observações de funcionamento.
        </div>
      `);
      isValid = false;
    }
  } else {
    $('textarea[name="observacoes_funcionamento"]').val('');
  }

  // Validação: horários por dia ativo
  $('.day-block').each(function () {
    const bloco = $(this);
    const dia = bloco.data('dia');
    const ativo = bloco.find('.ativar-dia').is(':checked');

    bloco.find('.erro-horario').remove();

    if (ativo) {
      const abertura = bloco.find(`input[name="agenda_personalizada[${dia}][abertura]"]`).val();
      const fechamento = bloco.find(`input[name="agenda_personalizada[${dia}][fechamento]"]`).val();

      if (!abertura || !fechamento) {
        bloco.append(`
          <div class="erro-horario invalid-feedback d-block mt-1">
            Informe os horários de abertura e fechamento.
          </div>
        `);
        $('html, body').animate({ scrollTop: bloco.offset().top - 100 }, 300);
        isValid = false;
        return false;
      }

      if (abertura >= fechamento) {
        bloco.append(`
          <div class="erro-horario invalid-feedback d-block mt-1">
            O horário de abertura deve ser anterior ao horário de fechamento.
          </div>
        `);
        $('html, body').animate({ scrollTop: bloco.offset().top - 100 }, 300);
        isValid = false;
        return false;
      }
    }
  });

  return isValid;
}

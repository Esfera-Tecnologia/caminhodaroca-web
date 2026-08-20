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
        $('.alert:not(.not-fade)').fadeOut(500, function () {
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

  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
  const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))


    var SPMaskBehavior = function(val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    };
    var spOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
    };
    $('.telefone').mask(SPMaskBehavior, spOptions);



   //inicia select2
  $('.select2').each(function () {
      const $this = $(this);

      setTimeout(() => {
        // Se já foi inicializado localmente (ex: com configurações de placeholder, closeOnSelect, etc.),
        // não sobrescrevemos a instância para não perder as configurações personalizadas.
        if (!$this.hasClass('select2-hidden-accessible')) {
          $this.select2({
            theme: 'bootstrap-5',
            width: '100%',
            language: {
              noResults: function () {
                return "Nenhum resultado encontrado";
              },
              searching: function () {
                return "Buscando...";
              },
              inputTooShort: function (args) {
                return "Digite " + (args.minimum - args.input.length) + " ou mais caracteres";
              }
            }
          });
        }

        // Aplica borda e altura padrão do Bootstrap (form-select para simples, form-control para múltiplos)
        if ($this.attr('multiple')) {
          $this.next('.select2-container').find('.select2-selection').addClass('form-control');
        } else {
          $this.next('.select2-container').find('.select2-selection').addClass('form-select');
        }
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

        // Expõe a instância do validador para validações dinâmicas em páginas específicas
        window.caminhoRocaValidators = window.caminhoRocaValidators || {};
        window.caminhoRocaValidators[form.id] = validator;

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
            if (field.name === 'latitude' || field.name === 'longitude') {
              rules.push(
                { 
                    validator: (value) => {
                        const num = parseFloat(value);
                        
                        if (field.name === 'latitude') {
                            return num >= -90 && num <= 90;
                        }

                        if (field.name === 'longitude') {
                            return num >= -180 && num <= 180;
                        }

                        return false;
                    },
                    errorMessage: field.name === 'latitude'
                        ? 'A latitude deve estar entre -90 e 90.'
                        : 'A longitude deve estar entre -180 e 180.'
                }
              );
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
          validateFormPropriedade();
        });

         // Garante que Select2 revalide ao alterar
        form.querySelectorAll('.select2').forEach(select => {
            $(select).on('change', function () {
                validator.revalidateField(`#${this.id}`);
            });
        });
    });
});


function validateFormPropriedade() {
  const form = $("#form-propriedade")[0];
  if(!form) {
    return;
  }
  let isValid = true;
  const tipo = form.querySelector('input[name="tipo_funcionamento"]:checked')?.value || '';

  // Validação: Pelo menos uma categoria adicionada
  $('#erro-categorias').remove();
  const temCategoria = $('#categorias-container .categoria-block').length > 0;
  if (!temCategoria) {
    $('#categorias-container').before(`
      <div id="erro-categorias" class="invalid-feedback d-block">
        Você deve adicionar pelo menos uma categoria antes de salvar.
      </div>
    `);
    isValid = false;
  }
  // Validação: Pelo menos uma foto adicionada
  $('#erro-fotos').remove();
  const temArquivos = $('#imageUploader', form)[0].files.length > 0;
  const temPreviews = $("#imagePreviewContainer .image-thumb").length > 0;
  if (!temArquivos && !temPreviews) {
    $('#uploadBox').after(`
      <div id="erro-fotos" class="invalid-feedback d-block">
        Você deve adicionar pelo menos uma foto antes de salvar.
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
      } else if (abertura >= fechamento) {
        bloco.append(`
          <div class="erro-horario invalid-feedback d-block mt-1">
            O horário de abertura deve ser anterior ao horário de fechamento.
          </div>
        `);
        $('html, body').animate({ scrollTop: bloco.offset().top - 100 }, 300);
        isValid = false;
      }
    }
  });

  return isValid;
}

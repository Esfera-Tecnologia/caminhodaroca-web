$(document).ready(function () {

//Inicio Login 
  $('#linkEsqueciSenha').on('click', function (e) {
    e.preventDefault();
    $('#loginForm').addClass('d-none');
    $('#recuperarSenhaForm').removeClass('d-none');
  });

  $('#loginForm').on('submit', function (e) {
    e.preventDefault();
    window.location.href = "dashboard.html";
  });

  $('#voltarLogin').on('click', function () {
    $('#recuperarSenhaForm').addClass('d-none');
    $('#loginForm').removeClass('d-none');
  });

  //fim Login 

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

  //geral 


    iniciarDataTable('#tabelaPerfis');

    $('.btn-excluir').click(function () {
        $('#modalConfirmacao').modal('show');
    });
  

});

function iniciarDataTable(idTabela) {
    if ($.fn.DataTable.isDataTable(idTabela)) {
      // Se já estiver inicializado, não faz nada (ou poderia destruir e recriar se quiser)
      return;
    }
  
    $(idTabela).DataTable({
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
      }
    });
  }
  
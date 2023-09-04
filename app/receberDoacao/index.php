<?php
    include_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/ame_public_v2/imports/imports.php');
    session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-image:linear-gradient( rgba(255,255,255,.95) 0%,rgba(255,255,255,.7) 500%), url(./imagens/ame_bg.jpg);
            background-attachment: fixed;
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }

        .content {
            margin: 0 auto;
            margin-top: 15px;
            width: 30%;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.04);
            border-radius: 5px;
        }

        #btnCadastro {
            width: 100%;
            margin-top: 10px;
        }

        #refLogin {
            margin: 10px 150px;
        }

    </style>

    <body>

        <section class="content">

            <h1>Você também pode ser ajudado!</h1>
            <p>
                Você pode usar esta funcionalidade para encontrar um dos nosso colaboradores mais próximos de sua casa!
                Conte-nos sobre você, cadastre-se e receba doações.
            </p>

        </section>

        <div class="divider"> </div>

        <section class="content">

            <form class="ui form">
                
                <div id="idEstadoDiv" class="field">
                    <label>Estado</label>
                    <select id="idEstado" class="ui fluid dropdown">
                        <option value="0">Selecione seu estado</option>
                    </select>
                </div>
                
                <div class="two fields">
                    
                    <div id="idCidadeDiv" class="field">
                        <label>Cidade</label>
                        <select id="idCidade" class="ui fluid dropdown">
                            <option value="0">Selecione sua cidade</option>
                        </select>
                    </div>

                    <div id="idBairroDiv" class="field">
                        <label>Bairro</label>
                        <select id="idBairro" class="ui fluid dropdown">
                            <option value="0">Selecione seu bairro</option>
                        </select>
                    </div>
                    
                </div>

                <div id="idEmpresaDiv" class="field">
                    <label>Empresa</label>
                    <select id="idEmpresa" class="ui fluid dropdown">
                        <option value="0">Selecione a empresa</option>
                    </select>
                </div>

            </form>

        </section>

        <div class="divider"> </div>

        <section class="content">

            <div class="ui form">
                <div class="field">
                    <b>Sobre Você</b>
                    <textarea id="dsMotivoDoacao"></textarea>
                </div>
            </div>

            <div id="loader" class="ui segment">
                <p></p>
                <div class="ui active dimmer">
                    <div class="ui loader">
                    </div>
                </div>
            </div>
            
            <button id="btnCadastro" class="positive ui button">Cadastrar</button>

            <div id="alertaVermelho" class="ui negative message">
                <i id="fechaAlertaVermelho" class="close icon" onclick="$('#alertaVermelho').hide();"></i>
                <div class="header">
                    Há algo de errado!
                </div>
                <p>Seu cadastro não foi efetuado.</p>
            </div>

            <div id="alertaVerde" class="ui positive message">
                <i id="fechaAlertaVerde" class="close icon" onclick="$('#alertaVerde').hide();"></i>
                <div class="header">
                    Tudo ok!
                </div>
                <p>Seu cadastro foi efetuado!</p>
            </div>

        </section>
        
    </body>

    <script>

        if(getUrlVar("idUsuario"))
            var idUsuario = window.atob(getUrlVar("idUsuario"));

        $(document).ready(function() {
            init();
        });

        function init() {

            $('#loader').hide();
            $('#alertaVerde').hide();
            $('#alertaVermelho').hide();

            carregaEstados();

            //Change Estado
            $('#idEstado').change(function() {

                $('#idCidade').removeClass("disabled");

                if($('#idCidade').val() != 0 && 
                   $('#idBairro').val() != 0 &&
                   $('#idEmpresa').val() != 0) {

                    $('#idCidade').closest('.dropdown').dropdown('set selected', "0");
                    $('#idBairro').closest('.dropdown').dropdown('set selected', "0");
                    $('#idEmpresa').closest('.dropdown').dropdown('set selected', "0");

                    $("#idCidade > option").each(function() {

                        if($(this).val() != 0)
                            $("#idCidade option[value='"+$(this).val()+"']").remove();
                    
                    });

                    $("#idBairro > option").each(function() {

                        if($(this).val() != 0)
                            $("#idBairro option[value='"+$(this).val()+"']").remove();

                    });

                    $("#idEmpresa > option").each(function() {

                        if($(this).val() != 0)
                            $("#idEmpresa option[value='"+$(this).val()+"']").remove();

                    });

                    $('#idBairro').parent().addClass("disabled");
                    $('#idEmpresa').parent().addClass("disabled");

                    let value = $(this).val();
                    carregaCidades(value);
                   
                
                } else {

                    let value = $(this).val();
                    carregaCidades(value);

                }

            });

            //Change Cidade
            $('#idCidade').change(function() {

                $('#idBairro').removeClass("disabled");

                if($(this).val() > 0) {

                    if($('#idBairro').val() != 0 &&
                       $('#idEmpresa').val() != 0)  {

                        $('#idBairro').closest('.dropdown').dropdown('set selected', "0");
                        $('#idEmpresa').closest('.dropdown').dropdown('set selected', "0");

                        $("#idBairro > option").each(function() {

                            if($(this).val() != 0)
                                $("#idBairro option[value='"+$(this).val()+"']").remove();

                        });

                        $("#idEmpresa > option").each(function() {

                            if($(this).val() != 0)
                                $("#idEmpresa option[value='"+$(this).val()+"']").remove();

                        });

                        $('#idEmpresa').parent().addClass("disabled");

                        let value = $(this).val();
                        carregaBairros(value);
                    
                    } else {

                        $('#idBairro').removeClass("disabled");

                        let value = $(this).val();
                        carregaBairros(value);

                    }

                }

            });

            //Change Bairro
            $('#idBairro').change(function() {

                // $('#idEmpresa').removeClass("disabled");

                console.log("change bairro");
                console.log($('#idEmpresa').val());

                if($(this).val() > 0) {

                    if($('#idEmpresa').val() == 0) {

                        $('#idEmpresa').closest('.dropdown').dropdown('set selected', "0");

                        $("#idEmpresa > option").each(function() {

                            if($(this).val() != 0)
                                $("#idEmpresa option[value='"+$(this).val()+"']").remove();

                        });

                        let idEstado = $('#idEstado').val();
                        let idCidade = $('#idCidade').val();
                        let idBairro = $('#idBairro').val();

                        console.log("idEstado ", $('#idEstado').val());
                        console.log("idBairro ", $('#idCidade').val());
                        console.log("idCidade ", $('#idBairro').val());

                        console.log("Aqui");
                        console.log("tem a classe: ", $('#idEmpresa').parent().hasClass("disabled"));

                        if($('#idEmpresa').parent().hasClass("disabled")) {
                            console.log("remove");
                            $('#idEmpresa').parent().removeClass("disabled");
                        } else {
                            $('#idEmpresa').parent().addClass("disabled");
                        }

                        carregaEmpresas(idEstado, idCidade, idBairro);

                    } 

                } else {

                    let idEstado = $('#idEstado').val();
                    let idCidade = $('#idCidade').val();
                    let idBairro = $('#idBairro').val();

                    $('#idEmpresa').removeClass("disabled");

                    carregaEmpresas(idEstado, idCidade, idBairro);
                
                }

            });

            $('#idCidade').addClass("disabled");
            $('#idBairro').addClass("disabled");
            $('#idEmpresa').addClass("disabled");
        
        }

        function getUrlVar(variavel) {
            
            let query = window.location.search.substring(1);
            let variaveis = query.split("&");
            let valor;
            
            for (let i = 0; i < variaveis.length; i++) {

                valor = variaveis[i].split("=");
                
                if (valor[0] == variavel) 
                    return valor[1];
            
            }
            
            return false;
        
        }

        $("#btnCadastro").click(function(event) {

            event.preventDefault();

            $('#loader').show();
            $('#btnCadastro').hide();

            let data = new FormData();

            data.append("idEmpresa", $("#idEmpresa").val());
            data.append("dsMotivoDoacao", $("#dsMotivoDoacao").val());
            data.append("idUsuario", idUsuario);

            $.ajax({
                url: "./control/receberDoacao.php",
                type: "POST",
                dataType: "json",
                data: data,
                processData: false,
                contentType: false
            }).done(function(result) {

                setTimeout(function() {

                    $('#loader').hide();
                    $('#btnCadastro').show();

                    if(result.STATUS) {
                        $('#alertaVerde').show();
                        location.href = "../../home/index.php?idUsuario=" + window.btoa(idUsuario);
                    } else {
                        $('#alertaVermelho').show();
                    }

                }, 1000);

            }).fail(function(jqXHR, textStatus ) {
                console.log("Request failed: " + textStatus);
            });

        });

        function carregaEstados() {

            $.ajax({
                url: "./control/carregaEstados.php",
                type: "POST",
                dataType: "json",
                processData: false,
                contentType: false
            }).done(function(result) {

                if(result.STATUS) {

                    let estados = result.RESULT;

                    estados.forEach((estado, indice) => {

                        $('#idEstado').append($("<option>", {
                            value: estado.idEstado,
                            text: estado.nmEstado
                        }));

                    });

                    $('#idEstado').dropdown('refersh');

                }

            }).fail(function(jqXHR, textStatus ) {
                console.log("Request failed: " + textStatus);
            });

        }

        function carregaCidades(idEstado) {

            console.log("idEstado", idEstado);

            let data = new FormData();

            data.append("idEstado", idEstado);

            $.ajax({
                url: "./control/carregaCidades.php",
                type: "POST",
                dataType: "json",
                data: data,
                processData: false,
                contentType: false
            }).done(function(result) {

                if(result.STATUS) {

                    let cidades = result.RESULT;

                    cidades.forEach((cidade, indice) => {

                        $('#idCidade').append($("<option>", {
                            value: cidade.idCidade,
                            text: cidade.nmCidade
                        }));

                    });

                    $('#idCidade').dropdown('refersh');

                }

            }).fail(function(jqXHR, textStatus ) {
                console.log("Request failed: " + textStatus);
            });

        }

        function carregaBairros(idCidade) {

            console.log("idCidade")

            let data = new FormData();

            data.append("idCidade", idCidade);

            $.ajax({
                url: "./control/carregaBairros.php",
                type: "POST",
                dataType: "json",
                data: data,
                processData: false,
                contentType: false
            }).done(function(result) {

                if(result.STATUS) {

                    let bairros = result.RESULT;

                    bairros.forEach((bairro, indice) => {

                        $('#idBairro').append($("<option>", {
                            value: bairro.idBairro,
                            text: bairro.nmBairro
                        }));

                    });

                    $('#idBairro').dropdown('refersh');

                }

            }).fail(function(jqXHR, textStatus ) {
                console.log("Request failed: " + textStatus);
            });

        }

        function carregaEmpresas(idEstado, idCidade, idBairro) {

            let data = new FormData();

            data.append("idEstado", idEstado);
            data.append("idCidade", idCidade);
            data.append("idBairro", idBairro);

            $.ajax({
                url: "./control/carregaEmpresas.php",
                type: "POST",
                dataType: "json",
                data: data,
                processData: false,
                contentType: false
            }).done(function(result) {

                if(result.STATUS) {

                    let empresas = result.RESULT;

                    empresas.forEach((empresa, indice) => {

                        $('#idEmpresa').append($("<option>", {
                            value: empresa.idEmpresa,
                            text: empresa.nmEmpresa
                        }));

                    });

                    $('#idEmpresa').dropdown('refersh');

                }

            }).fail(function(jqXHR, textStatus ) {
                console.log("Request failed: " + textStatus);
            });

        }

        

    </script>

</html>

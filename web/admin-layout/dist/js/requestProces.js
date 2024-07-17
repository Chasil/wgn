$('body').on('click','.request-info',function(e){
        e.preventDefault();
        var request = $(this).closest('tr').data('request');
          if(request['abo-type']==="firma"){
              $('#modal-request-info').find('[data-request-type="company"]').removeClass('hidden');
              $('#modal-request-info').find('[data-request-type="person"]').addClass('hidden');
          }
          $('#modal-request-info').find('#state-desc').removeClass('red');
          $('#modal-request-info').find('#state-desc').removeClass('green');

          if(request['case-status']==='r16' || request['case-status']==='r17' || request['case-status']==='r18'){
              console.log(request['case-status']);
              $('#modal-request-info').find('#state-desc').addClass('red');
          }else if(request['case-status']==='r03' || request['case-status']==='r06' || request['case-status']==='r12' || request['case-status']==='r13'){
              $('#modal-request-info').find('#state-desc').addClass('green');
          }

          $('#modal-request-info').find('#state-desc').html(getStateDescription(request['case-status']));
          $('#modal-request-info').find('#state-info').html(getStateInfo(request));
          for(var id in request){

              $('#modal-request-info').find('#'+id).html(translateValue(id,request[id]));
          }

        $('#modal-request-info').modal();
    });

    $('#modal-date-range-custom,#modal-request-E06,#modal-request-E12,#modal-request-E13').on('shown.bs.modal', function (e) {
            $('.datepicker').datepicker({language:'pl',format: 'yyyy-mm-dd'});
    });
        $('#status-change').on('change', function (e) {
            $(this).closest('form').submit();
    });
    $('.send-request').on('click', function (e) {
        e.preventDefault();
        $('#modal-request-' + $(this).data('request')).find('.btn-success').attr('data-request-data',setData($(this)));
        $('#modal-request-' + $(this).data('request')).modal();
    });
    $('.modal').on('click','.btn-success',function(e){
        e.preventDefault();
        var data = updateData($(this));

        if(validate(data)){
            sendRequest(data);
        }
    });
    function validate(data){
        switch(data.msg_type){
            case 'E12':
                if(data.casependingactivationdate===''){
                    $('#modal-request-E12').find('input[name="casependingactivationdate"]').closest('.form-group').addClass('has-error');
                    return false;
                }else {
                    $('#modal-request-E12').find('input[name="casependingactivationdate"]').closest('.form-group').removeClass('has-error');
                    return true;
                }
            break;
            case 'E13':
                if(data.portingdate===''){
                    $('#modal-request-E13').find('input[name="portingdate"]').closest('.form-group').addClass('has-error');
                    return false;
                }else {
                    $('#modal-request-E13').find('input[name="portingdate"]').closest('.form-group').removeClass('has-error');
                    return true;
                }            break;
            case 'E06':
                if(data.caseterminationdate===''){
                    $('#modal-request-E12').find('input[name="casependingactivationdate"]').closest('.form-group').addClass('has-error');
                    return false;
                }else {
                    $('#modal-request-E12').find('input[name="casependingactivationdate"]').closest('.form-group').removeClass('has-error');
                    return true;
                }
            break;
            case 'E17':
                if(data.reason===''){
                    $('#modal-request-E17').find('select[name="reason"]').closest('.form-group').addClass('has-error');
                    return false;
                }else {
                    $('#modal-request-E17').find('select[name="reason"]').closest('.form-group').removeClass('has-error');
                    return true;
                }
            break;
            case 'E18':
                if(data.reason===''){
                    $('#modal-request-E18').find('select[name="reason"]').closest('.form-group').addClass('has-error');
                    return false;
                }else {
                    $('#modal-request-E18').find('select[name="reason"]').closest('.form-group').removeClass('has-error');
                    return true;
                }
            break;
        }

        return false;
    }
    function sendRequest(requestData){

        $.ajax({
            url: appSendRequestUrl,
            data: requestData
          }).done(function(resonse) {

            if(resonse.data.status ==='error'){
                $('#request-response-container').html('<div class="alert alert-danger alert-dismissible" role="alert">'
                  + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                  resonse.data.reason
                  +  '</div>');
            }else{
                $('#request-response-container').html('<div class="alert alert-success alert-dismissible" role="alert">'
                  + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                  + 'Komunikat został wysłany</div>');

                location.reload();
            }

            $('#modal-request-' + requestData.msg_type).modal('hide');

          });
    }
    function setData(element){
        var requestType = element.data('request');
        var requestData = element.closest('tr').data('request');
        var data = null;

        switch(requestType){
            case 'E12':
                data = {msg_type:'E12',caseid:requestData['case-id'],dirnum:requestData['dirnum'],dirnumend:requestData['dirnum-end'],casependingactivationdate:''};
            break;
            case 'E13':
                data = {msg_type:'E13',caseid:requestData['case-id'],dirnum:requestData['dirnum'],dirnumend:requestData['dirnum-end'],recipient:requestData['recipient'],routingnumber:requestData['routing-number'],portingdate:''};
            break;
            case 'E06':
                data = {msg_type:'E06',caseid:requestData['case-id'],dirnum:requestData['dirnum'],dirnumend:requestData['dirnum-end'],caseterminationdate:'',recipient:requestData['recipient']};
            break;
            case 'E17':
                data = {msg_type:'E17',caseid:requestData['case-id'],dirnum:requestData['dirnum'],dirnumend:requestData['dirnum-end'],recipient:requestData['recipient'],reason:''};
            break;
            case 'E18':
                data = {msg_type:'E18',caseid:requestData['case-id'],dirnum:requestData['dirnum'],dirnumend:requestData['dirnum-end'],donor:requestData['donor'],reason:''};
            break;
        }

        return JSON.stringify(data);
    }
    function updateData(element){
        var data = element.data('request-data');
        switch(data.msg_type){
            case 'E12':
                data.casependingactivationdate = $('#modal-request-E12').find('input[name="casependingactivationdate"]').val();
            break;
            case 'E13':
                data.portingdate = $('#modal-request-E13').find('input[name="portingdate"]').val();
            break;
            case 'E06':
                data.caseterminationdate = $('#modal-request-E06').find('input[name="caseterminationdate"]').val();
            break;
            case 'E17':
                data.reason= $('#modal-request-E17').find('select[name="reason"]').val();
            break;
            case 'E18':
                data.reason = $('#modal-request-E18').find('select[name="reason"]').val();
            break;
        }

        return data;
    }
    function translateValue(key,value){
        var translate = [];
        translate['abo-type'] = {osoba: 'Osoba Prywatna',firma:'Firma'};
        translate['abo-identifier-type'] = {PES: 'PESEL',NIP:'NIP',REG:'REGON',KRS:'KRS'};
        if( translate[key] === undefined ) {

            return value;
        }
        if(translate[key][value]===undefined){
            return value;
        }
        return translate[key][value];
    }
    function getStateDescription(state){
        var desc = null;

        switch(state){
            case 'r03':
                desc = 'Żądanie przeniesienia Numeru';
            break;
            case 'r06':
                desc = 'Zgoda na przekazanie numeru';
            break;
            case 'r12':
                desc = 'Zaakceptowano datę przeniesienia';
            break;
            case 'r13':
                desc = 'Numer został przeniesiony';
            break;
            case 'r16':
                desc = 'Odrzucenie wniosku przez PLICBD';
            break;
            case 'r17':
                desc = 'Odrzucenie wniosku przez Dawcę';
            break;
            case 'r18':
                desc = 'Anulowanie wniosku.';
            break;
            case 's03':
                desc = 'Oczekujemy na odpowiedź Dawcy';
            break;
            case 's06':
                desc = 'Oczekujemy na odpowiedź Biorcy';
            break;
            case 's12':
                desc = 'Oczkujemy na odpowiedź Dawcy';
            break;
            case 's13':
                desc = 'Numer został przeniesiony';
            break;
            case 's17':
                desc = 'Anulowano wniosek';
            break;
            case 's18':
                desc = 'Anulowano wniosek';
            break;
        }
        return desc;
    }
    function getStateInfo(request){
        var info = null;

        switch(request['case-status']){
            case 'r03':
                info = request['case-pending-activation-date'];
            break;
            case 'r06':
               info = request['case-pending-activation-date'];
            break;
            case 's13':
               info = request['case-pending-activation-date'];
            break;
            case 'r17':
                info = getReason(request['reason']);
            break;
            case 'r18':
                info = getReason(request['reason']);
            break;
            default:
                info = '<span>-------------------------------</span>';
            break;
        }
        return info;
    }
    function getReason(id){

        var info = null;

        switch(id){
            case '1':
                info = 'E03: Niezgodne dane rejestracyjne';
            break;
            case '2':
               info = 'E03: Numer Katalogowy nieaktywny';
            break;

            case '3':
                info = 'Abonent zrezygnował z usługi przeniesienia Numeru Katalogowego';
            break;
            case '4':
                info = 'E03: Niewłaściwy typ kontraktu';
            break;
            case '5':
                info = 'E03: Błędne wskazanie grupy Numerów';
            break;
            case '6':
                info = 'E03: Data rozwiązania umowy w trybie DAY wypada później niż data rozwiązania umowy w trybie END';
            break;
            case '7':
                info = 'E03: Numer nie należy do Dawcy (identyfikator błędu na okres przejściowy – do czasu włączenia weryfikacji przez System PLI CBD)';
            break;
            case '8':
                info = 'E03: Wyznaczona Umowna Data realizacji Przeniesienia Numeru przekracza';
            break;
            case '20':
                info = 'Abonent zrezygnował z usługi przeniesienia Numeru Katalogowego';
            break;
            case '21':
                info = 'Niezrealizowane zamówienie na Usługę Hurtową';
            break;
            case '22':
                info = 'Powiązane zamówienie na Usługę Hurtową zostało anulowane / niezrealizowane';
            break;
            case '23':
                info = 'Pomyłka w danych rejestracyjnych';
            break;
            case '24':
                info = 'Anulowanie Sprawy NP w związku z błędami w systemach';
            break;
        }
        return info;
    }



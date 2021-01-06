function  proccessPayment(token)
{
    let data = {
        card_token: token,
        hash: PagSeguroDirectPayment.getSenderHash(),
        installment: document.querySelector('select.select-installments').value,
        card_name: document.querySelector('input[name=card_name]').value,
        _token: csrf_token
    };
    $.ajax({
        type: 'POST',
        url:  urlProcess,
        data: data,
        dataType: 'json',
        success: function (res) {
            toastr.success(res.data.message, 'Success');
            window.location.href = `${urlThanks}?order=${res.data.order}`;
        }

    });
}

function getInstallments(amount,brand) {
    PagSeguroDirectPayment.getInstallments({
        amount:amount,
        brand:brand,
        maxInstallmentsNoInterest: 0,
        success: function(res) {
            let selectInstallments = drawSelectInstallments(res.installments['visa']);
            document.querySelector('div.installments').innerHTML = selectInstallments;

        },
        error: function(err) {

        },
        complete: function(res) {

        }

    });

}

function drawSelectInstallments(installments) {
    let select = '<label>Opções de Parcelamento:</label>';

    select += '<select class="form-control select-installments">';

    for(let l of installments) {
        select += `<option value="${l.quantity}|${l.installmentAmount}">${l.quantity}x de ${l.installmentAmount} - Total fica ${l.totalAmount}</option>`;
    }


    select += '</select>';

    return select;
}

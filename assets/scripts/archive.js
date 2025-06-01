jQuery(document).ready(function ($) {

    ////////////////////////////// ticket filtering  //////////////////////////////
    let isFiltering = false;
    
    let fadeLoadOnSuccess = function (res) {
        $('.tickets-list-inner').fadeOut(200, function () {
            res.data[0] ? $(this).html(res.data[0]).fadeIn(200) : $(this).html('<h3 class="no-result">نتیجه ای یافت نشد!</h3>').fadeIn(200);
        }); 
    }

    function translate_nums(expr) {

        let farsiDifigts = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        return expr.toString().replace(/\d/g, digit => farsiDifigts[digit]);
        
    }
    
    $('#tickets-filters-form').on('submit', function (event) {

        event.preventDefault();
            
        if(isFiltering) return false;
        isFiltering = true

        let data = $(this).serialize()
        
        let accountId = $(this).closest('.ctm-section-container').data('account');
        data += "&account=" + accountId;

        $.ajax({
            url: archive_data.ajax_url,
            type: "GET",
            data: data,
            beforeSend: () => $('<div class="loader"><div></div></div>').insertAfter('.tickets-list-inner'),
            success: (res) => fadeLoadOnSuccess(res),
            error: (xhr, status, error) => Swal.fire( { title: "خطا", titleText: "خطایی رخ داد!", text: "لطفا مجددا تلاش فرمایید", icon: "error" } ),
            complete: function () {
                $('.loader').remove();
                setTimeout(() => isFiltering = false , 500);
            }
        })
    });




    ////////////////////////////// pagination  //////////////////////////////
    function showNewPagination(res, page, max) {
        $('#ctm-pagination').children('.page-num').html(page)
        $('.tickets-total-count span:first-child').html(translate_nums(page * 10 - 10))
        $('.tickets-total-count span:last-child').html(translate_nums(page * 10))

        if(max > 10) {
            $('.first-pages').html(`
                <div data-id="${+page - 1}" class="ctm-page-${+page - 1}">${translate_nums(+page - 1)}</div>
                <div data-id="${+page}" class="ctm-page-${+page} active">${translate_nums(+page)}</div>
                <div data-id="${+page + 1}" class="ctm-page-${+page + 1}">${translate_nums(+page + 1)}</div>
            `);
            if(page < max - 3) {
                $('.pagination-dots').css('display', 'block');
                $('.last-pages').css('display', 'flex');
            } else if (page >= max - 3) {
                $('.pagination-dots').css('display', 'none');
                $('.last-pages').css('display', 'none');
            }
        } else {
            $('[class^=ctm-page].active').removeClass('active');
            $(`.ctm-page-${page}`).addClass('active');
        }

        fadeLoadOnSuccess(res);   
    }

    $('#ctm-pagination').children().each(function () {
        $(this).on('click', function (event) {
            
            let maxPage = $('#ctm-pagination [class^=ctm-page]').last().data('id')            

            pageNum = $('#ctm-pagination').children('.page-num').html()
            newPage = $(this).hasClass('prev-page') ? --pageNum : $(this).hasClass('next-page') ? ++pageNum : event.target.dataset.id

            if(newPage > maxPage || newPage < 1) {
                Swal.fire({title: 'خطا', titleText: "صفحه درخواستی موجود نمیباشد", text: 'لطفا از میان صفحه هایه موجود انتخاب فرمایید', icon: "error"});
                return;
            }

            let sort = $('#tickets-filters-form .by_date-filter').val();

            data = {
                page: newPage,
                action: 'page_tickets',
                nonce: archive_data.tickets_pagination_nonce,
                account: $(this).closest('.ctm-section-container').data('account'),
                sort: sort
            }

            $.ajax({
                url: archive_data.ajax_url,
                method: "GET",
                data: data,
                beforeSend: () => $('<div class="loader"><div></div></div>').insertAfter('.tickets-list-inner'),
                success: (res) => res.success ? showNewPagination(res, newPage, maxPage) : Swal.fire( { title: "خطا", titleText: "خطایی رخ داد!", text: "لطفا مجددا تلاش فرمایید", icon: "error" } ),
                error: ()=> Swal.fire( { title: "خطا", titleText: "خطایی رخ داد!", text: "لطفا مجددا تلاش فرمایید", icon: "error" } ),
                complete: () => $('.loader').remove()
            });
        })
    })
})





////////////////////////////// account selection  //////////////////////////////
window.addEventListener('load', ()=> {
    if(window.location.href.includes('select_account=1')) {
        let accounts = JSON.parse(sessionStorage.getItem('accounts_list'));
        
        if(accounts.length === 0) {
            Swal.fire({title: 'error', titleText: 'خطایی رخ داد! لطفا مجدد تلاش کنید', icon: 'error'});
            return;
        }

        let popup = document.createElement("div");
        popup.classList.add('accounts-list-container');
        popup.innerHTML = `
            <div class="account-list-content">
                <div class="account-list-title">شماره وارد شده, با حساب چند مشتری در ارتباط است. لطفا یکی را انتخاب کنید</div>
                <div class="accounts-list">
                    ${accounts.map(acc => `<div role="button" onclick="selectAccount('${acc.id}')">${acc.name}</div>`).join("")}
                </div>
            </div> 
        `;

        document.body.appendChild(popup)

        setTimeout(() => popup.style.opacity = '1' ,200);
    }
})

function selectAccount(id) {
    window.location.href= "?selected_account=" + id;
}
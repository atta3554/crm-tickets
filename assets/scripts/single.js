jQuery(document).ready( function ($) {

    ////////////////////////////// ticket reading  //////////////////////////////
    if($('#ctm-ticket-container').data('read') === false) {
        $.ajax({
            url: single_data.ajax_url,
            type: "GET",
            data: { action: 'ctm_ticket_read', ticket_id: $('#ticket-id').val(), nonce: single_data.read_ticket_nonce},
            success: (res) => res.success ? console.log(res.data.message) : console.log(res.data.message),
            error: (xhr, status, error) => console.log(xhr, status, error),
        })
    }


    

    ////////////////////////////// ticket rating  //////////////////////////////
    let isRating = false;

    $('#ctm-ticket-rating').on('submit', function (event) {
        event.preventDefault();        

        let ticketRate = $('#ctm-ticket-rating input[name=rating]:checked').val();
        if(!ticketRate) return false;

        if(isRating) return false;
        isRating = true;

        let data = $(this).serialize();

        $.ajax({
            url: single_data.ajax_url,
            type: "GET",
            data: data,
            beforeSend: () => $('<div class="loader"><div></div></div>').insertAfter('#ctm-ticket-rating'),
            success: (res) => {
                console.log(res);
                if(res.success && res.data.message === 'success') {
                    Swal.fire( { title: "موفق", titleText: "امتیاز شما با موفقیت ثبت شد", text: "ممنون از ثبن امتیاز", icon: "success" } )
                }
            },
            error: (xhr, status, error) => Swal.fire( { title: "خطا", titleText: "خطایی رخ داد!", text: "لطفا مجددا تلاش فرمایید", icon: "error" } ),
            complete: function () {
                $('.loader').remove();
                setTimeout(() => isRating = false , 2000);
            }
        })
        
    });
        



    ////////////////////////////// toggle sidebar  //////////////////////////////
    $('.toggle-sidebar').on('click', function () {
        $('.ctm-ticket-container').toggleClass('hide-sidebar');
    });



    
    ////////////////////////////// reply section  //////////////////////////////
    $('.reply-toggle-btn').on('click', function () {
        $('.ticket-content-inner > form').slideToggle("slow", function() {
        });
    }) 




    ////////////////////////////// user files handling  //////////////////////////////

    /**** voice recorder ****/
    let mediaRecorder;
    let audioChunks = [];
    let isRecording = false;
    let audioInput = document.querySelector('#audio-input')

    function validateUploadedFile(file) {
        // const allowedTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/webm', 'video/webm', 'image/png', 'image/jpg', 'image/jpeg'];
        const allowedTypes = ['audio/webm;codecs=opus', 'audio/webm','audio/mp4', 'image/png', 'image/jpg', 'image/jpeg', 'application/zip', 'application/x-compressed', 'application/x-zip-compressed', 'application/vnd.rar'];
        const maxFileSize = 1024 * 1024 * 5;
        
        if(!allowedTypes.includes(file.type)) {
            swal.fire({
                title: 'failed',
                titleText: 'خطا',
                text: 'فایل ارسالی جزو فایل هایه مجاز نمیباشد',
                icon: 'error'
            })
            return false;
        } 
        
        if(file.size > maxFileSize) {
            swal.fire({
                title: 'failed',
                titleText: 'خطا',
                text: 'حجم فایل آپلود شده بالاتر از حد مجاز است',
                icon: 'error'
            })
            return false;
        }

        displayUploadedFile(file);
        setTimeout(() => {
            swal.fire({
                title: 'success',
                titleText: 'آپلود موفقیت آمیز',
                icon: 'success'
            });
        }, 500);
        return true;
    }

    function displayUploadedFile(file) {
        
        let fileURL = URL.createObjectURL(file);
        let audioElement = '';
        
        let fileType = file.type.includes('audio') ? 'audio' : file.type.includes('image') ? 'file' : file.type.includes('compressed') || file.type.includes('zip') || file.type.includes('rar') ? 'archive' : null;

        let dataTransfer = new DataTransfer();
        dataTransfer.items.add(file)

        if(fileType === 'audio') {
            audioElement =`
            <div class="uploaded-audio">
                <audio controls src="${fileURL}"></audio>
                <button>
                    <svg xmlns="http://www.w3.org/2000/svg" data-name="Livello 1" id="Livello_1" viewBox="0 0 151.57 151.57"><title/><circle cx="1038.5" cy="467.01" r="72.28" style="fill:#4e4e4e;stroke:#f2f2f2;stroke-linecap:round;stroke-linejoin:round;stroke-width:7px" transform="translate(-988.78 479.89) rotate(-45)"/><line style="fill:#da2244;stroke:#f2f2f2;stroke-linecap:round;stroke-linejoin:round;stroke-width:7px" x1="47.57" x2="103.99" y1="103.99" y2="47.57"/><line style="fill:#da2244;stroke:#f2f2f2;stroke-linecap:round;stroke-linejoin:round;stroke-width:7px" x1="45.8" x2="105.7" y1="45.87" y2="105.77"/></svg>
                </button>
            </div>
            `;
            $('.user-recorded-voice').html(audioElement).hide().fadeIn(500);
            audioInput.files = dataTransfer.files
        } else if(fileType === 'file' || fileType === 'archive') {
            
            if(fileType === 'archive') fileURL = single_data.assets_url + 'images/archive.jpg';

            audioElement = `
            <div class="uploaded-${fileType}">
                <div class="file-thumb"><img src="${fileURL}"></img></div>
                <div class="file-metadata">
                    <p class="file-name">${file.name}</p>
                    <p class="file-size">${(file.size / 1024).toFixed(2)} KB</p>
                </div>
                <button>
                    <svg xmlns="http://www.w3.org/2000/svg" data-name="Livello 1" id="Livello_1" viewBox="0 0 151.57 151.57"><title/><circle cx="1038.5" cy="467.01" r="72.28" style="fill:#4e4e4e;stroke:#f2f2f2;stroke-linecap:round;stroke-linejoin:round;stroke-width:7px" transform="translate(-988.78 479.89) rotate(-45)"/><line style="fill:#da2244;stroke:#f2f2f2;stroke-linecap:round;stroke-linejoin:round;stroke-width:7px" x1="47.57" x2="103.99" y1="103.99" y2="47.57"/><line style="fill:#da2244;stroke:#f2f2f2;stroke-linecap:round;stroke-linejoin:round;stroke-width:7px" x1="45.8" x2="105.7" y1="45.87" y2="105.77"/></svg>
                </button>
            </div>
            `;
            $('.reply-attachments-inner').addClass('is-uploaded');
            $('.reply-attachments-inner .uploaded-file-container').html(audioElement).hide().fadeIn(500);
            fileInput.files = dataTransfer.files
        }

        $('.loader').remove();

        $(`.uploaded-${fileType} button`).on('click', function (event) {
            event.preventDefault();
            fileType === 'audio' ? audioInput.value = '' : fileType === 'file' ? fileInput.value = '' : null
            
            $(this).closest(`.uploaded-${fileType}`).fadeOut(500, function () {
                $(this).closest(`.reply-attachments-inner`)?.removeClass('is-uploaded')
                $(this).remove();
            })
        })
    }

    function timer() {
        return setInterval(() => {
            let firstSec = Number($('.recorder-timer .second').text().slice(1,2));
            let secondSec = Number($('.recorder-timer .second').text().slice(0,1));
            let firstMin = Number($('.recorder-timer .minute').text().slice(1,2));
            let secondMin = Number($('.recorder-timer .minute').text().slice(0,1));
            let firstHour = Number($('.recorder-timer .hour').text().slice(1,2));
            let secondHour = Number($('.recorder-timer .hour').text().slice(0,1));
            firstSec++;
            if(firstSec >= 10) {
                firstSec = 0;
                secondSec++;
            }
            if(secondSec >= 6) {
                secondSec = 0;
                firstMin++;
            }
            if(firstMin >= 10) {
                firstMin = 0;
                secondMin++;
            }
            if(secondMin >= 6) {
                secondMin = 0;
                firstHour++;
            }
            if(firstHour > 10) {
                firstHour = 0;
                secondHour++;
            }
            $('.recorder-timer .second').html(`${secondSec}${firstSec}`);
            $('.recorder-timer .minute').html(`${secondMin}${firstMin}`);
            $('.recorder-timer .hour').html(`${secondHour}${firstHour}`);
        }, 1000);
    }

    $('#start-recording').on('click', async function (event) {
        
        event.preventDefault();

        if(isRecording) return false;
        isRecording = true;

        $('.recorder-timer .second').html('00');
        $('.recorder-timer .minute').html('00');
        $('.recorder-timer .hour').html('00');
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            
            let startTimer = timer();

            mediaRecorder = new MediaRecorder(stream, {mimeType: 'audio/webm;codecs=opus'});
            mediaRecorder.start();

            audioChunks = [];

            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };

            mediaRecorder.onstop = function () {
                clearInterval(startTimer);
                $('<div class="loader"><div></div></div>').insertAfter('.user-recorded-voice');
                let audioBlob = new Blob(audioChunks, { type: mediaRecorder.mimeType});
                let audioFile = new File([audioBlob], 'audio_file.webm', {type: audioBlob.type})
                
                validateUploadedFile(audioFile);
            
                isRecording = false;
            }

            $('#start-recording').prop('disabled', true);
            $('#stop-recording').prop('disabled', false);

        } catch (error) {
            Swal.fire({
                title: 'failed',
                titleText: 'خطا',
                text: 'ضبط صدا با خطا مواجه شد! لطفا مجدد امتحان کنید',
                icon: 'error'
            });
        }
    });

    $('#stop-recording').on('click', function () {
        mediaRecorder.stop();

        $('#start-recording').prop('disabled', false);
        $('#stop-recording').prop('disabled', true);
    })


    /**** drag and drop upload files ****/
    let dropArea = document.querySelector('.reply-attachments-inner');
    let fileInput = document.querySelector('#file-input');
    if(dropArea) {

        let dragIn = ['dragenter', 'dragover'];
        let dragOut = ['dragleave', 'drop'];

        dragIn.forEach(event=> {
            dropArea.addEventListener(event, e=> {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.add('is-dragging')
            }, false)
        })

        dragOut.forEach(event=> {
            dropArea.addEventListener(event, e=> {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.remove('is-dragging')
            }, false)
        })

        dropArea.addEventListener('drop', event => validateUploadedFile(event.dataTransfer.files[0]));

        $('#browse-btn').on('click', ()=> fileInput.click());

        fileInput.addEventListener('change', event => validateUploadedFile(event.target.files[0]));
    }




    ////////////////////////////// send new message  //////////////////////////////
    function translate_nums(expr) {

        let farsiDifigts = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        return expr.toString().replace(/\d/g, digit => farsiDifigts[digit]);
        
    }

    $('#new-ticket').on('submit', function (event) {
        
        event.preventDefault();

        if(isRecording) {
            Swal.fire({title: 'خطا', titleText: 'در حال ضبط صدا', text: 'لطفا ابتدا ضبط خود را به پایان برسانید', icon: 'error'});
            return false;
        }

        let formData = new FormData(this);

        let importance = formData.get('ticket-importance');
        if(importance !== null && importance === '0') {
            Swal.fire({title: 'خطا', titleText: 'خطا!', text: 'لطفا اولویت تیکت خود را انتخاب کنید', icon: 'error'});
            return;
        }

        if(formData.get('file-input').name && formData.get('audio-input').name){
            Swal.fire({title: 'error', titleText: 'خطا!', text: ' در هر تیکت تنها یک فایل میتوان ارسال کرد. لطفا صدایه ضبط شده یا فایل انتخاب شده را حذف فرمایید', icon: 'error'});
            return;
        }

        $.ajax({
            method: "POST",
            url: single_data.ajax_url,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: () => $('<div class="loader"><div></div></div>').insertAfter('#new-ticket'),
            success: function (res) {
                if(res.success) {
                    if(res.data.message === 'success') Swal.fire({title: 'success', titleText: 'تیکت با موفقیت ارسال شد', html: `<a class="view-ticket btn" href="${res.data.link}">مشاهده تیکت</a>`, icon: 'success'})
                    
                    else if(res.data.annotation.hasOwnProperty('annotationid')) {
                        let template = `
                        <div class="dialog-box user" data-id="${res.data.annotation.annotationid}">
                            <div class="dialog-box-inner">
                                <div class="message-box">
                                    <div class="message-box-inner">
                                        <p>${res.data.annotation.notetext}</p>
                        `;

                        if(res.data.annotation.filename) {
                            template += `
                                        <div class="ctm-file-container">
                                            <button>
                                                <div class="file-icon"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><polygon style="fill:#FFE182;" points="360.129,172.138 256,472.276 512,172.138 "/><g><polygon style="fill:#FFCD73;" points="105.931,39.724 0,172.138 151.871,172.138  "/><polygon style="fill:#FFCD73;" points="360.129,172.138 512,172.138 406.069,39.724  "/><polygon style="fill:#FFCD73;" points="360.129,172.138 256,39.724 151.871,172.138  "/></g><polygon style="fill:#FFAA64;" points="256,39.724 105.931,39.724 151.871,172.138 "/><polygon style="fill:#FFE182;" points="406.069,39.724 256,39.724 360.129,172.138 "/><polygon style="fill:#FFAA64;" points="151.871,172.138 256,472.276 360.129,172.138 "/><polygon style="fill:#FF8C5A;" points="0,172.138 256,472.276 151.871,172.138 "/></svg></div>
                                                <div class="file-datas">
                                                    <span class="file-name">${res.data.annotation.filename}</span>
                                                    <span class="download-icon"><svg xmlns="http://www.w3.org/2000/svg" height="44" viewBox="0 0 512 512" width="18" class=""><g><path d="m409.785156 278.5-153.785156 153.785156-153.785156-153.785156 28.285156-28.285156 105.5 105.5v-355.714844h40v355.714844l105.5-105.5zm102.214844 193.5h-512v40h512zm0 0" data-original="#000000" class="active-path" style="fill:#8D99A9" data-old_color="#000000"/></g></svg></span>
                                                </div>
                                            </button>
                                        </div>
                            `;
                        }

                        template +=`
                                    </div>
                                </div>
                                <div class="message-metadata">
                                    <div class="metadata-inner">
                                        <div class="user-image" style="background-image: url(${res.data.annotation.user_image});"></div>
                                        <span class="owner-name">${res.data.annotation.owner_name} - ${translate_nums(res.data.annotation.jalali_created)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;

                        $('.ticket-messages-inner').append($(template));
                        $('.dialog-box:last-child').hide();
                        $('.dialog-box:last-child').slideDown('slow');

                        if(formData.get('status') === 'closed') {
                            $('#new-ticket .send-btn').attr('disabled', true);
                        }
                    }
                }
            },
            error: (err)=> console.log(err),
            complete: ()=> $('.loader').remove()
        });
        
    })

    $('#ticket-importance').on('change', function () {
        if($(this).closest('.ctm-ticket-importance').data('count') >= 3 && $(this).val() === '1') {
            Swal.fire({title: 'error', titleText: 'خطا', text: 'در هر ماه تنها ۳ تیکت با اولویت بالا میتوان ارسال کرد', icon: 'error'});
            $(this).children()[0].removeAttribute('selected')
            $(this).children()[0].setAttribute('selected', true);
            
            return;
        }
    })


    

    ////////////////////////////// download dialogs files  //////////////////////////////
    $('.ctm-file-container button').on('click', function () {
        
        $('<div class="loader"><div></div></div>').insertAfter($(this));

        let url = single_data.ajax_url + '/?action=download_file&nonce=' + single_data.file_download_nonce + '&data=' + $(this).closest('.dialog-box').data('id')
        
        let link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('target', '_blank');
        link.setAttribute('download', '');
        link.setAttribute('style', 'display: none');

        document.body.appendChild(link);
        
        link.click();
        link.remove()
        
        setTimeout(() => $('.loader').remove() ,1000);
    })
})